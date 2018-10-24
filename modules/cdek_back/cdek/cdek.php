<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(dirname(__FILE__).'/config.php');

class CDEK extends Module
{
    protected $config;
    protected $errors = array();
    public function __construct()
    {
        $this->name = 'cdek';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.15';
        $this->bootstrap = 1;
        $this->author = 'SeoSA';
        $this->need_instance = '0';
        $this->module_key = 'f3e8bd8c4ba01dd0003a618b8dc92d9b';
        parent::__construct();

        $this->displayName = $this->l('CDEK');
        $this->description = $this->l('Delivery option');

        $this->config = array(
            'account' => '',
            'secure_password' => '',
            'postcode' => '',
            'delete_order_order_state' => 6,
            'width' => 10,
            'height' => 10,
            'length' => 10,
            'weight' => 1,
            'categories_setting' => '',
            'DeliveryRecipientVATRate' => 'VAT18',
            'carriers_settings' => '',
            'weight_unit' => 1,
            'status_send_order' => 2,
            'send_order_after_create' => true
        );

        /**
         * for 1.7.x.x
         */
        $this->tabs = array(
            array(
                'class_name' => 'AdminCdekSetting',
                'ParentClassName' => 'AdminParentShipping',
                'name' => array(
                    'en-US' => 'CDEK delivery option',
                    'ru-RU' => 'CDEK способ доставки'
                ),
                'visible' => true,
                'icon' => false
            )
        );
    }

    public function install()
    {
        $this->setConfiguration();
        if (version_compare(_PS_VERSION_, '1.7.1.0', '<'))
        {
            ToolsModuleSK::createTab($this->name,
                'AdminCdekSetting',
                'AdminParentShipping',
                array(
                    'ru' => 'CDEK способ доставки',
                    'en' => 'CDEK delivery option'
            ));
        }

        HelperDbSK::loadClass('CarrierTariff')->installDb();
        HelperDbSK::loadClass('CDEKOrderInfo')->installDb();
        HelperDbSK::loadClass('CDEKCity')->installDb();
        $this->installTariffs();
        if (!parent::install() || !$this->registerHook('displayHeader')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionOrderStatusUpdate')
            || !$this->registerHook('displayAdminOrder')
            || !$this->registerHook('displayBeforeCarrier')
            || !$this->registerHook('displayOrderDetail'))
            return false;
        return true;
    }

    public function uninstall()
    {
        $this->deleteConfiguration();
        ToolsModuleSK::deleteTab('AdminCdekSetting');
        $this->uninstallTariffs();
        HelperDbSK::loadClass('CarrierTariff')->uninstallDb();
        HelperDbSK::loadClass('CDEKOrderInfo')->uninstallDb();
        HelperDbSK::loadClass('CDEKCity')->uninstallDb();
        if (!parent::uninstall())
            return false;
        return true;
    }

    public function installTariffs()
    {
        foreach (CDEKConf::getTariffs() as $tariff)
        {
            $id_carrier = $this->installCarrier('СДЭК '.$tariff['name'], $tariff['range']);
            if ($id_carrier)
            {
                if (Configuration::get('PS_CARRIER_DEFAULT') < 0)
                    Configuration::updateValue('PS_CARRIER_DEFAULT', $id_carrier);

                $carrier = new Carrier($id_carrier);
                $carrier_tariff = new CarrierTariff();
                $carrier_tariff->id_reference = $carrier->id_reference;
                $carrier_tariff->id_tariff = $tariff['id'];
                $carrier_tariff->save();
            }
        }
    }

    public function uninstallTariffs()
    {
        try
        {
            foreach (CarrierTariff::getAll() as $tariff)
                $this->uninstallCarrier($tariff['id_reference']);
        }
        catch(Exception $e)
        {
            unset($e);
        };
    }

    public function setConfiguration()
    {
        foreach ($this->config as $name => $config)
            ConfSK::setConf($name, $config);
    }

    public function deleteConfiguration()
    {
        foreach (array_keys($this->config) as $name)
            ConfSK::deleteConf($name);
    }

    /**
     * @param string $name
     * @param array $weight_range
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    private function installCarrier($name, $weight_range)
    {
        $carrier = new Carrier();
        $carrier->range_behavior = 1;
        $carrier->name = $name;
        $carrier->active = true;
        $carrier->deleted = 0;
        $carrier->shipping_handling = false;
        $delay_str_list = array('ru'=>' ', 'default'=>'Delivery time depens on distance');
        $languages = Language::getLanguages(false);
        foreach ($languages as $language)
        {
            if (!isset($delay_str_list[$language['iso_code']]))
                $carrier->delay[(int)$language['id_lang']] = $delay_str_list['default'];
            else
                $carrier->delay[(int)$language['id_lang']] = $delay_str_list[$language['iso_code']];
        }
        $carrier->shipping_external = true;
        $carrier->is_module = true;
        $carrier->external_module_name = $this->name;
        $carrier->need_range = true;
        if ($carrier->add())
        {
            $groups = Group::getGroups(true);
            foreach ($groups as $group)
            {
                Db::getInstance()->insert('carrier_group', array(
                    'id_carrier' => (int)$carrier->id,
                    'id_group' => (int)$group['id_group']
                ));
            }
            $range_price = new RangePrice();
            $range_price->id_carrier = $carrier->id;
            $range_price->delimiter1 = '0';
            $range_price->delimiter2 = '100500';
            $range_price->add();

            $range_weight = new RangeWeight();
            $range_weight->id_carrier = $carrier->id;
            $range_weight->delimiter1 = $weight_range['min'];
            $range_weight->delimiter2 = $weight_range['max']; //Предельные тяжеловесные посылки 20 кг
            $range_weight->add();

            $zones = Zone::getZones(true);
            foreach ($zones as $z)
            {
                Db::getInstance()->insert('carrier_zone', array('id_carrier' => (int)$carrier->id,
                    'id_zone' => (int)$z['id_zone']));
                Db::getInstance()->insert('delivery',
                    array('id_carrier' => (int)$carrier->id, 'id_range_price' => (int)$range_price->id,
                        'id_range_weight' => null, 'id_zone' => (int)$z['id_zone'], 'price' => '0'), true, 0);
                Db::getInstance()->insert('delivery', array('id_carrier' => (int)$carrier->id,
                    'id_range_price' => null, 'id_range_weight' => (int)$range_weight->id,
                    'id_zone' => (int)$z['id_zone'], 'price' => '0'), true, 0);
            }
            if (file_exists(dirname(__FILE__).'/views/img/carrier.jpg'))
                copy(dirname(__FILE__).'/views/img/carrier.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg');
            return $carrier->id;
        }

        return false;
    }

    private function uninstallCarrier($id_reference)
    {
        $carrier = Carrier::getCarrierByReference($id_reference);
        if (Validate::isLoadedObject($carrier))
        {
            $lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $carriers = Carrier::getCarriers($lang_default, true, false, false, null, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
            if (Configuration::get('PS_CARRIER_DEFAULT') == $carrier->id)
            {
                foreach ($carriers as $c)
                {
                    if ($c['active'] && !$c['deleted'] && ($c['name'] != $carrier->name))
                        Configuration::updateValue('PS_CARRIER_DEFAULT', $c['id_carrier']);
                }
            }

            if (!$carrier->deleted)
            {
                $carrier->deleted = 1;
                if (!$carrier->update())
                    return false;
            }
        }
        return true;
    }

    public function hookDisplayHeader()
    {
        $this->context->cart->getDeliveryOptionList();

        if (Tools::isSubmit('ajax_cdek'))
            ToolsModuleSK::createAjaxApiCall($this);

        $this->context->smarty->assign(array(
            'cdek_carriers' => CarrierTariff::getAllCarriers(self::$delay_carriers, false),
            'cdek_dir' => $this->getPathUri(),
            'cdek_address_parameter' => false,
            'cdek_order_info' => CDEKOrderInfo::getInstanceByCart($this->context->cart->id)
        ));

        $lang = 'en-US';
        switch ($this->context->language->iso_code)
        {
            case 'ru':
                $lang = 'ru-RU';
                break;
            case 'en':
                $lang = 'en-US';
                break;
        }

        if ($this->context->controller instanceof ParentOrderController
            || $this->context->controller instanceof OrderController) {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $this->context->controller->addJS('//api-maps.yandex.ru/2.1/?lang='
                    .$lang);
            } else {
                $this->context->controller->registerJavascript(
                    'yandex_map',
                    '//api-maps.yandex.ru/2.0-stable/?load=package.standard&lang='
                    .$lang,
                    array(
                        'server' => 'remote'
                    )
                );
            }
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            $this->context->controller->addJqueryUI('ui.mouse');
            $this->context->controller->addJqueryUI('ui.datepicker');
        } else {
            $this->context->controller->addJS($this->getPathUri().'/views/js/jquery-ui.js');
        }

        $this->context->controller->addJS($this->getPathUri().'/views/js/jQAllRangeSliders-min.js');
        ToolsModuleSK::autoloadJS($this->getPathUri().'views/js/tools/');
//        $this->context->controller->addJS(_THEME_JS_DIR_.'order-opc.js');
        $this->context->controller->addJS($this->getPathUri().'views/js/front.js');

        $this->context->controller->addCSS($this->getPathUri().'views/css/iThing.css');
        $this->context->controller->addCSS($this->getPathUri().'views/css/front.css');
        return $this->display(__FILE__, 'header.tpl');
    }

    public function hookActionValidateOrder($params)
    {
        $order = $params['order'];
        /**
         * @var OrderState
         */
        $order_state = $params['orderStatus'];
        $tariff = CarrierTariff::getTariffByCarrier($order->id_carrier);
        if ($tariff && ConfSK::getConf('send_order_after_create')) {
            $document = new CDEKNewOrders(
                $params['order'],
                (in_array(
                    $order_state->id,
                    array(2, 12)
                ) ? true : false)
            );
            $document->send();
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        /**
         * @var $order_state OrderState
         */
        $order_state = $params['newOrderStatus'];
        $order = new Order((int)$params['id_order']);
        if (Validate::isLoadedObject($order)) {
            if ($order_state->id == (int)CDEKConf::getDeleteOrderOrderState()) {
                $document = new CDEKDeleteOrders($order);
                $document->send();
            }
            if ($order_state->id == (int)ConfSK::getConf('status_send_order')) {
                $document = new CDEKNewOrders(
                    $order,
                    (in_array(
                        $order->current_state,
                        array(2, 12)
                    ) || in_array(
                        $order_state->id,
                        array(2, 12)
                    ) ? true : false)
                );
                $document->send();
            }
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        ToolsModuleSK::registerSmartyFunctions();
        $id_order = (int)$params['id_order'];
        $order = new Order($id_order);
        if (Validate::isLoadedObject($order) && $order->current_state != CDEKConf::getDeleteOrderOrderState())
        {
            $order_info = $this->getOrderInfo($order);
            $id_carrier = $order->id_carrier;
            $tariff = CarrierTariff::getTariffByCarrier($id_carrier);
            if ($tariff)
            {
                $this->context->smarty->assign(array(
                    'document_link' => $this->context->link->getAdminLink('AdminCdekSetting', true),
                    'id_order' => $id_order,
                    'order_info' => $order_info,
                    'info' => CDEKOrderInfo::getInstanceByCart($order->id_cart),
                    'status_send_order' => new OrderState(
                        ConfSK::getConf('status_send_order'),
                        $this->context->language->id
                    )
                ));
                return $this->display(__FILE__, 'admin_order.tpl');
            }
        }
    }

    public function hookDisplayOrderDetail($params)
    {
        /**
         * @var $order Order
         */
        $order = $params['order'];
        if (Validate::isLoadedObject($order) && $order->current_state != CDEKConf::getDeleteOrderOrderState())
        {
            $order_info = $this->getOrderInfo($order);
            $id_carrier = $order->id_carrier;
            $tariff = CarrierTariff::getTariffByCarrier($id_carrier);
            if ($tariff)
            {
                $this->context->smarty->assign(array(
                    'order_info' => $order_info,
                    'info' => CDEKOrderInfo::getInstanceByCart($order->id_cart)
                ));
                return $this->display(__FILE__, 'order_detail.tpl');
            }
        }
    }

    public function hookDisplayBeforeCarrier()
    {
        if (!count($this->errors) || !_PS_MODE_DEV_)
            return '';
        $this->context->smarty->assign('cdek_errors', $this->errors);
        return $this->display(__FILE__, 'before_carrier.tpl');
    }

    public function getOrderInfo($order)
    {
        $document = new CDEKHistory($order);
        $response = $document->send();
        $order_info = array(
            'ActNumber' => '',
            'Number' => '',
            'DispatchNumber' => '',
            'DeliveryDate' => '',
            'RecipientName' => '',
            'Status' => array()
        );

        if (ToolsModuleSK::checkItemArray('StatusReport', $response))
        {
            if (ToolsModuleSK::checkItemArray('Order', $response['StatusReport']))
            {
                if (ToolsModuleSK::checkItemArray('@attributes', $response['StatusReport']['Order']))
                {
                    $attributes = $response['StatusReport']['Order']['@attributes'];
                    $order_info['ActNumber'] = $attributes['ActNumber'];
                    $order_info['Number'] = $attributes['Number'];
                    $order_info['DispatchNumber'] = $attributes['DispatchNumber'];
                    $order_info['DeliveryDate'] = $attributes['DeliveryDate'];
                    $order_info['RecipientName'] = $attributes['RecipientName'];
                }
                $order = $response['StatusReport']['Order'];
                if (ToolsModuleSK::checkItemArray('Status', $order))
                {
                    if (ToolsModuleSK::checkItemArray('State', $order['Status']))
                    {
                        $statuses = array();
                        if (ToolsModuleSK::checkItemArray('@attributes', $order['Status']['State']))
                            $statuses[] = $order['Status']['State'];
                        else
                            $statuses = $order['Status']['State'];
                        $order_info['Status'] = $statuses;
                    }
                }
            }
        }

        return $order_info;
    }

    public static $delay_carriers = array();
    public static $cache_order_shipping_cost = array();
    public function getOrderShippingCost($params, $shipping_cost)
    {
        $cart = $params;
        if (!($cart instanceof Cart))
            return false;
        unset($shipping_cost);
        /**
         * @var $address Address
         */
        $address = $this->context->cart->getAddressCollection();
        $address = current($address);
        if (!Validate::isLoadedObject($address))
            return false;

        if (!isset($cart->id_carrier_current))
            return false;

        if (array_key_exists($cart->id_carrier_current, self::$cache_order_shipping_cost))
            return self::$cache_order_shipping_cost[$cart->id_carrier_current];


        $calculate = new CDEKCalculate($cart);
        $response = $calculate->calculate();
        //$error_logger = ErrorLoggerSK::getInstance();

        if ($response['overprice'] || $address->city == 'Санкт-Петербург')
            return false;

        if (!$response['has_error'])
        {
            if ($response['result']['deliveryPeriodMin'] == $response['result']['deliveryPeriodMax'])
                $period = sprintf($this->l('Period delivery %s days.'), $response['result']['deliveryPeriodMin']);
            else
                $period = sprintf($this->l('Period delivery min %s days, max %s days.'),
                    $response['result']['deliveryPeriodMin'],
                    $response['result']['deliveryPeriodMax']);

            if ($response['result']['deliveryDateMin'] == $response['result']['deliveryDateMax'])
                $date = sprintf($this->l('Date delivery %s.'), $response['result']['deliveryDateMin']);
            else
                $date = sprintf($this->l('Date delivery min %s, max %s.'),
                    $response['result']['deliveryDateMin'],
                    $response['result']['deliveryDateMax']);
            self::$delay_carriers[$cart->id_carrier_current] = $period.' '.$date;

            $price = $response['result']['price'];
            $carrier = new Carrier($cart->id_carrier_current);
            $carriers_settings = FormatConfCarriers::getSettings();
            if (isset($carriers_settings[$carrier->id_reference]))
                $price += (float)$carriers_settings[$carrier->id_reference]['commission'];
            self::$cache_order_shipping_cost[$cart->id_carrier_current] = $price;
            return self::$cache_order_shipping_cost[$cart->id_carrier_current];
        }
        else
        {
            //$error_logger->add($response['errors']);
            /*$carrier = new Carrier($cart->id_carrier_current, $this->context->language->id);
            if (is_array($response['errors']))
            {
                array_shift($response['errors']);
                foreach ($response['errors'] as $item)
                    $this->errors[] = $carrier->name.': '.$item['text'];
            }
            else
                $this->errors[] = $carrier->name.': '.$response['errors'];
            $this->errors[] = '';*/
            return false;
        }
    }

    public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params, 0);
    }

    public function ajaxProcessGetListPvz()
    {
        $return_pvz_list = array();
        $address = $this->context->cart->getAddressCollection();
        /**
         * @var $address Address
         */
        $address = current($address);
        $xml_response = call_user_func(
            'file_get_contents',
            'https://integration.cdek.ru/pvzlist.php?citypostcode='.$address->postcode
        );
        $array_response = XML2Array::createArray($xml_response);

        $pvz_code = ToolsModuleSK::getCookieKey('cdek_pvz_key');
        $check_pvz_code = false;
        if (ToolsModuleSK::checkItemArray('PvzList', $array_response))
        {
            $pvz_list = $array_response['PvzList'];
            if (ToolsModuleSK::checkItemArray('Pvz', $pvz_list))
            {
                $pvz_list_items = $pvz_list['Pvz'];
                if (is_array($pvz_list_items) && count($pvz_list_items))
                {
                    if (array_key_exists('@attributes', $pvz_list_items))
                        $pvz_list_items = array($pvz_list_items);

                    foreach ($pvz_list_items as $pvz_list_item)
                    {
                        if (ToolsModuleSK::checkItemArray('@attributes', $pvz_list_item))
                        {
                            if ($pvz_code && $pvz_list_item['@attributes']['Code'] == $pvz_code) {
                                $check_pvz_code = $pvz_list_item['@attributes'];
                            }
                            $return_pvz_list[] = $pvz_list_item['@attributes'];
                        }
                    }
                }
            }
        }

        $order_info = CDEKOrderInfo::getInstanceByCart($this->context->cart->id);
        if (!ToolsModuleSK::getCookieKey('cdek_pvz_key') || !$check_pvz_code) {
            ToolsModuleSK::setCookieKey('cdek_pvz_key', $return_pvz_list[0]['Code']);
            $order_info->pvz_key = $return_pvz_list[0]['Code'];
            $order_info->delivery_point = $return_pvz_list[0]['City'].', '.$return_pvz_list[0]['Address'];
            $order_info->tariff = $return_pvz_list[0]['Code'];
            $order_info->save();
        }
        if ($check_pvz_code) {
            $order_info->delivery_point = $check_pvz_code['City'].', '.$check_pvz_code['Address'];
            $order_info->tariff = $check_pvz_code['Code'];
            $order_info->pvz_key = $check_pvz_code['Code'];
            $order_info->save();
        }

        return array(
            'pzv_list' => $return_pvz_list,
            'cdek_pvz_code' => ToolsModuleSK::getCookieKey('cdek_pvz_key'),
            'address' => array(
                'street' => ToolsModuleSK::getCookieKey('cdek_street'),
                'house' => ToolsModuleSK::getCookieKey('cdek_house'),
                'flat' => ToolsModuleSK::getCookieKey('cdek_flat')
            )
        );
    }

    public function ajaxProcessCdekCode()
    {
        $code = Tools::getValue('code');
        $delivery_point = Tools::getValue('delivery_point');
        ToolsModuleSK::setCookieKey('cdek_pvz_key', $code);
        $order_info = CDEKOrderInfo::getInstanceByCart($this->context->cart->id);
        $order_info->tariff = $code;
        $order_info->pvz_key = $code;
        $order_info->id_cart = $this->context->cart->id;
        $order_info->delivery_point = $delivery_point;
        $order_info->save();
        return array();
    }

    public function ajaxProcessCdekDeliveryDate()
    {
        $date = Tools::getValue('date');
        $time_begin = Tools::getValue('time_begin');
        $time_end = Tools::getValue('time_end');

        $order_info = CDEKOrderInfo::getInstanceByCart($this->context->cart->id);
        $order_info->delivery_date = ($date && $date != '00-00-0000'
            ? date('Y-m-d H:i:s', strtotime($date)) : '0000-00-00 00:00:00');
        $order_info->delivery_time_begin = $time_begin;
        $order_info->delivery_time_end = $time_end;
        $order_info->save();
        return array();
    }

    public function ajaxProcessCdekAddress()
    {
        ToolsModuleSK::setCookieKey('cdek_street', Tools::getValue('street'));
        ToolsModuleSK::setCookieKey('cdek_house', Tools::getValue('house'));
        ToolsModuleSK::setCookieKey('cdek_flat', Tools::getValue('flat'));
        $order_info = CDEKOrderInfo::getInstanceByCart($this->context->cart->id);
        $order_info->tariff = '';
        $order_info->street = Tools::getValue('street');
        $order_info->house = Tools::getValue('house');
        $order_info->flat = Tools::getValue('flat');
        $order_info->id_cart = $this->context->cart->id;
        $order_info->delivery_point = '';
        $order_info->save();
        return array();
    }

    public function getContent()
    {
        ToolsModuleSK::registerSmartyFunctions();
        return $this->getDocumentation();
    }

    public function assignDocumentation()
    {
        $this->context->controller->addCSS($this->getLocalPath().'views/css/documentation.css');
        $documentation_folder = $this->getLocalPath().'views/templates/admin/documentation';
        $documentation_pages = self::globRecursive($documentation_folder.'/**.tpl');
        natsort($documentation_pages);

        $tree = array();
        if (is_array($documentation_pages) && count($documentation_pages))
            foreach ($documentation_pages as &$documentation_page)
            {
                $name = str_replace(array($documentation_folder.'/', '.tpl'), '', $documentation_page);
                $path = explode('/', $name);

                $tmp_tree = &$tree;
                foreach ($path as $key => $item)
                {
                    $part = $item;
                    if ($key == (count($path) - 1))
                        $tmp_tree[$part] = $name;
                    else
                    {
                        if (!isset($tmp_tree[$part]))
                            $tmp_tree[$part] = array();
                    }
                    $tmp_tree = &$tmp_tree[$part];
                }
            }

        $this->context->smarty->assign('tree', $this->buildTree($tree));
        $this->context->smarty->assign('documentation_pages', $documentation_pages);
        $this->context->smarty->assign('documentation_folder', $documentation_folder);
    }

    public function getDocumentation()
    {
        $this->assignDocumentation();
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/documentation.tpl');
    }

    public function buildTree($tree)
    {
        $tree_html = '';
        if (is_array($tree) && count($tree))
        {
            foreach ($tree as $name => $tree_item)
            {
                preg_match('/^(\d+)\._(.*)$/', $name, $matches);
                $format_name = $matches[1].'. '.TransModSK::getInstance()->ld($matches[2]);

                $tree_html .= '<li>';
                $tree_html .= '<a '.(!is_array($tree_item) ? 'data-tab="'.$tree_item.'" href="#"' : '').'>'.$format_name.'</a>';
                if (is_array($tree_item) && count($tree_item))
                {
                    $tree_html .= '<ul>';
                    $tree_html .= $this->buildTree($tree_item);
                    $tree_html .= '</ul>';
                }
                $tree_html .= '</li>';
            }
        }
        return $tree_html;
    }

    /**
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    public static function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        if (!$files)
            $files = array();

        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $files = array_merge($files, self::globRecursive($dir.'/'.basename($pattern), $flags));

        return $files;
    }

    public static function noEscape($value)
    {
        return $value;
    }

    public function getDocumentationLinks()
    {
        $this->context->controller->addCSS($this->getPathUri().'views/css/front.css');
        $this->context->smarty->assign('link_on_tab_module', $this->getAdminLink());
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/documentation_links.tpl');
    }

    public function getAdminLink()
    {
        return $this->context->link->getAdminLink('AdminModules', true)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    }
}