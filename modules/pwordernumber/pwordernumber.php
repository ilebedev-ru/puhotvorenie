<?php



if (!defined('_PS_VERSION_')) {

    exit;

}



class PwOrderNumber extends Module

{

    protected $config_form = false;



    public function __construct()

    {

        $this->name = 'pwordernumber';

        $this->tab = 'administration';

        $this->version = '1.0.0';

        $this->author = 'Prestaweb.ru';

        $this->need_instance = 0;



        $this->bootstrap = true;



        parent::__construct();



        $this->displayName = $this->l('Сделать заказы порядковым номером');

        $this->description = $this->l('Заменяет буквы на порядковый номер');



        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

    }



    public function install()

    {

        return parent::install();

    }



    public function uninstall()

    {

        return parent::uninstall();

    }



    

    public function renderForm()

    {

        $fields_form = array(

            'form' => array(

                'legend' => array(

                    'title' => $this->l('Настройки'),

                    'icon' => 'icon-cogs'

                ),

                'input' => array(

                    array(

                        'type' => 'radio',

                        'label' => $this->l('Тип номера заказа'),

                        'name' => 'PWORDERNUMBER_OPTION1',

                        'hint' => $this->l(''),

                        'values' => array(

                            array(

                                'id' => 'disabled',

                                'value' => 0,

                                'label' => $this->l('Отключить')

                            ),

                            array(

                                'id' => 'id_order',

                                'value' => 1,

                                'label' => $this->l('Порядковый номер')

                            ),

                            array(

                                'id' => 'random',

                                'value' => 2,

                                'label' => $this->l('В случайном порядке')

                            )

                        )

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

        $helper->submit_action = 'submitPWORDERNUMBER';

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

            'PWORDERNUMBER_OPTION1' => Tools::getValue('PWORDERNUMBER_OPTION1', Configuration::get('PWORDERNUMBER_OPTION1'))

        );

    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitPWORDERNUMBER'))

        {

            Configuration::updateValue('PWORDERNUMBER_OPTION1', Tools::getValue('PWORDERNUMBER_OPTION1'));

            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=6');

        }

        return $output.$this->renderForm();

    }

}

