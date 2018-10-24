<?php
if (!defined('_PS_VERSION_'))
    exit;

/**
 * Changelog
 * 1.1 Исправлена ошибка при нескольких товарах + добавлен параметр quantity для метрики
 * Class pwanalytics
 */

class pwanalytics extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 1.1;
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = "Аналитика Яндекс и Google";
        $this->description = "Помогает настроить аналитику";
    }

    public function install()
    {
        Configuration::updateValue('PWANALYTICS_YANDEX', true);
        Configuration::updateValue('PWANALYTICS_GOOGLE', true);
        return (parent::install()  AND $this->registerHook('OrderConfirmation'));
    }

    //start_helper
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Настройки'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                   /* array(
                        'type' => 'radio',
                        'label' => $this->l('Случайная настройка 1'),
                        'name' => 'PWANALYTICS_OPTION1',
                        'hint' => $this->l('Select which category is displayed in the block. The current category is the one the visitor is currently browsing.'),
                        'values' => array(
                            array(
                                'id' => 'home',
                                'value' => 0,
                                'label' => $this->l('Вариант 1')
                            ),
                            array(
                                'id' => 'current',
                                'value' => 1,
                                'label' => $this->l('Вариант 2')
                            ),
                            array(
                                'id' => 'parent',
                                'value' => 2,
                                'label' => $this->l('Вариант 3')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Настройка 2'),
                        'name' => 'PWANALYTICS_OPTION2',
                        'desc' => $this->l('Подсказка'),
                    ),*/
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Яндекс'),
                        'name' => 'PWANALYTICS_YANDEX',
                        'desc' => $this->l('Включить аналитику яндекса'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Google'),
                        'name' => 'PWANALYTICS_GOOGLE',
                        'desc' => $this->l('Включить аналитику google'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPWANALYTICS';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'PWANALYTICS_YANDEX' => Tools::getValue('PWANALYTICS_YANDEX', Configuration::get('PWANALYTICS_YANDEX')),
            'PWANALYTICS_GOOGLE' => Tools::getValue('PWANALYTICS_GOOGLE', Configuration::get('PWANALYTICS_GOOGLE')),
        );
    }
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitPWANALYTICS'))
        {
            $maxDepth = (int)(Tools::getValue('PWANALYTICS_OPTION1'));
            if ($maxDepth < 0)
                $output .= $this->displayError($this->l('Опция не прошла проверку, убирите её из кода если не нужна'));
            else{
                Configuration::updateValue('PWANALYTICS_YANDEX', Tools::getValue('PWANALYTICS_YANDEX'));
                Configuration::updateValue('PWANALYTICS_GOOGLE', Tools::getValue('PWANALYTICS_GOOGLE'));
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=6');
            }
        }
        return $output.$this->renderForm();
    }
    //end_helper


    public function wrapProduct($product, $extras, $index = 0, $full = false)
    {
        $ga_product = '';

        $variant = null;
        if (isset($product['attributes_small']))
            $variant = $product['attributes_small'];
        elseif (isset($extras['attributes_small']))
            $variant = $extras['attributes_small'];

        $product_qty = 1;
        if (isset($extras['qty']))
            $product_qty = $extras['qty'];
        elseif (isset($product['cart_quantity']))
            $product_qty = $product['cart_quantity'];

        $product_id = 0;
        if (!empty($product['id_product']))
            $product_id = $product['id_product'];
        else if (!empty($product['id']))
            $product_id = $product['id'];

        if (!empty($product['id_product_attribute']))
            $product_id .= '-'. $product['id_product_attribute'];

        $product_type = 'typical';
        if (isset($product['pack']) && $product['pack'] == 1)
            $product_type = 'pack';
        elseif (isset($product['virtual']) && $product['virtual'] == 1)
            $product_type = 'virtual';

        if ($full)
        {

            $ga_product = array(
                'id' => $product_id,
                'name' => $product['name'],
                'category' => $product['category'],
                'brand' => isset($product['manufacturer_name']) ? $product['manufacturer_name'] : '',
                'variant' => $variant,
                'sku' => $product['reference'],
                'type' => $product_type,
                'position' => $index ? $index : '0',
                'quantity' => $product_qty,
                'list' => Tools::getValue('controller'),
                'url' => isset($product['link']) ? urlencode($product['link']) : '',
                'price' => number_format($product['price'], '2')
            );
        }
        else
        {
            $ga_product = array(
                'id' => $product_id,
                'name' => $product['name']
            );
        }
        return $ga_product;
    }

	public function hookOrderConfirmation($params){
        //d(1);
        $order = $params['objOrder'];
        if (Validate::isLoadedObject($order) && $order->getCurrentState() != (int)Configuration::get('PS_OS_ERROR'))
        {
            if ($order->id_customer == $this->context->cookie->id_customer)
            {
                $order_products = array();
                $cart = new Cart($order->id_cart);
                foreach ($cart->getProducts() as $order_product){
                    $order_product['reference']  = Db::getInstance()->getValue('SELECT reference FROM `'._DB_PREFIX_.'product` WHERE id_product = '.$order_product['id_product']);
                    if ($order_product['id_manufacturer']) {
                        $order_product['manufacturer_name'] = ManufacturerCore::getNameById($order_product['id_manufacturer']);
                    }
//                    d($order_product['reference']);
                    $order_products[] = $this->wrapProduct($order_product, array(), 0, true);
                }

                $currency = new Currency($this->context->cookie->id_currency);

                $transaction = array(
                    'id' => $order->id,
                    'affiliation' => (version_compare(_PS_VERSION_, '1.5', '>=') && Shop::isFeatureActive()) ? $this->context->shop->name : Configuration::get('PS_SHOP_NAME'),
                    'revenue' => $order->total_paid,
                    'shipping' => $order->total_shipping,
                    'tax' => $order->total_paid_tax_incl - $order->total_paid_tax_excl,
                    'currency' =>   $currency->iso_code,
                    'customer' => $order->id_customer);

                $this->context->smarty->assign(Array(
                    'transaction' => $transaction,
                    'order_products' => $order_products
                ));
                return $this->display(__FILE__, 'pwanalytics.tpl');
            }
        }
	}


}


