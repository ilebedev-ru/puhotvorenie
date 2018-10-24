<?php

class ProstorSMS extends Module
{
    private $_html = '';

    public function __construct()
    {
        $this->name = 'prostorsms';
        $this->tab = 'administration';
        $this->version = '0.4.0';
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;

        parent::__construct();

        $this->bootstrap = true;
        $this->displayName = $this->l('Prostor SMS');
        $this->description = $this->l('Sends an SMS for each new order and new user');

        if (!Configuration::get('PSMS_LOGIN'))
            $this->warning = $this->l('Please add login');
        if (!Configuration::get('PSMS_PASSWORD'))
            $this->warning = $this->l('Please add password');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('newOrder') ||
            !$this->registerHook('createAccount') ||
            !$this->registerHook('postUpdateOrderStatus'))
            return false;

        Configuration::updateValue('PSMS_NEW_ORDER', 'Заказ #{id_order} на сумму {total} руб. оформлен.');
        return true;

    }

    public function uninstall()
    {
        Configuration::deleteByName('PSMS_LOGIN');
        Configuration::deleteByName('PSMS_PASSWORD');
        Configuration::deleteByName('PSMS_SENDER');

        Configuration::deleteByName('PSMS_SEND_CUSTOMER');
        Configuration::deleteByName('PSMS_SEND_ADM');
        Configuration::deleteByName('PSMS_TEL_ADM');
        Configuration::deleteByName('PSMS_TEL_MANAGER');
        
        Configuration::deleteByName('PSMS_USE_AS_MODULE_AUTH');
        Configuration::deleteByName('PSMS_USE_ON_ORDER_STATE_CHANGE');

        return parent::uninstall();
    }

    public function getContent()
    {
        $this->postProcess();
        return $this->renderForm();
    }

    public function hookNewOrder($params)
    {
        require_once (dirname(__FILE__).'/classes/ProstorSMS.php');
        $api = new PSMS(Configuration::get('PSMS_LOGIN'), Configuration::get('PSMS_PASSWORD'));
        $order = $params['order'];
        $cart = $params['cart'];
        if (!Configuration::get('PSMS_LOGIN') || !Configuration::get('PSMS_PASSWORD'))
            return ;    

        $customer = $params['customer'];
        $currency = $params['currency'];
        $status = $params['orderStatus'];
        $address_delivery = new Address($order->id_address_delivery);
        if (!empty($address_delivery->phone_mobile)) {
            $phone = $address_delivery->phone_mobile;
        } else {
            $phone = $address_delivery->phone;
        }



        $template = Configuration::get('PSMS_NEW_ORDER');
        $content = str_replace('{id_order}', sprintf("%06d", (int)($order->id)), $template);
        $content = str_replace('{total}', number_format($cart->getOrderTotal(true, Cart::BOTH), 0, '.', ''), $content);
        if (Configuration::get('PSMS_SEND_CUSTOMER') && $phone) {
            $api->sendSms($phone, $content, Configuration::get('PSMS_SENDER'));
        }

        if (Configuration::get('PSMS_SEND_ADM')) {
            $api->sendSms(Configuration::get('PSMS_TEL_ADM'), $content, Configuration::get('PSMS_SENDER'));
        }
    }
    
    public function hookpostUpdateOrderStatus($params)
    {
      if (!(bool)Configuration::get('PSMS_USE_ON_ORDER_STATE_CHANGE')) {
          return false;
      }
      require_once (dirname(__FILE__).'/classes/ProstorSMS.php');
      $api = new PSMS(Configuration::get('PSMS_LOGIN'), Configuration::get('PSMS_PASSWORD'));
      $id_status = (int)$params['newOrderStatus']->id;
      $file = _PS_ROOT_DIR_.'/modules/prostorsms/sms/'.$id_status.'.txt';
      if (file_exists($file)) {
        $content = trim(file_get_contents($file));
        $order = new Order($params['id_order']);
        $content = str_replace('{id_order}', $order->id, $content);
        $addressDelivery = new Address($order->id_address_delivery, $order->id_lang);
        if ($addressDelivery->phone_mobile) {
            $phone = $addressDelivery->phone_mobile;
        } else {
            $phone = $addressDelivery->phone;
        }
            $return = $api->sendSms($phone, $content, Configuration::get('PSMS_SENDER'));
        }
    }



    public function hookCreateAccount($params)
    {
        if (!Configuration::get('PSMS_USE_AS_MODULE_AUTH'))
            return;

        $cookie = $this->context->cookie;
        $cart = $this->context->cart;
        
        require_once (dirname(__FILE__).'/classes/ProstorSMS.php');
        $api = new PSMS(Configuration::get('PSMS_LOGIN'), Configuration::get('PSMS_PASSWORD'));

        if (Validate::isPhoneNumber(substr($params['cookie']->email, 0, -(strlen ('@' .Tools::getShopDomain(false))))))
            $api->sendSms(substr($params['cookie']->email, 0, -(strlen ('@' .Tools::getShopDomain(false)))), 'Вы зарегестрировались в магазине ' .strtoupper(Tools::getShopDomain(false)). '.' . ' Ваш пароль: ' . $params['_POST']['passwd'], Configuration::get('PSMS_SENDER'));
    }
    
    private function postProcess()
    {
        if (Tools::isSubmit('submit'.$this->name))
        {
            Configuration::updateValue('PSMS_LOGIN', Tools::getValue('PSMS_LOGIN'));
            Configuration::updateValue('PSMS_PASSWORD', Tools::getValue('PSMS_PASSWORD'));
            Configuration::updateValue('PSMS_SENDER', Tools::getValue('PSMS_SENDER'));
            Configuration::updateValue('PSMS_TEL_ADM', Tools::getValue('PSMS_TEL_ADM'));

            Configuration::updateValue('PSMS_SEND_CUSTOMER', (int)Tools::getValue('PSMS_SEND_CUSTOMER'));
            Configuration::updateValue('PSMS_SEND_ADM', (int)Tools::getValue('PSMS_SEND_ADM'));
            Configuration::updateValue('PSMS_USE_AS_MODULE_AUTH', (int)Tools::getValue('PSMS_USE_AS_MODULE_AUTH'));
            Configuration::updateValue('PSMS_USE_ON_ORDER_STATE_CHANGE', (int)Tools::getValue('PSMS_USE_ON_ORDER_STATE_CHANGE'));
            
            Configuration::updateValue('PSMS_NEW_ORDER', Tools::getValue('PSMS_NEW_ORDER'));
        }
    }
    
    private function renderForm()
    {
        require_once (dirname(__FILE__).'/classes/ProstorSMS.php');
        $example = strtoupper(Tools::getShopDomain(false));
        $bal = array();
        if (Configuration::get('PSMS_LOGIN') && Configuration::get('PSMS_PASSWORD')) {
            $api = new PSMS(Configuration::get('PSMS_LOGIN'), Configuration::get('PSMS_PASSWORD'));
            $availableSenders = $api->getAvailableSenders();
            $example = array();
            $senders = explode("\n", $availableSenders);
            foreach ($senders as $sender) {
                $send = explode(';', $sender);
                $example[] = $send[0];
            }
            $example = implode(', ', $example);
            $balance = $api->getBalance();
            $invoices = explode("\n", $balance);
            foreach ($invoices as $invoice) {
                $row = explode(';', $invoice);
                $bal[] = $row[1].' '.$row[0];
            }
        }
        $bal = implode('<br />', $bal);
        $default_lang = $this->context->language->id;
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        $fields_form = array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                    ),
                    'input' => array(
                        array(
                            'type'  => 'text',
                            'label' => $this->l('LOGIN'),
                            'name'  => 'PSMS_LOGIN',
                            'required' => true
                        ),
                        array(
                            'type'  => 'text',
                            'label' => $this->l('PASSWORD'),
                            'name'  => 'PSMS_PASSWORD',
                            'required' => true
                        ),
                        array(
                            'type'  => 'text',
                            'label' => $this->l('SENDER'),
                            'name'  => 'PSMS_SENDER',
                            'desc'  => $this->l('Allowed characters: letters and numbers (from 5 - 11). Sample:').$example,
                        ),
                        array(
                            'type'  => 'text',
                            'label' => $this->l('TEL ADMINISTRATOR'),
                            'name'  => 'PSMS_TEL_ADM',
                            'form_group_class' => (bool)Configuration::get('PSMS_SEND_ADM')?'':'hidden',
                            'desc'  => $this->l('Sample: 7900000000'),
                        ),
                        array(
                            'type'  => 'switch',
                            'label' => $this->l('SEND CUSTOMER'),
                            'name'  => 'PSMS_SEND_CUSTOMER',
                            'required' => false,
                            'is_bool'  => true,
                            'values'   => array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                        ),
                        array(
                            'type'  => 'switch',
                            'label' => $this->l('SEND ADMINISTRATOR'),
                            'name'  => 'PSMS_SEND_ADM',
                            'required' => false,
                            'is_bool'  => true,
                            'values'   => array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                        ),
                        /*array(
                            'type'  => 'switch',
                            'label' => $this->l('USE AS MODULE AUTH'),
                            'name'  => 'PSMS_USE_AS_MODULE_AUTH',
                            'required' => false,
                            'is_bool'  => true,
                            'values'   => array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                        ),
                        array(
                            'type'  => 'switch',
                            'label' => $this->l('Использовать при изменении статуса заказа'),
                            'name'  => 'PSMS_USE_ON_ORDER_STATE_CHANGE',
                            'required' => false,
                            'is_bool'  => true,
                            'values'   => array(
                                array(
                                    'id'    => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id'    => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                        ),*/
                    ),
                    'submit'    => array(
                        'title' => $this->l('Save'),
                        'class' => 'button'
                    ),
                ),
            ),
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Шаблоны'),
                    ),
                    'input' => array(
                        array(
                            'type'  => 'text',
                            'name'  => 'PSMS_NEW_ORDER',
                            'label' => $this->l('Сообщение при создании заказа'),
                            'desc'  => 'Используйте шорткод {id_order} и {total} в месте, где хотите вставить номер заказа'
                        ),
                    ),
                    'submit'    => array(
                        'title' => $this->l('Save'),
                        'class' => 'button'
                    ),
                ),
            )
            /*,array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Connect'),
                    ),
                    'description' => $this->l('Для использования модуля пройдите регистрацию на сайте http://prostor-sms.ru.'),
                    'input' => array(
                        array(
                            'type'  => 'html',
                            'name'  => 'auth',
                            'label' => $this->l('Использовать модуль для авторизации'),
                            'html_content' => '<div class="alert alert-message">'.$this->l('Требуется установка модификаторора').'</div>',
                        ),
                        array(
                            'type'  => 'html',
                            'name'  => 'description',
                            'label' => $this->l('Описание'),
                            'html_content' => '<div class="alert alert-message">'.$this->l('Регистрация и авторизация в магазине используя номер телефона как эл.адрес, при регистрации покупатель вводит номер своего телефона и автоматически регистрируется на сайте').'</div>',
                        ),
                        array(
                            'type'  => 'html',
                            'name'  => 'balance',
                            'form_group_class' => empty($bal)?'hidden':'',
                            'label' => $this->l('Баланс'),
                            'html_content' => $bal,
                        ),
                    ),
                ),
            ),*/
        );
        $helper->fields_value = Configuration::getMultiple(array(
            'PSMS_LOGIN', 'PSMS_PASSWORD', 'PSMS_SENDER', 'PSMS_TEL_ADM', 'PSMS_SEND_CUSTOMER', 'PSMS_SEND_ADM', 'PSMS_USE_AS_MODULE_AUTH',
            'PSMS_USE_ON_ORDER_STATE_CHANGE', 'PSMS_NEW_ORDER',
        ));
        return $helper->generateForm($fields_form);
    }

}