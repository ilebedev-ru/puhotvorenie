<?php

if (!defined('_PS_VERSION_'))
    exit;

class PwadvancedorderAjaxModuleFrontController extends ModuleFrontController
{
    public $module;

    public function init()
    {

        parent::init();

        if (Tools::isSubmit('method')) {
            $method = Tools::getValue('method');
            if ($method == 'getStep') {
                die(Tools::jsonEncode(array_merge($this->getStep(), $this->getShipping(),  $this->getPayments())));
            } elseif ($method == 'changeState') {
                die($this->changeState());
            }
        }
        die;
    }
    
    protected function changeState()
    {
        $state = Tools::getValue('state', false);
        $this->context->cookie->checkedTOS = $state;
        return $state;
    }
    
    protected function getPayments()
    {
        require_once('order.php');
        $cr = new pwadvancedorderorderModuleFrontController();
        return array('HOOK_PAYMENT' => $cr->_getPaymentMethods());
    }
    
    protected function getShipping()
    {
        require_once('order.php');
        $cr = new pwadvancedorderorderModuleFrontController();
        return $cr->_getCarrierList();
    }
    
    protected function getStep()
    {
        $post = Tools::getValue('data');
        $data = array();
        foreach ($post as $p) {
            $data[$p['name']] = $p['value'];
            $_POST[$p['name']] = $p['value'];
        }
        $step = $data['advStep'];
        if ($step == 'information') {
            return $this->infoStep($data);
        } elseif ($step == 'shipping') {
            return $this->shippingStep($data);
        } elseif ($step == 'selectAddress') {
            return $this->selectAddress($data);
        } elseif ($step == 'customer') {
            return $this->updateCustomer($data);
        } elseif ($step == 'newAddress') {
            return $this->newAddress($data);
        }
    }
    
    protected function newAddress($data)
    {
        $address = new Address();
        $address->id_customer = $this->context->customer->id;
        $address->alias = 'Мой адрес';
        $address->city = Tools::getValue('city', ' ');
        $address->id_country = Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));
        $address->firstname = $this->context->customer->firstname;
        $address->lastname = $this->context->customer->lastname;
        $address->middlename = $this->context->customer->middlename;
        $address->address1 = Tools::getValue('address1', ' ');
        if ($errors = $address->validateController()) {
            return array(
                'is_error' => true,
                'errors' => $errors,
            );
        }
        if (!$address->add()) {
            return array(
                'is_error' => true,
                'errors' => array($this->module->l('Невозможно создать адрес')),
            );
        }
        return array(
            'success' => 1,
            'message' => $this->module->l('Успешно добавлен адрес'),
            'address' => $address,
        );
    }
    
    protected function updateCustomer($data)
    {
        if ($errors = $this->context->customer->validateController()) {
            return array(
                'is_error' => true,
                'errors' => $errors,
            );
        }
        if (!$this->context->customer->update()) {
            return array(
                'is_error' => true,
                'errors' => array(
                    $this->module->l('Невозможно обновить данные покупателя')
                ),
            );
        }
        return array(
            'success' => 1,
            'message' => $this->module->l('Успешно обновлено'),
        );
    }
    
    protected function selectAddress($data)
    {
        if (empty($data['id_address_delivery'])) {
            return array(
                'is_error' => true,
                'errors' => array(
                    $this->module->l('Требуется выбрать адрес')
                ),
            );
        }
        $this->context->cart->id_address_delivery = $data['id_address_delivery'];
        $this->context->cart->id_address_invoice = $data['id_address_delivery'];
        $infos = Address::getCountryAndState((int)$this->context->cart->id_address_delivery);
        if (isset($infos['id_country']) && $infos['id_country']) {
            $country = new Country((int)$infos['id_country']);
            $this->context->country = $country;
        }

        // Address has changed, so we check if the cart rules still apply
        $cart_rules = $this->context->cart->getCartRules();
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
        if ((int)Tools::getValue('allow_refresh')) {
            // If the cart rules has changed, we need to refresh the whole cart
            $cart_rules2 = $this->context->cart->getCartRules();
            if (count($cart_rules2) != count($cart_rules)) {
                $this->ajax_refresh = true;
            } else {
                $rule_list = array();
                foreach ($cart_rules2 as $rule) {
                    $rule_list[] = $rule['id_cart_rule'];
                }
                foreach ($cart_rules as $rule) {
                    if (!in_array($rule['id_cart_rule'], $rule_list)) {
                        $this->ajax_refresh = true;
                        break;
                    }
                }
            }
        }
        if (!$this->context->cart->isMultiAddressDelivery()) {
            $this->context->cart->setNoMultishipping();
        }
        $this->context->cart->update();
        return array(
            'success' => 1,
            'message' => $this->module->l('Успешно изменен адрес доставки'),
        ); 
    }
    
    protected function shippingStep($data)
    {
        $delivery_option = $data['delivery_option'];
        $delivery_option_list = $this->context->cart->getDeliveryOptionList(null, true);
        foreach ($delivery_option_list as $id_address => $options)
        {
            if (isset($options[$delivery_option]))
            {
                $this->context->cart->id_carrier = (int)$delivery_option;
                $this->context->cart->setDeliveryOption(array($id_address => $delivery_option));
                if (isset($this->context->cookie->id_country))
                    unset($this->context->cookie->id_country);
                if (isset($this->context->cookie->id_state))
                    unset($this->context->cookie->id_state);
                break;
            }
        }
        Hook::exec('actionCarrierProcess', array('cart' => $this->context->cart));

        $this->context->cart->update();

        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
        if (Configuration::get('PS_CONDITIONS')) {
            $this->context->cookie->checkedTOS = true;
        }
        return array(
            'success' => 1,
            'message' => $this->module->l('Успешно изменен способ доставки'),
        );
    }
    
    protected function infoStep($data)
    {
        $module = Module::getInstanceByName('pwadvancedorder');
        $fields = array_merge($module->getCustomerFields(), $module->getAddressFields());
        $errors = array();
        foreach ($fields as $key => $field) {
            $name = 'PWADVANCEDORDER_FIELD_'.$key;
            if (!Configuration::hasKey($name)) {
                continue;
            }
            $state = Configuration::get($name);
            if (!$state) {
                continue;
            }
            if ($state == 'required' && (empty($data[$key])||strlen(trim($data[$key]))==0)) {
                $errors[$key] = $this->module->l('Поле '.$field['name'].' необходимо заполнить');
            } elseif(empty($data[$key]) && !empty($field['required'])) {
                $data[$key] = $_POST[$key] = $this->getDefaultValue($key);
            }
        }
        if (!empty($errors)) {
            return array(
                'is_error' => true,
                'errors' => $errors,
            );
        }
//        $isLogged = $this->context->customer->id && Customer::customerIdExistsStatic((int)$this->context->cookie->id_customer);
//        if (!$isLogged && isset($data['email']) && Customer::customerExists($data['email'])) {
//            return array(
//                'is_error' => true,
//                'errors' => array($this->module->l('Данный e-mail уже зарегестрирован')),
//            );
//        }




        $isLogged = $this->context->customer->id && Customer::customerIdExistsStatic((int)  $this->context->cookie->id_customer);

        $customer = new Customer();
        if (!$isLogged && isset($data['email']) && Customer::customerExists($data['email'])) {
            $customer = $customer->getByEmail($data['email']);
//            $this->auth($customer);
//            return array(
//                'is_error' => true,
//                'errors' => array($this->module->l('Данный e-mail уже зарегестрирован')),
//            );
        }
        elseif ($isLogged) {
            $customer = new Customer((int)Context::getContext()->customer->id);
        }





//        $customer = new Customer((int)Context::getContext()->customer->id);
        if ($errors = $customer->validateController()) {
            foreach ($errors as $field => $params) {
                if ($field == 'passwd') {
                    $_POST[$field] = $this->getDefaultValue($field);
                } else {
                    $customer->{$field} = $this->getDefaultValue($field);
                }
            }
        }
        if ($errors = $customer->validateController()) {
            return array(
                'is_error' => true,
                'errors' => $errors,
            );
        }
        if (!$customer->save()) {
            return array(
                'is_error' => true,
                'errors' => array($this->module->l('Не удалось сохранить покупателя')),
            );
        }

        $addresses = $customer->getAddresses($this->context->language->id);
        foreach ($addresses as $a){
            $adr =  new Address($a['id_address']);
            $adr->delete();
        }

        $address = new Address();
        $address->id_customer = $customer->id;
        if ($errors = $address->validateController()) {
            foreach ($errors as $field => $params) {
                $address->{$field} = $this->getDefaultValue($field);
            }
        }
        if ($errors = $address->validateController()) {
            return array(
                'is_error' => true,
                'errors' => $errors,
            );
        }
        if (!$address->add()) {
            return array(
                'is_error' => true,
                'errors' => array($this->module->l('Не удалось создать адрес')),
            );
        }
        $this->auth($customer);
        return array(
            'success' => 1,
            'message' => $this->module->l('Покупатель успешно зарегестрирован'),
        );
    }
    
    protected function auth($customer)
    {
        if(Validate::isLoadedObject($customer)){
            if (method_exists('Context', 'updateCustomer')) {
                $this->context->updateCustomer($customer);
            } else {
                $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
                $this->context->cookie->id_customer = (int)($customer->id);
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->customer_middlename = $customer->middlename;
                $this->context->cookie->logged = 1;
                $customer->logged = 1;
                $this->context->cookie->is_guest = $customer->isGuest();
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->email = $customer->email;
                $this->context->customer = $customer;
                if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
                    $this->context->cart = new Cart($id_cart);
                else
                {
                    $this->context->cart->id_carrier = 0;
                    $this->context->cart->setDeliveryOption(null);
                    $this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
                    $this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
                }
                $this->context->cart->id_customer = (int)$customer->id;
                $this->context->cart->secure_key = $customer->secure_key;
                $this->context->cart->save();
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
                $this->context->cookie->write();
//                $this->context->cart->autosetProductAddress();
                $this->context->cart->autosetProductAddressForcibly();
            }
        }
    }
    
    protected function getDefaultValue($key)
    {
        $fields = array_merge($this->module->getCustomerFields(), $this->module->getAddressFields());
        if ($key == 'id_country') {
            return Configuration::get('PS_COUNTRY_DEFAULT');
        } elseif ($key == 'email') {
            return uniqid('pw').'@'.uniqid().'.ru';
        } elseif ($key == 'postcode') {
            return '000000';
        } elseif ($key == 'alias') {
            return 'Мой адрес';
        } elseif ($key == 'passwd') {
            return Tools::passwdGen();
        }
        if ($fields[$key]['type'] == ObjectModel::TYPE_INT) {
            return 0;
        } elseif($fields[$key]['type'] == ObjectModel::TYPE_FLOAT) {
            return 0;
        } elseif($fields[$key]['type'] == ObjectModel::TYPE_BOOL) {
            return false;
        } elseif($fields[$key]['type'] == ObjectModel::TYPE_STRING) {
            return ' ';
        } else {
            return 0;
        }
    }

}
