<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwmicrobreadcrumbs extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.2;
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Микроразметка в хлебных крошках");
        $this->description = $this->l("Модуль интеграции микроразметки в хлебные крошки");
        
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if ( !parent::install() OR !$this->registerHook('displayHeader')) return false;

        if (file_exists (__DIR__.'/dump/breadcrumb.tpl'))
            unlink(__DIR__.'/dump/breadcrumb.tpl');

        copy(_PS_THEME_DIR_.'breadcrumb.tpl', __DIR__.'/dump/breadcrumb.tpl');

        unlink(_PS_THEME_DIR_.'breadcrumb.tpl');
        copy(__DIR__.'/pwmicrobreadcrumbs.tpl', _PS_THEME_DIR_.'breadcrumb.tpl');

        return true;
    }
    
    public function uninstall()
    {
        if (file_exists (__DIR__.'/dump/breadcrumb.tpl')){
            unlink(_PS_THEME_DIR_.'breadcrumb.tpl');
            copy(__DIR__.'/dump/breadcrumb.tpl', _PS_THEME_DIR_.'breadcrumb.tpl');
        }

        if (!parent::uninstall()) return false;
        return true;
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
                        'type' => 'text',
                        'label' => $this->l('Разделитель'),
                        'name' => 'PWMICROBREADCRUMBS_PIPE',
                        'desc' => $this->l('Разделитель между элементами хлебных крошек'),
                    ),
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
        $helper->submit_action = 'submitPWMICROBREADCRUMBS';
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
            'PWMICROBREADCRUMBS_PIPE' => Tools::getValue('PWMICROBREADCRUMBS_PIPE', Configuration::get('PWMICROBREADCRUMBS_PIPE')),
        );
    }
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitPWMICROBREADCRUMBS'))
        {
                Configuration::updateValue('PWMICROBREADCRUMBS_PIPE', Tools::getValue('PWMICROBREADCRUMBS_PIPE'));
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=6');

        }
        return $output.$this->renderForm();
    }


    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'pwmicrobreadcrumbs.css', 'all');

        if(Configuration::get('PWMICROBREADCRUMBS_PIPE') && count(Configuration::get('PWMICROBREADCRUMBS_PIPE')))
        {
            $this->context->controller->addJS($this->_path.'pwmicrobreadcrumbs.js');
            $this->smarty->assign(array(
                'pwmicrobreadcrumbspipe' => Configuration::get('PWMICROBREADCRUMBS_PIPE'))
            );
        }
        
        return $this->display($this->_path, 'header.tpl');
    }
    
}


