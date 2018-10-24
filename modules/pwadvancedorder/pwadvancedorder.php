<?php
if (!defined('_PS_VERSION_'))
    exit;


class pwadvancedorder extends PaymentModule
{

    public function __construct()
    {
        $this->name = 'pwadvancedorder';
        $this->tab = 'other';
        $this->version = '0.5.3';
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Быстрый заказ");
        $this->description = $this->l("Быстрый заказ");
        $this->controllers = array("ajax", "order");
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if (!parent::install() 
            || !$this->registerHook(array("Header","actionDispatcher","moduleRoutes", "displayOrderConfirmation"))
            || !$this->installDb()
        ){
            return false;
        }
        Configuration::updateValue('PWADVANCEDORDER_HEADER', '1');
        $fields = array_merge($this->getCustomerFields(), $this->getAddressFields());
        foreach($fields as $key => $field) {
            $name = 'PWADVANCEDORDER_FIELD_'.$key;
            if (!Configuration::hasKey($name)) {
                Configuration::updateValue($name, !empty($field['required'])?'required':'');
            }
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallDb())
            return false;

        return true;
    }

    protected function installDb()
    {
        return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'customer` ADD COLUMN `middlename` VARCHAR(45) NULL AFTER `lastname`;'); //ToDO: add to middlename to _DB_PREFIX_.'address

    }

    protected function uninstallDb()
    {
        return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'customer` DROP COLUMN `middlename`;');
    }

    public function getContent()
    {
        $this->postProcess();
        return $this->renderForm();
    }
    
    private function postProcess()
    {
        if (Tools::isSubmit('submitPwadvancedorder'))
        {
            Configuration::updateValue('PWADVANCEDORDER_FOOTER', Tools::getValue('PWADVANCEDORDER_FOOTER'));
            Configuration::updateValue('PWADVANCEDORDER_HEADER', Tools::getValue('PWADVANCEDORDER_HEADER'));
            Configuration::updateValue('PWADVANCEDORDER_ONECLICK', Tools::getValue('PWADVANCEDORDER_ONECLICK'));
            Configuration::updateValue('PWADVANCEDORDER_REEMAIL', Tools::getValue('PWADVANCEDORDER_REEMAIL'));

            $fields = array_merge($this->getCustomerFields(), $this->getAddressFields());
            foreach($fields as $key => $field) {
                $name = 'PWADVANCEDORDER_FIELD_'.$key;
                if (Tools::isSubmit($name)) {
                    Configuration::updateValue($name, Tools::getValue($name));
                }
            }
            
            $this->context->controller->confirmations[] = $this->l('Настройки успешно сохранены');
        }
    }
    
    private function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Настройки'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Показывать футер'),
                        'name' => 'PWADVANCEDORDER_FOOTER',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'PWADVANCEDORDER_FOOTER_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'PWADVANCEDORDER_FOOTER_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Показывать шапку'),
                        'name' => 'PWADVANCEDORDER_HEADER',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'PWADVANCEDORDER_HEADER_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'PWADVANCEDORDER_HEADER_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Показывать форму покупки в 1 клик'),
                        'name' => 'PWADVANCEDORDER_ONECLICK',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'PWADVANCEDORDER_ONECLICK_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'PWADVANCEDORDER_ONECLICK_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Разрешить повторно использовать email'),
                        'name' => 'PWADVANCEDORDER_REEMAIL',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'PWADVANCEDORDER_REEMAIL_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'PWADVANCEDORDER_REEMAIL_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            ),
        );
        $fields = array_merge($this->getCustomerFields(), $this->getAddressFields());
        $inputs = array();
        foreach ($fields as $key => $field) {
            $input = array(
                'type' => 'radio',
                'label' => $field['name'],
                'name' => 'PWADVANCEDORDER_FIELD_'.$key,
                'values' => array(
                    array(
                        'id'    => $key.'_off',
                        'value' => '',
                        'label' => $this->l('Выключить')
                    ),
                    array(
                        'id'    => $key.'_on',
                        'value' => 'enable',
                        'label' => $this->l('Включить')
                    ),
                    array(
                        'id'    => $key.'_required',
                        'value' => 'required',
                        'label' => $this->l('Обязательное')
                    ),
                ),
            );
            $inputs[] = $input;
        }
        $auth_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Поля регистрации'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPwadvancedorder';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form, $auth_form));
    }
    
    public function getCustomerFields()
    {
        $exclude = array('date_add', 'date_upd', 'id_lang', 'id_default_group', 'id_shop_group', 'id_shop', 'is_guest', 'note', 'deleted', 'active', 'max_payment_days', 'id_risk',
                        'show_public_prices', 'outstanding_allow_amount', 'ape', 'siret', 'siret', 'website', 'ip_registration_newsletter', 'newsletter_date_add', 'last_passwd_gen',
                        'passwd', 'secure_key', 'id_gender', 'newsletter', 'optin');
        $fields = Customer::$definition['fields'];
        foreach ($exclude as $field) {
            if (isset($fields[$field])) {
                unset($fields[$field]);
            }
        }
        foreach ($fields as $field => $params) {
            $fields[$field]['name'] = Customer::displayFieldName($field, 'Customer');
            $fields[$field]['field'] = $field;
        }
        return $fields;
    }
    
    public function getAddressFields()
    {
        $exclude = array('id_customer', 'id_manufacturer', 'id_supplier', 'id_warehouse', 'dni', 'deleted', 'date_add', 'date_upd',);
        $fields = Address::$definition['fields'];
        foreach ($exclude as $field) {
            if (isset($fields[$field])) {
                unset($fields[$field]);
            }
        }
        foreach ($fields as $field => $params) {
            $fields[$field]['name'] = Address::displayFieldName($field, 'Address');
            $fields[$field]['field'] = $field;
        }
        return $fields;
    }
    
    private function getConfigFieldsValues()
    {
        $values = Configuration::getMultiple(array(
            'PWADVANCEDORDER_FOOTER', 'PWADVANCEDORDER_HEADER', 'PWADVANCEDORDER_ONECLICK', 'PWADVANCEDORDER_REEMAIL'
        ));
        $fields = array_merge($this->getCustomerFields(), $this->getAddressFields());
        foreach($fields as $key => $field) {
            $name = 'PWADVANCEDORDER_FIELD_'.$key;
            if (Configuration::hasKey($name)) {
                $values[$name] = Configuration::get($name);
            } else {
                $values[$name] = !empty($field['required'])?'required':'';
            }
        }
        return $values;
    }

    public function hookHeader($params)
    {
        $this->context->controller->addJS($this->_path.'pwadvancedorder.js');
//        $this->context->controller->addJqueryUI('ui.autocomplete');
        $this->context->controller->addCss($this->_path.'pwadvancedorder.css');
    }

    public function hookActionDispatcher($params)
    {
        if ($this->context->controller instanceof ParentOrderController) {
            if ($this->context->controller instanceof pwadvancedorderorderModuleFrontController) {
                return;
            }
            if (!$this->ajax) {
                Tools::redirect($this->context->link->getModuleLink($this->name, 'order', ($_GET + $_POST)), __PS_BASE_URI__, null, 'HTTP/1.1 301 Moved Permanently');
            }
        }
    }

    public function hookModuleRoutes($params)
    {
        return array(
            'module-pwadvancedorder-order' => array(
                'controller' => 'order',
                'rule' => 'advancedorder',
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'pwadvancedorder',
                ),
            ),
        );
    }
    
    public function hookdisplayOrderConfirmation($params)
    {
        global $smarty;
        $smarty->assign(array(
            'total_to_pay' => number_format($params['total_to_pay'], '0', '', ''),
            'id_order' => $params['objOrder']->id
        ));

        if (Tools::getValue('oneclick') =='yes')
            $this->context->customer->mylogout();

        return $this->display(__FILE__, 'confirmation.tpl');
    }


}
