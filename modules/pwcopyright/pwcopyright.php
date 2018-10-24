<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwcopyright extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = "Отображает копирайт";
        $this->description = "Prestaweb.ru или Altopromo.com";
    }

    public function install()
    {
        Configuration::updateValue('PWCOPYRIGHT_OPTION1', 2); //Prestaweb.ru dark
        Configuration::updateValue('PWCOPYRIGHT_TEXT', 'Разработка и продвижение сайта'); //
        Configuration::updateValue('PWCOPYRIGHT_NOFOLLOW', 0); //Prestaweb.ru dark
        return (parent::install() AND $this->registerHook('displayHeader') AND $this->registerHook('footerCopyright'));
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
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Какой копирайт показывать'),
                        'name' => 'PWCOPYRIGHT_OPTION1',
                        'hint' => $this->l('Выберите, какую картинку показывать'),
                        'values' => array(
                            array(
                                'id' => 'option1',
                                'value' => 1,
                                'label' => $this->l('PrestaWeb.ru light')
                            ),
                            array(
                                'id' => 'option2',
                                'value' => 2,
                                'label' => $this->l('PrestaWeb.ru dark')
                            ),
                            array(
                                'id' => 'option2',
                                'value' => 3,
                                'label' => $this->l('Altopromo.com light')
                            ),
                            array(
                                'id' => 'option3',
                                'value' => 4,
                                'label' => $this->l('Altopromo.com dark')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Текст под копирайтом'),
                        'name' => 'PWCOPYRIGHT_TEXT',
                        'hint' => $this->l('Выберите, какую картинку показывать')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('rel=nofollow'),
                        'name' => 'PWCOPYRIGHT_NOFOLLOW',
                        'desc' => $this->l('Отключает ссылку от поисковиокв'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Включить')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Выключить')
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
        $helper->submit_action = 'submitPWCOPYRIGHT';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
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
            'PWCOPYRIGHT_OPTION1' => Tools::getValue('PWCOPYRIGHT_OPTION1', Configuration::get('PWCOPYRIGHT_OPTION1')),
            'PWCOPYRIGHT_TEXT' => Tools::getValue('PWCOPYRIGHT_TEXT', Configuration::get('PWCOPYRIGHT_TEXT')),
            'PWCOPYRIGHT_NOFOLLOW' => Tools::getValue('PWCOPYRIGHT_NOFOLLOW', Configuration::get('PWCOPYRIGHT_NOFOLLOW'))
        );
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitPWCOPYRIGHT')) {
            $maxDepth = (int)(Tools::getValue('PWCOPYRIGHT_OPTION1'));
            if ($maxDepth < 0)
                $output .= $this->displayError($this->l('Опция не прошла проверку, убирите её из кода если не нужна'));
            else {
                Configuration::updateValue('PWCOPYRIGHT_OPTION1', Tools::getValue('PWCOPYRIGHT_OPTION1'));
                Configuration::updateValue('PWCOPYRIGHT_TEXT', Tools::getValue('PWCOPYRIGHT_TEXT'));
                Configuration::updateValue('PWCOPYRIGHT_NOFOLLOW', Tools::getValue('PWCOPYRIGHT_NOFOLLOW'));
                Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&conf=6');
            }
        }
        return $output . $this->renderForm();
    }

    //end_helper


    public function hookdisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . $this->name . '.css', 'all');
        //$this->context->controller->addJS($this->_path.$this->name.'.js');
    }

    public function hookdisplayFooter($params)
    {
        switch (Configuration::get('PWCOPYRIGHT_OPTION1')) {
            default:
            case 1:
                $domain = "prestaweb.ru";
                $type = "light";
                break;
            case 2:
                $domain = "prestaweb.ru";
                $type = "dark";
                break;
            case 3:
                $domain = "altopromo.com";
                $type = "light";
                break;
            case 4:
                $domain = "altopromo.com";
                $type = "dark";
                break;
        }
        $pwcopyright = Array(
            'domain' => $domain,
            'type' => $type,
            'text' => Configuration::get('PWCOPYRIGHT_TEXT'),
            'nofollow' => Configuration::get('PWCOPYRIGHT_NOFOLLOW')
        );
        $this->context->smarty->assign(Array(
            'pwcopyright' => $pwcopyright
        ));
        return $this->display(__FILE__, 'pwcopyright.tpl');
    }

    public function hookfooterCopyright($params)
    {
        return $this->hookdisplayFooter($params);
    }

}


