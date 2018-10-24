<?php
if (!defined('_PS_VERSION_'))
    exit;

class PwBackCall extends Module
{
    public $html;

    public $isShowButton = false;
    
    public function __construct()
    {
        $this->tab              = 'other';
        $this->name             = 'pwbackcall';
        $this->author           = 'PrestaWeb.ru';
        $this->version          = '2.0.0';
        $this->bootstrap        = true;
        $this->need_instance    = 0;
        $this->only_pwbackcall  = true;

        parent::__construct();

        $this->displayName = "Обратная связь";
        $this->description = "Обратный звонок и вопросы на почту";
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->isShowButton = (Configuration::get('PW_BACK_ENABLE') == '1') ? true : false;
    }

    public function install()
    {
        if ( !parent::install()
            || !$this->registerHook('footer')
            || !$this->registerHook('header')
            || !Configuration::updateValue('PW_BACK_JS', '')
            || !Configuration::updateValue('PW_BACK_ENABLE', 0)
            || !Configuration::updateValue('PW_BACK_FORM_CAPTION', 'Обратный звонок')
            || !Configuration::updateValue('PW_BACK_FORM_EXTENDED', 0)
            || !Configuration::updateValue('PW_BACK_QUEST_MESSAGE', 'Скоро ответим')
            || !Configuration::updateValue('PW_BACK_CALL_MESSAGE', 'Мы вам перезвоним')
            || !Configuration::updateValue('PW_BACK_EMAILS', Configuration::get('PS_SHOP_EMAIL'))
        )
            return false;
        return true;
    }
    
    public function uninstall()
    {
        //$this->_clearCache('pwbackcall.tpl');
        if ( !parent::uninstall()
            || !$this->unregisterHook('footer')
            || !$this->unregisterHook('header')
            || !Configuration::deleteByName('PW_BACK_JS')
            || !Configuration::deleteByName('PW_BACK_EMAILS')
            || !Configuration::deleteByName('PW_BACK_ENABLE')
            || !Configuration::deleteByName('PW_BACK_FORM_CAPTION')
            || !Configuration::deleteByName('PW_BACK_CALL_MESSAGE')
            || !Configuration::deleteByName('PW_BACK_QUEST_MESSAGE')
            || !Configuration::deleteByName('PW_BACK_FORM_EXTENDED')
        )
            return false;
        return true;
    }

    public function hookHeader($params)
    {

        if ($this->isShowButton) {
            $this->context->controller->addJqueryUI('ui.draggable');
        }
        
        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->controller->addCSS(($this->_path).'views/css/pwbackcall.css', 'all');
        $this->context->controller->addJS(($this->_path).'views/js/pwbackcall.js');
    }

    public function getContent()
    {        
        if (Tools::isSubmit('submitpwbackcall')) {
            
            $errors = array();

            $emails = $this->prepareEmails(Tools::getValue('PW_BACK_EMAILS'));

            if (empty($emails)) {
                $errors[] = $this->l('Не корректный E-mail');
            } else {

                if ( !Configuration::updateValue('PW_BACK_JS', Tools::getValue('PW_BACK_JS'))
                    || !Configuration::updateValue('PW_BACK_EMAILS', implode(PHP_EOL, $emails))
                    || !Configuration::updateValue('PW_BACK_ENABLE', Tools::getValue('PW_BACK_ENABLE'))
                    || !Configuration::updateValue('PW_BACK_FORM_CAPTION', Tools::getValue('PW_BACK_FORM_CAPTION'))
                    || !Configuration::updateValue('PW_BACK_CALL_MESSAGE', Tools::getValue('PW_BACK_CALL_MESSAGE'))
                    || !Configuration::updateValue('PW_BACK_QUEST_MESSAGE', Tools::getValue('PW_BACK_QUEST_MESSAGE'))
                    || !Configuration::updateValue('PW_BACK_FORM_EXTENDED', Tools::getValue('PW_BACK_FORM_EXTENDED'))
                ) {
                    $errors[] = $this->l('Произошла ошибка');
                }
            }

            if (!empty($errors)) {
                $this->html .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->html .= $this->displayConfirmation($this->l('Настройки обновлены'));
            }
        }
        
        return $this->html.$this->displayForm();
    }

    public function displayForm()
    {
        $helper = new HelperOptions();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
        $fields_options = array(
            'general' => array(
                'title' => $this->l('Общие'),
                'icon' =>    'icon-cogs',
                'fields' => array(
                    'PW_BACK_ENABLE' => array(
                        'title' => $this->l('Показывать кнопку'),
                        'cast'  => 'boolval',
                        'type'  => 'bool'
                    ),
                    'PW_BACK_FORM_EXTENDED' => array(
                        'title' => $this->l('Тип формы'),
                        'cast'  => 'intval',
                        'type'  => 'radio',
                        'choices' => array(
                            0 => 'Простая',
                            1 => 'расширеная'
                        ),
                        'validation' => 'isBool',
                        'hint' => 'Простая - одно поле. Расширенная - три поля.'
                    ),
                    'PW_BACK_FORM_CAPTION' => array(
                        'title' => $this->l('Заголовок формы'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_BACK_CALL_MESSAGE' => array(
                        'title' => $this->l('Сообщение при запросе обратного звонка'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),
                    'PW_BACK_QUEST_MESSAGE' => array(
                        'title' => $this->l('Сообщение при задавании вопроса'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),
                    'PW_BACK_EMAILS' => array(
                        'title' => $this->l('Отправлять запросы на почтовые ящики:'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),
                    'PW_BACK_JS' => array(
                        'title' => $this->l('JavaScript код:'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval',
                        'hint'  => $this->l('Код выполнится после успешной отправки данных')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                    'name'  => 'submitpwbackcall'
                )
            )
        );
        
        return $helper->generateOptions($fields_options);
    }
    
    public function hookFooter($params)
    {

        $showbutton = (!$this->isShowButton) ? false : true;

        $pwbackcall = array(
            'link'      => $this->context->link->getModuleLink($this->name, 'ajax'),
            'extended'  => Configuration::get('PW_BACK_FORM_EXTENDED'),
            'caption'   => Configuration::get('PW_BACK_FORM_CAPTION'),
        );

        $this->context->smarty->assign(array(
            'showbutton' => $showbutton,
            'pwbackcall' => $pwbackcall,
            'backcalljs'    => Configuration::get('PW_BACK_JS'),
            'backcallMessage' => Configuration::get('PW_BACK_CALL_MESSAGE'),
            'backquestMessage' => Configuration::get('PW_BACK_QUEST_MESSAGE'),
        ));

        return $this->display(__FILE__, 'pwbackcall.tpl');
    }

    public function prepareEmails($emails)
    {
        $result = array();
        $emails = preg_split('/\\r\\n?|\\n/', $emails);

        foreach ($emails as $email) {
            if ($email !== '' && Validate::isEmail($email)) {
                $result[] = trim($email);
            }
        }
        return $result;
    }
}


