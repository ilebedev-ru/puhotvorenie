<?php


class PWBackForm extends Module
{
    /**
     * @var
     */
    public $html;

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @var array
     */
    public $errors = array();

    /**
     * PWBackForm constructor.
     */
    public function __construct()
    {
        $this->tab              = 'other';
        $this->name             = 'pwbackform';
        $this->author           = 'PrestaWeb.ru';
        $this->version          = '1.0.0';
        $this->bootstrap        = true;
        $this->need_instance    = 0;

        parent::__construct();

        $this->displayName = $this->l("Форма задать вопрос");
        $this->description = $this->l("Вопросы на почту");
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->fields = array(
            'name' => array(
                'field' => 'name',
                'label' =>$this->l('Имя')
            ),
            'email' => array(
                'field' => 'email',
                'label' => $this->l('Почта')
            ),
            'files' => array(
                'field' => 'files[]',
                'label' => $this->l('Файлы')
            ),
            'phone' => array(
                'field' => 'phone',
                'label' => $this->l('Телефон')
            ),
            'message' => array(
                'field' => 'message',
                'label' => $this->l('Сообщение')
            ),
        );
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function install()
    {
        if ( !parent::install()
            || !$this->registerHook('footer')
            || !$this->registerHook('header')
            || !Configuration::updateValue('PW_BF_JS', '')
            || !Configuration::updateValue('PW_BF_CAPTION', 'Задать вопрос')
            || !Configuration::updateValue('PW_BF_MESSAGE', 'Скоро ответим')
            || !Configuration::updateValue('PW_BF_TEXT', 'Дополнительная информация')
            || !Configuration::updateValue('PW_BF_SELECTOR', 'uipw-form_question_modal')
            || !Configuration::updateValue('PW_BF_EMAILS', Configuration::get('PS_SHOP_EMAIL'))
        ) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if ( !parent::uninstall()
            || !$this->unregisterHook('footer')
            || !$this->unregisterHook('header')
            || !Configuration::deleteByName('PW_BF_JS')
            || !Configuration::deleteByName('PW_BF_TEXT')
            || !Configuration::deleteByName('PW_BF_EMAILS')
            || !Configuration::deleteByName('PW_BF_CAPTION')
            || !Configuration::deleteByName('PW_BF_MESSAGE')
            || !Configuration::deleteByName('PW_BF_SELECTOR')
        ) {
            return false;
        }
        return true;
    }

    /**
     * Update module settings if form submit.
     * Show settings form.
     * Show success or fail message.
     *
     * @return string
     */
    public function getContent()
    {
        if ($this->isPost()) {
            $emails = $this->prepareEmails(Tools::getValue('PW_BF_EMAILS'));

            if (empty($emails)) {
                $this->errors[] = $this->l('Не корректный список E-mail адресов');
            } else {
                if ( !Configuration::updateValue('PW_BF_JS', Tools::getValue('PW_BF_JS'))
                    || !Configuration::updateValue('PW_BF_EMAILS', $emails)
                    || !Configuration::updateValue('PW_BF_TEXT', Tools::getValue('PW_BF_TEXT'))
                    || !Configuration::updateValue('PW_BF_CAPTION', Tools::getValue('PW_BF_CAPTION'))
                    || !Configuration::updateValue('PW_BF_MESSAGE', Tools::getValue('PW_BF_MESSAGE'))
                    || !Configuration::updateValue('PW_BF_SELECTOR', Tools::getValue('PW_BF_SELECTOR'))
                ) {
                    $this->errors[] = $this->l('Не удалось обновить настройки');
                }
            }

            if (!empty($this->errors)) {
                $this->html .= $this->displayError(implode('<br />', $this->errors));
            } else {
                $this->html .= $this->displayConfirmation($this->l('Настройки обновлены'));
            }
        }

        return $this->html.$this->displayForm();
    }

    /**
     * Generate settings form
     *
     * @return string
     */
    public function displayForm()
    {
        $helper = new HelperOptions();
        $helper->module = $this;
        $helper->bootstrap = true;
        $helper->show_toolbar = true;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $fields_options = array(
            'general' => array(
                'title' => $this->l('Общие'),
                'icon' =>    'icon-cogs',
                'fields' => array(
                    'PW_BF_CAPTION' => array(
                        'title' => $this->l('Заголовок формы'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_BF_MESSAGE' => array(
                        'title' => $this->l('Текст сообщения о успешно отправленной форме'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),
                    'PW_BF_TEXT' => array(
                        'title' => $this->l('Дополнительный текст'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),
                    'PW_BF_SELECTOR' => array(
                        'title' => $this->l('Слектор формы'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_BF_JS' => array(
                        'title' => $this->l('Дополнительный JavaScript код'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),

                    'PW_BF_EMAILS' => array(
                        'title' => $this->l('Список E-mail адресов'),
                        'type'  => 'textarea',
                        'rows'  => 5,
                        'cols'  => 40,
                        'cast'  => 'strval'
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            )
        );

        return $helper->generateOptions($fields_options);
    }

    /**
     * @param $params
     */
    public function hookHeader($params)
    {
        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->controller->addJS(($this->_path).'views/js/pwbackform.js');
        $this->context->controller->addCSS(($this->_path).'views/css/pwbackform.css', 'all');
    }

    /**
     * @param $params
     * @return string
     */
    public function hookFooter($params)
    {

        $customer = array();

        if ($this->context->customer->isLogged()) {
            $email = $this->context->customer->email;
            $address = $this->context->customer->getAddresses($this->context->language->id);
            $phone = !empty($address[0]['phone']) ? $address[0]['phone'] :
                        !empty($address[0]['phone_mobile']) ? $address[0]['phone_mobile'] : '';

            $customer = array(
                'email' => $email,
                'phone' => $phone
            );
        }

        $this->context->smarty->assign(array(
            'text'          => Configuration::get('PW_BF_TEXT'),
            'fields'        => $this->fields,
            'formjs'        => Configuration::get('PW_BF_JS'),
            'action'        => $this->context->link->getModuleLink($this->name, 'ajax'),
            'caption'       => Configuration::get('PW_BF_CAPTION'),
            'message'       => Configuration::get('PW_BF_MESSAGE'),
            'selector'      => Configuration::get('PW_BF_SELECTOR'),
            'customer'      => $customer,
            'javascript'    => Configuration::get('PW_BF_JS')
        ));

        return $this->display(__FILE__, 'footer.tpl');
    }

    /**
     * @param $emails
     * @return string
     */
    public function prepareEmails($emails)
    {
        $result = array();
        $emails = preg_split('/\\r\\n?|\\n/', $emails);

        foreach ($emails as $email) {
            if ($email !== '' && Validate::isEmail($email)) {
                $result[] = trim($email);
            }
        }
        return implode(PHP_EOL, $result);
    }

    /**
     * Check request post method
     *
     * @return bool
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'POST') ? true : false;
    }
}


