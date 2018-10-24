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
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(dirname(__FILE__).'/../../config.php');

class AdminCdekSettingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->display = 'edit';
        parent::__construct();
        ToolsModuleSK::registerSmartyFunctions();
        HelperDbSK::loadClass('CDEKLogger')->installDb();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addCSS(array(
            $this->module->getPathUri().'views/css/admin.css',
            '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css'
        ));
        $this->context->controller->addJS(array(
            $this->module->getPathUri().'views/js/admin.js',
            '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js',
            $this->module->getPathUri().'views/js/clipboard.js'
        ));
        $this->context->controller->addJqueryUI('ui.datepicker');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('orderPrint')) {
            $id_order = (int)Tools::getValue('id_order');
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order)) {
                $document = new CDEKOrdersPrint($order);
                $document->setLogged(false);
                die($document->send());
            } else {
                Tools::redirect($this->context->link->getAdminLink(Tools::getValue('controller')));
            }
        }

        if (Tools::isSubmit('submitAddconfiguration')) {
            $account = Tools::getValue(ConfSK::formatConfName('account'));
            if (!empty($account)) {
                ConfSK::setConf('account', Tools::getValue(ConfSK::formatConfName('account')));
            }
            else {
                $this->errors[] = $this->l('Account empty!');
            }

            ConfSK::setConf('write_log', Tools::getValue(ConfSK::formatConfName('write_log')));

            $secure_password = Tools::getValue(ConfSK::formatConfName('secure_password'));
            if (!empty($secure_password)) {
                ConfSK::setConf('secure_password', Tools::getValue(ConfSK::formatConfName('secure_password')));
            } else {
                $this->errors[] = $this->l('Secure password empty!');
            }

            $postcode = Tools::getValue(ConfSK::formatConfName('postcode'));
            if (!empty($postcode)) {
                ConfSK::setConf('postcode', Tools::getValue(ConfSK::formatConfName('postcode')));
            } else {
                $this->errors[] = $this->l('Postcode empty!');
            }
            ConfSK::setConf(
                'delete_order_order_state',
                Tools::getValue(ConfSK::formatConfName('delete_order_order_state'))
            );
            ConfSK::setConf(
                'send_order_after_create',
                Tools::getValue(ConfSK::formatConfName('send_order_after_create'))
            );

            $status_send_order = Tools::getValue(ConfSK::formatConfName('status_send_order'));
            ConfSK::setConf(
                'status_send_order',
                implode('|', $status_send_order)
            );
            ConfSK::setConf('width', (float)Tools::getValue(ConfSK::formatConfName('width')));
            ConfSK::setConf('height', (float)Tools::getValue(ConfSK::formatConfName('height')));
            ConfSK::setConf('length', (float)Tools::getValue(ConfSK::formatConfName('length')));
            ConfSK::setConf('weight', (float)Tools::getValue(ConfSK::formatConfName('weight')));
            ConfSK::setConf('include_insurance', (int)Tools::getValue(ConfSK::formatConfName('include_insurance')));

            if (ConfSK::getConf('weight_unit')
                != (float)Tools::getValue(ConfSK::formatConfName('weight_unit'))) {
                $weight_unit = (float)Tools::getValue(ConfSK::formatConfName('weight_unit'));
                $carrier_tariffs = CarrierTariff::getAll();
                foreach ($carrier_tariffs as $carrier_tariff) {
                    $tariff = CDEKConf::getTariffInfo($carrier_tariff['id_tariff']);
                    $carrier = Carrier::getCarrierByReference($carrier_tariff['id_reference']);
                    $ranges_weight = RangeWeight::getRanges($carrier->id);
                    foreach ($ranges_weight as $range_weight) {
                        $range = new RangeWeight($range_weight['id_range_weight']);
                        $range->delimiter1 = $tariff['range']['min'] / $weight_unit ;
                        $range->delimiter2 = $tariff['range']['max'] / $weight_unit ;
                        $range->save();
                    }
                }
            }

            ConfSK::setConf(
                'weight_unit',
                (float)Tools::getValue(ConfSK::formatConfName('weight_unit'))
            );
            ConfSK::setConf(
                'DeliveryRecipientVATRate',
                Tools::getValue(ConfSK::formatConfName('DeliveryRecipientVATRate'))
            );
            ConfSK::setConf(
                'categories_setting',
                Tools::getValue(ConfSK::formatConfName('categories_setting')),
                ConfSK::TYPE_ARRAY
            );

            ConfSK::setConf(
                'carriers_settings',
                Tools::getValue(ConfSK::formatConfName('carriers_settings')),
                ConfSK::TYPE_ARRAY
            );
            ConfSK::setConf(
                'order_state_settings',
                Tools::getValue(ConfSK::formatConfName('order_state_settings')),
                ConfSK::TYPE_ARRAY
            );

            $carrier_tariffs = CarrierTariff::getAll();
            foreach ($carrier_tariffs as $carrier_tariff) {
                $carrier = Carrier::getCarrierByReference($carrier_tariff['id_reference']);
                if (Validate::isLoadedObject($carrier)) {
                    if (Tools::getValue('tariffs_'.$carrier_tariff['id_tariff'])) {
                        $carrier->active = 1;
                    } else {
                        $carrier->active = 0;
                    }
                    $carrier->save();
                }
            }
            CDEKCache::clearCacheAll();
            $this->display = 'edit';
            if (!count($this->errors)) {
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminCdekSetting').'&conf=4'
                );
            }
        }

        return parent::postProcess();
    }

    public function getFieldsValue($obj)
    {
        $fields_value = parent::getFieldsValue($obj);
        $fields_value[ConfSK::formatConfName('account')] = ConfSK::getConf('account');
        $fields_value[ConfSK::formatConfName('write_log')] = ConfSK::getConf('write_log');
        $fields_value[ConfSK::formatConfName('send_order_after_create')]
            = ConfSK::getConf('send_order_after_create');
        $fields_value[ConfSK::formatConfName('status_send_order').'[]'] = (
            ConfSK::getConf('status_send_order') ?
            explode('|', ConfSK::getConf('status_send_order')) :
            array()
        );
        $fields_value[ConfSK::formatConfName('secure_password')]
            = ConfSK::getConf('secure_password');
        $fields_value[ConfSK::formatConfName('postcode')]
            = ConfSK::getConf('postcode');
        $fields_value[ConfSK::formatConfName('delete_order_order_state')]
            = ConfSK::getConf('delete_order_order_state');
        $fields_value[ConfSK::formatConfName('width')] = (float)ConfSK::getConf('width');
        $fields_value[ConfSK::formatConfName('height')] = (float)ConfSK::getConf('height');
        $fields_value[ConfSK::formatConfName('length')] = (float)ConfSK::getConf('length');
        $fields_value[ConfSK::formatConfName('weight')] = (float)ConfSK::getConf('weight');
        $fields_value[ConfSK::formatConfName('include_insurance')] = (float)ConfSK::getConf('include_insurance');
        $fields_value[ConfSK::formatConfName('weight_unit')]
            = (float)ConfSK::getConf('weight_unit');
        $fields_value[ConfSK::formatConfName('DeliveryRecipientVATRate')]
            = ConfSK::getConf('DeliveryRecipientVATRate');
        $fields_value[ConfSK::formatConfName('categories_setting')] = FormatConfCategories::getSettings();
        $fields_value[ConfSK::formatConfName('carriers_settings')] = FormatConfCarriers::getSettings();
        $fields_value[ConfSK::formatConfName('order_state_settings')] = FormatConfOrderState::getSettings();

        $carrier_tariffs = CarrierTariff::getAll();
        foreach ($carrier_tariffs as $carrier_tariff) {
            $carrier = Carrier::getCarrierByReference($carrier_tariff['id_reference']);
            if (Validate::isLoadedObject($carrier)) {
                $fields_value['tariffs_'.$carrier_tariff['id_tariff']] = (int)$carrier->active;
            } else {
                $fields_value['tariffs_'.$carrier_tariff['id_tariff']] = 0;
            }
        }
        return $fields_value;
    }

    public function renderForm()
    {
        $tariff_values = array();
        foreach (CDEKConf::getTariffs() as $tariff) {
            $tariff_values[] = array(
                'id' => 'tariff_'.$tariff['id'],
                'value' => $tariff['id'],
                'label' => $tariff['name'],
                'val' => 1
            );
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('CDEK settings')
            ),
            'input' => array(
                array(
                    'label' => $this->l('Account'),
                    'name' => ConfSK::formatConfName('account'),
                    'type' => 'text',
                    'placeholder' => 'f3f53177716dc9324bb8ae1f572e4f4c',
                    'required' => true
                ),
                array(
                    'label' => $this->l('Secure password'),
                    'name' => ConfSK::formatConfName('secure_password'),
                    'type' => 'text',
                    'placeholder' => '29e8c263c15c355ae68a33ff8e6639aa',
                    'required' => true
                ),
                array(
                    'label' => $this->l('Postcode'),
                    'name' => ConfSK::formatConfName('postcode'),
                    'type' => 'text',
                    'placeholder' => '117623',
                    'required' => true
                ),
                array(
                    'label' => $this->l('Weight unit'),
                    'name' => ConfSK::formatConfName('weight_unit'),
                    'type' => 'select',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 1,
                                'name' => $this->l('Kg')
                            ),
                            array(
                                'id' => 0.001,
                                'name' => $this->l('g')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'label' => $this->l('Tax on the fee for additional services'),
                    'name' => ConfSK::formatConfName('DeliveryRecipientVATRate'),
                    'type' => 'select',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'VATX',
                                'name' => $this->l('Without tax')
                            ),
                            array(
                                'id' => 'VAT0',
                                'name' => '0%'
                            ),
                            array(
                                'id' => 'VAT10',
                                'name' => '10%'
                            ),
                            array(
                                'id' => 'VAT18',
                                'name' => '18%'
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'label' => $this->l('Select tariffs'),
                    'name' => 'tariffs',
                    'type' => 'checkbox',
                    'values' => array(
                        'query' => $tariff_values,
                        'id' => 'value',
                        'name' => 'label'
                    )
                ),
                array(
                    'label' => $this->l('Include insurance'),
                    'name' => ConfSK::formatConfName('include_insurance'),
                    'type' => 'switch',
                    'values' => array(
                        array(
                            'id' => ConfSK::formatConfName('include_insurance').'_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => ConfSK::formatConfName('include_insurance').'_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'label' => $this->l('Write log'),
                    'name' => ConfSK::formatConfName('write_log'),
                    'type' => 'switch',
                    'values' => array(
                        array(
                            'id' => ConfSK::formatConfName('write_log').'_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => ConfSK::formatConfName('write_log').'_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
//                array(
//                    'label' => $this->l('Send order after create'),
//                    'name' => ConfSK::formatConfName('send_order_after_create'),
//                    'type' => 'switch',
//                    'values' => array(
//                        array(
//                            'id' => ConfSK::formatConfName('send_order_after_create').'_on',
//                            'value' => 1,
//                            'label' => $this->l('Yes')
//                        ),
//                        array(
//                            'id' => ConfSK::formatConfName('send_order_after_create').'_off',
//                            'value' => 0,
//                            'label' => $this->l('No')
//                        )
//                    )
//                ),
//                array(
//                    'label' => $this->l('Select order state, when order send in CDEK'),
//                    'name' => ConfSK::formatConfName('status_send_order'),
//                    'class' => 'select_order_state',
//                    'type' => 'select',
//                    'multiple' => true,
//                    'options' => array(
//                        'query' => OrderState::getOrderStates($this->context->language->id),
//                        'id' => 'id_order_state',
//                        'name' => 'name'
//                    )
//                ),
//				array(
//					'label' => $this->l('Select order state, when order canceled'),
//					'name' => ConfSK::formatConfName('delete_order_order_state'),
//					'type' => 'select',
//					'options' => array(
//						'query' => OrderState::getOrderStates($this->context->language->id),
//						'id' => 'id_order_state',
//						'name' => 'name'
//					)
//				),
                array(
                    'label' => $this->l('Width (on default)'),
                    'name' => ConfSK::formatConfName('width'),
                    'type' => 'text'
                ),
                array(
                    'label' => $this->l('Height (on default)'),
                    'name' => ConfSK::formatConfName('height'),
                    'type' => 'text'
                ),
                array(
                    'label' => $this->l('Length (on default)'),
                    'name' => ConfSK::formatConfName('length'),
                    'type' => 'text'
                ),
                array(
                    'label' => $this->l('Weight (on default)'),
                    'name' => ConfSK::formatConfName('weight'),
                    'type' => 'text'
                ),
                array(
                    'label' => $this->l('Categories setting'),
                    'name' => ConfSK::formatConfName('categories_setting'),
                    'type' => 'category',
                    'categories' => Category::getSimpleCategories($this->context->language->id)
                ),
                array(
                    'label' => $this->l('Carriers settings'),
                    'name' => ConfSK::formatConfName('carriers_settings'),
                    'type' => 'carrier',
                    'carriers' => CarrierTariff::getAllCarriers(array())
                ),
                array(
                    'label' => $this->l('Order state settings'),
                    'name' => ConfSK::formatConfName('order_state_settings'),
                    'type' => 'order_state',
                    'order_states' => OrderState::getOrderStates($this->context->language->id)
                )
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        $this->tpl['link_on_tab_module'] = $this->module->getDocumentationLinks();
        $total = CDEKLogger::getAll(1, true);

        $this->context->smarty->assign(
            array(
                'log_items' => CDEKLogger::getAll(1),
                'pages' => ceil($total / CDEKLogger::LIMIT),
                'methods' => CDEKLogger::getMethods()
            )
        );
        return $this->module->getDocumentationLinks().parent::renderForm();
    }

    public function ajaxProcessGetCdekLog()
    {
        $page = Tools::getValue('page');
        $search = Tools::getValue('search');
        $rows = CDEKLogger::getAll($page, false, $search);

        $html = '';
        foreach ($rows as $row) {
            $this->context->smarty->assign(array(
                'row' => $row
            ));
            $html .= ToolsModuleSK::fetchTemplate(
                'admin/cdek_setting/helpers/form/table_log_row.tpl'
            );
        }

        $json = array(
            'html' => $html
        );

        if ($page == 1) {
            $total = CDEKLogger::getAll($page, true, $search);
            $json['pages'] = ceil($total / CDEKLogger::LIMIT);
        }

        die(Tools::jsonEncode($json));
    }

    public function ajaxProcessClearCdekLog()
    {
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'cdek_logger');
        die();
    }

    public function ajaxProcessSetPvz()
    {
        $pvz_key = Tools::getValue('pvz_key');
        $delivery_point = Tools::getValue('delivery_point');
        $id_cart = Tools::getValue('id_cart');

        $order_info = CDEKOrderInfo::getInstanceByCart($id_cart);
        $order_info->pvz_key = $pvz_key;
        $order_info->delivery_point = $delivery_point;
        $order_info->save();
        die(Tools::jsonEncode(array()));
    }

    public function ajaxProcessRefreshHistoryCdek()
    {
        $id_cart = Tools::getValue('id_cart');
        $order_info = CDEKOrderInfo::getInstanceByCart($id_cart);
        if (Validate::isLoadedObject($order_info)) {
            $order_info->history_response = null;
            $order_info->history_last_update = null;
            $order_info->save();
        }
        die(Tools::jsonEncode(array()));
    }
}
