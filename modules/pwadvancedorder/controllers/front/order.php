<?php

if (!defined('_PS_VERSION_'))
    exit;

class pwadvancedorderorderModuleFrontController extends ParentOrderController
{
    public $module;
    public $php_self = 'module-pwadvancedorder-order';
    public $isLogged;

    public function init()
    {
        $this->isLogged = $this->context->customer->id && Customer::customerIdExistsStatic((int)$this->context->cookie->id_customer);

        FrontController::init();

        /* Disable some cache related bugs on the cart/order */
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        $this->nbProducts = $this->context->cart->nbProducts();

        if (Configuration::get('PS_CATALOG_MODE')) {
            $this->errors[] = Tools::displayError('This store has not accepted your new order.');
        }
        if (Tools::isSubmit('submitReorder') && $id_order = (int)Tools::getValue('id_order')) {
            $oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
            $duplication = $oldCart->duplicate();
            if (!$duplication || !Validate::isLoadedObject($duplication['cart'])) {
                $this->errors[] = Tools::displayError('Sorry. We cannot renew your order.');
            } elseif (!$duplication['success']) {
                $this->errors[] = Tools::displayError('Some items are no longer available, and we are unable to renew your order.');
            } else {
                $this->context->cookie->id_cart = $duplication['cart']->id;
                $context = $this->context;
                $context->cart = $duplication['cart'];
				CartRule::autoAddToCart($context);
				
				$products=$context->cart->getProducts();
				foreach($products as $product)
				{
					if($product['id_product']==706)
					{
						$context->cookie->__set('hasdiscount',true);
					}
				}
				
                $this->context->cookie->write();
				
                Tools::redirect('index.php?controller=order&fc=module&module=pwadvancedorder');
                
            }
        }
        if ($this->nbProducts) {
            if (CartRule::isFeatureActive()) {
                if (Tools::isSubmit('submitAddDiscount')) {
                    if (!($code = trim(Tools::getValue('discount_name')))) {
                        $this->errors[] = Tools::displayError('You must enter a voucher code.');
                    } elseif (!Validate::isCleanHtml($code)) {
                        $this->errors[] = Tools::displayError('The voucher code is invalid.');
                    } else {
                        if (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule)) {
                            if ($error = $cartRule->checkValidity($this->context, false, true)) {
                                $this->errors[] = $error;
                            } else {
                                $this->context->cart->addCartRule($cartRule->id);
                                CartRule::autoAddToCart($this->context);
                                Tools::redirect('index.php?controller=order&fc=module&module=pwadvancedorder&addingCartRule=1');
                            }
                        } else {
                            $this->errors[] = Tools::displayError('This voucher does not exists.');
                        }
                    }
                    $this->context->smarty->assign(array(
                        'errors' => $this->errors,
                        'discount_name' => Tools::safeOutput($code)
                    ));
                } elseif (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule)) {
                    $this->context->cart->removeCartRule($id_cart_rule);
                    CartRule::autoAddToCart($this->context);
                    Tools::redirect('index.php?controller=order&fc=module&module=pwadvancedorder');
                }
            }
            /* Is there only virtual product in cart */
            if ($isVirtualCart = $this->context->cart->isVirtualCart()) {
                $this->setNoCarrier();
            }
            $this->context->smarty->assign('virtual_cart', $this->context->cart->isVirtualCart());
        }
        $this->context->smarty->assign('is_multi_address_delivery', $this->context->cart->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1));
        $this->context->smarty->assign('open_multishipping_fancybox', (int)Tools::getValue('multi-shipping') == 1);
        $this->context->smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
    }
    
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->display_footer = (bool)Configuration::get('PWADVANCEDORDER_FOOTER');
        parent::initContent();
        
        /* id_carrier is not defined in database before choosing a carrier, set it to a default one to match a potential cart _rule */
        if (empty($this->context->cart->id_carrier)) {
            $checked = $this->context->cart->simulateCarrierSelectedOutput();
            $checked = ((int)Cart::desintifier($checked));
            $this->context->cart->id_carrier = $checked;
            $this->context->cart->update();
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);
        }

        // SHOPPING CART
        $this->_assignSummaryInformations();
        // WRAPPING AND TOS
        $this->_assignWrappingAndTOS();

        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        // If a rule offer free-shipping, force hidding shipping prices
        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }

        $this->context->smarty->assign(array(
            'free_shipping' => $free_shipping,
            'isGuest' => isset($this->context->cookie->is_guest) ? $this->context->cookie->is_guest : 0,
            'countries' => $countries,
            'sl_country' => (int)Tools::getCountry(),
            'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
            'errorCarrier' => Tools::displayError('You must choose a carrier.', false),
            'errorTOS' => Tools::displayError('You must accept the Terms of Service.', false),
            'isPaymentStep' => isset($_GET['isPaymentStep']) && $_GET['isPaymentStep'],
            'genders' => Gender::getGenders(),
            'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
            'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
            'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
        ));
        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();
        $this->context->smarty->assign(array(
            'years' => $years,
            'months' => $months,
            'days' => $days,
        ));

        /* Load guest informations */
        if ($this->isLogged && $this->context->cookie->is_guest) {
            $this->context->smarty->assign('guestInformations', $this->_getGuestInformations());
        }
        // ADDRESS
        if ($this->isLogged) {
            $this->_assignAddress();
        }
        $this->context->smarty->assign('customer', $this->context->customer);
        // CARRIER
        $this->_assignCarrier();
        // PAYMENT
        $this->_assignPayment();
        Tools::safePostVars();

        $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));
        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());

        $this->_processAddressFormat();
        
        $this->context->smarty->assign('show_head', (bool)Configuration::get('PWADVANCEDORDER_HEADER'));
        $this->context->smarty->assign('show_one_click', (bool)Configuration::get('PWADVANCEDORDER_ONECLICK'));
        $this->context->smarty->assign('isLogged', (bool)$this->isLogged);
        
        $module = Module::getInstanceByName('pwadvancedorder');
        $fields = array_merge($module->getCustomerFields(), $module->getAddressFields());
        $smarty_fields = array();
        foreach ($fields as $key => $field) {
            $name = 'PWADVANCEDORDER_FIELD_'.$key;
            if (!Configuration::hasKey($name)) {
                continue;
            }
            $state = Configuration::get($name);
            if (!$state) {
                continue;
            }
            $smarty_fields[$key] = $field;
            if ($state == 'required') {
                $smarty_fields[$key]['is_required'] = true;
            }
        }
        $this->context->smarty->assign('fields', $smarty_fields);
        $this->context->smarty->assign('states', State::getStates(true));
        $return = array_merge(array(
            // 'order_opc_adress' => $this->context->smarty->fetch(_PS_THEME_DIR_.$tpl),
            'block_user_info' => (isset($block_user_info) ? $block_user_info->hookDisplayTop(array()) : ''),
            'block_user_info_nav' => (isset($block_user_info) ? $block_user_info->hookDisplayNav(array()) : ''),
            // 'formatedAddressFieldsValuesList' => $formated_address_fields_values_list,
            'carrier_data' => $this->_getCarrierList(),
            'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
            'HOOK_PAYMENT' => $this->_getPaymentMethods(),
            'no_address' => 0,
            ),
            $this->getFormatedSummaryDetail()
        );
        $this->context->smarty->assign($return);
        
        $this->setTemplate(_PS_MODULE_DIR_.'pwadvancedorder/views/templates/front/order.tpl');
    }
    
    protected function _processAddressFormat()
    {
        $address_delivery = new Address((int)$this->context->cart->id_address_delivery);
        $address_invoice = new Address((int)$this->context->cart->id_address_invoice);

        $inv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_delivery->id_country, false, true);
        $dlv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_invoice->id_country, false, true);
        $require_form_fields_list = AddressFormat::getFieldsRequired();

        // Add missing require fields for a new user susbscription form
        foreach ($require_form_fields_list as $field_name) {
            if (!in_array($field_name, $dlv_adr_fields)) {
                $dlv_adr_fields[] = trim($field_name);
            }
        }

        foreach ($require_form_fields_list as $field_name) {
            if (!in_array($field_name, $inv_adr_fields)) {
                $inv_adr_fields[] = trim($field_name);
            }
        }

        $inv_all_fields = array();
        $dlv_all_fields = array();

        foreach (array('inv', 'dlv') as $adr_type) {
            foreach (${$adr_type.'_adr_fields'} as $fields_line) {
                foreach (explode(' ', $fields_line) as $field_item) {
                    ${$adr_type.'_all_fields'}[] = trim($field_item);
                }
            }

            ${$adr_type.'_adr_fields'} = array_unique(${$adr_type.'_adr_fields'});
            ${$adr_type.'_all_fields'} = array_unique(${$adr_type.'_all_fields'});

            $this->context->smarty->assign(array(
                $adr_type.'_adr_fields' => ${$adr_type.'_adr_fields'},
                $adr_type.'_all_fields' => ${$adr_type.'_all_fields'},
                'required_fields' => $require_form_fields_list
            ));
        }
    }
    
    public function _getCarrierList()
    {
        $address_delivery = new Address($this->context->cart->id_address_delivery);
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, Configuration::get('PS_SSL_ENABLED'));
        if (!strpos($link_conditions, '?')) {
            $link_conditions .= '?content_only=1';
        } else {
            $link_conditions .= '&content_only=1';
        }
        $carriers = $this->context->cart->simulateCarriersOutput();
        $delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
        $wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
        $wrapping_fees_tax_inc = $this->context->cart->getGiftWrappingPrice();
        $old_message = Message::getMessageByCartId((int)$this->context->cart->id);

        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }

        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        if($address_delivery->city != 'Санкт-Петербург'){
                foreach($delivery_option_list as &$option_list){
                    foreach($option_list as $key => $option){
                        foreach ($option['carrier_list'] as $carrier){
                            if ($carrier['instance']->id_reference == 1 || $carrier['instance']->id_reference == 2){
                                unset($option_list[$key]);
                            }
                        }
                    }
                }
        }

        $this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
        $vars = array(
            'advanced_payment_api' => (bool)Configuration::get('PS_ADVANCED_PAYMENT_API'),
            'free_shipping' => $free_shipping,
            'checkedTOS' => (int)$this->context->cookie->checkedTOS,
            'recyclablePackAllowed' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
            'giftAllowed' => (int)Configuration::get('PS_GIFT_WRAPPING'),
            'cms_id' => (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
            'conditions' => (int)Configuration::get('PS_CONDITIONS'),
            'link_conditions' => $link_conditions,
            'recyclable' => (int)$this->context->cart->recyclable,
            'gift_wrapping_price' => (float)$wrapping_fees,
            'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
            'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency),
            'delivery_option_list' => $delivery_option_list,
            'delivery_city' => $address_delivery->city,
            'carriers' => $carriers,
            'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
            'delivery_option' => $delivery_option,
            'address_collection' => $this->context->cart->getAddressCollection(),
            'opc' => true,
            'oldMessage' => isset($old_message['message'])? $old_message['message'] : '',
            'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                'carriers' => $carriers,
                'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                'delivery_option' => $delivery_option
            ))
        );

        Cart::addExtraCarriers($vars);

        $this->context->smarty->assign($vars);

        if (!Address::isCountryActiveById((int)$this->context->cart->id_address_delivery) && $this->context->cart->id_address_delivery != 0) {
            $this->errors[] = Tools::displayError('This address is not in a valid area.');
        } elseif ((!Validate::isLoadedObject($address_delivery) || $address_delivery->deleted) && $this->context->cart->id_address_delivery != 0) {
            $this->errors[] = Tools::displayError('This address is invalid.');
        } else {
            $result = array(
                'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                    'carriers' => $carriers,
                    'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                    'delivery_option' => $this->context->cart->getDeliveryOption(null, true)
                )),
                'carrier_block' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'pwadvancedorder/views/templates/front/order-carrier.tpl'),

            );
            Cart::addExtraCarriers($result);
            return $result;
        }
        if (count($this->errors)) {
            return array(
                'hasError' => true,
                'errors' => $this->errors,
                'carrier_block' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'pwadvancedorder/views/templates/front/order-carrier.tpl')
            );
        }

    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.'pwadvancedorder/pwadvancedorder.js');
        $this->addCSS(_MODULE_DIR_.'pwadvancedorder/pwadvancedorder.css');
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('oneclick')) {
            $module = Module::getInstanceByName('pwadvancedorder');
            if (!$this->isLogged) {
                $customer = new Customer();
                $customer->firstname = Tools::getValue('firstname');
                $customer->lastname = $_POST['lastname'] = ' ';
                $customer->middlename = $_POST['middlename'] = ' ';
                $customer->email = $_POST['email'] = uniqid('pw').'@'.uniqid().'.ru';
                $_POST['passwd'] = Tools::passwdGen();
                $this->errors = $customer->validateController();
                if ($this->errors) {
                    return;
                }
                if ($customer->add()) {
                    $this->auth($customer);
                } else {
                    $this->errors[] = $module->l('Невозможно создать покупателя');
                    return;
                }
            }
            if (!$this->context->cart->id_address_delivery) {
                $addresses = $this->context->customer->getAddresses($this->context->language->id);
                if ($addr = end($addresses)) {
                    $id_address = $addr['id_address'];
                    $address = new Address($id_address);
                } else {
                    $address = new Address();
                    $address->id_customer = $this->context->customer->id;
                    $address->alias = ' ';
                    $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                    $address->address1 = ' ';
                    $address->city = ' ';
                    $address->postcode = '000000';
                }
                $address->phone = Tools::getValue('phone');
                $this->errors = $address->validateController();
                if ($this->errors) {
                    return;
                }
                if ($address->save()) {
                    $this->context->cart->id_address_delivery = $address->id;
                    $this->context->cart->id_address_invoice = $address->id;
                    $this->context->cart->update();
                    CartRule::autoAddToCart($this->context);
                } else {
                    $this->errors[] = $module->l('Невозможно создать адрес');
                    return;
                }
            }
            $total = $this->context->cart->getOrderTotal(true, Cart::BOTH);
            $orderValidate = $module->validateOrder(
                $this->context->cart->id,
                Configuration::get('PS_OS_PREPARATION'),
                $total,
                $module->displayName,
                null,
                array(),
                null,
                false,
                $this->context->customer->secure_key
            );
            $this->context->customer->firstname = Tools::getValue('firstname');
            $this->errors = $this->context->customer->validateController();
            if ($this->errors) {
                return;
            }
            $this->context->customer->update();
            $address = new Address($this->context->cart->id_address_delivery);
            $address->phone = Tools::getValue('phone');
            $this->errors = $address->validateController();
            if ($this->errors) {
                return;
            }
            $address->update();
            if ($orderValidate) {
                Tools::redirect($this->context->link->getPageLink(
                    'order-confirmation',
                    null,
                    null,
                    'id_order='.Order::getOrderByCartId($this->context->cart->id).'&id_module='.$module->id.'&key='.$this->context->customer->secure_key.'&oneclick=yes&id_cart='.$this->context->cart->id
                ));
            }
            $this->errors[] = $module->l('Невозможно создать заказ');
        }
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
                $this->context->cart->autosetProductAddress();
            }
        }
    }
    
    public function _getPaymentMethods()
    {
        if (!($this->context->customer->id && Customer::customerIdExistsStatic((int)$this->context->cookie->id_customer))) {
            return '<p class="warning">'.Tools::displayError('Please sign in to see payment methods.').'</p>';
        }
        if ($this->context->cart->OrderExists()) {
            return '<p class="warning">'.Tools::displayError('Error: This order has already been validated.').'</p>';
        }
        if (!$this->context->cart->id_customer || !Customer::customerIdExistsStatic($this->context->cart->id_customer) || Customer::isBanned($this->context->cart->id_customer)) {
            return '<p class="warning">'.Tools::displayError('Error: No customer.').'</p>';
        }
        $address_delivery = new Address($this->context->cart->id_address_delivery);
        $address_invoice = ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice ? $address_delivery : new Address($this->context->cart->id_address_invoice));
        if (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice || !Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted) {
            return '<p class="warning">'.Tools::displayError('Error: Please select an address.').'</p>';
        }
        if (count($this->context->cart->getDeliveryOptionList()) == 0 && !$this->context->cart->isVirtualCart()) {
            if ($this->context->cart->isMultiAddressDelivery()) {
                return '<p class="warning">'.Tools::displayError('Error: None of your chosen carriers deliver to some of the addresses you have selected.').'</p>';
            } else {
                return '<p class="warning">'.Tools::displayError('Error: None of your chosen carriers deliver to the address you have selected.').'</p>';
            }
        }
        if (!$this->context->cart->getDeliveryOption(null, false) && !$this->context->cart->isVirtualCart()) {
            return '<p class="warning">'.Tools::displayError('Error: Please choose a carrier.').'</p>';
        }
        if (!$this->context->cart->id_currency) {
            return '<p class="warning">'.Tools::displayError('Error: No currency has been selected.').'</p>';
        }
        if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS')) {
            return '<p class="warning">'.Tools::displayError('Please accept the Terms of Service.').'</p>';
        }

        /* If some products have disappear */
        if (is_array($product = $this->context->cart->checkQuantities(true))) {
            return '<p class="warning">'.sprintf(Tools::displayError('An item (%s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']).'</p>';
        }

        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            return '<p class="warning">'.sprintf(Tools::displayError('An item in your cart is no longer available (%s). You cannot proceed with your order.'), Product::getProductName((int)$id_product)).'</p>';
        }

        /* Check minimal amount */
        $currency = Currency::getCurrency((int)$this->context->cart->id_currency);

        $minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase) {
            return '<p class="warning">'.sprintf(
                Tools::displayError('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                Tools::displayPrice($minimal_purchase, $currency), Tools::displayPrice($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), $currency)
            ).'</p>';
        }

        /* Bypass payment step if total is 0 */
        if ($this->context->cart->getOrderTotal() <= 0) {
            return '<p class="center"><button class="button btn btn-default button-medium" name="confirmOrder" id="confirmOrder" onclick="confirmFreeOrder();" type="submit"> <span>'.Tools::displayError('I confirm my order.').'</span></button></p>';
        }

        $return = Hook::exec('displayPayment');
        if (!$return) {
            return '<p class="warning">'.Tools::displayError('No payment method is available for use at this time. ').'</p>';
        }
        return $return;
    }
    
    protected function getFormatedSummaryDetail()
    {
        $result = array('summary' => $this->context->cart->getSummaryDetails(),
                        'customizedDatas' => Product::getAllCustomizedDatas($this->context->cart->id, null, true));

        foreach ($result['summary']['products'] as $key => &$product) {
            $product['quantity_without_customization'] = $product['quantity'];
            if ($result['customizedDatas']) {
                if (isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']])) {
                    foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses) {
                        foreach ($addresses as $customization) {
                            $product['quantity_without_customization'] -= (int)$customization['quantity'];
                        }
                    }
                }
            }
        }

        if ($result['customizedDatas']) {
            Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
        }
        return $result;
    }
    
    protected function _assignAddress()
    {
        //if guest checkout disabled and flag is_guest  in cookies is actived
        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED') == 0 && ((int)$this->context->customer->is_guest != Configuration::get('PS_GUEST_CHECKOUT_ENABLED'))) {
            $this->context->customer->logout();
            Tools::redirect('');
        }

        $customer = $this->context->customer;
        if (Validate::isLoadedObject($customer)) {
            /* Getting customer addresses */
            $customerAddresses = $customer->getAddresses($this->context->language->id);

            // Getting a list of formated address fields with associated values
            $formatedAddressFieldsValuesList = array();

            foreach ($customerAddresses as $i => $address) {
                if (!Address::isCountryActiveById((int)$address['id_address'])) {
                    unset($customerAddresses[$i]);
                }
                $tmpAddress = new Address($address['id_address']);
                $formatedAddressFieldsValuesList[$address['id_address']]['ordered_fields'] = AddressFormat::getOrderedAddressFields($address['id_country']);
                $formatedAddressFieldsValuesList[$address['id_address']]['formated_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
                    $tmpAddress,
                    $formatedAddressFieldsValuesList[$address['id_address']]['ordered_fields']);

                unset($tmpAddress);
            }

            $customerAddresses = array_values($customerAddresses);
            $this->context->smarty->assign(array(
                'addresses' => $customerAddresses,
                'formatedAddressFieldsValuesList' => $formatedAddressFieldsValuesList)
            );

            /* Setting default addresses for cart */
            if (count($customerAddresses)) {
                if ((!isset($this->context->cart->id_address_delivery) || empty($this->context->cart->id_address_delivery)) || !Address::isCountryActiveById((int)$this->context->cart->id_address_delivery)) {
                    $this->context->cart->id_address_delivery = (int)$customerAddresses[0]['id_address'];
                    $update = 1;
                }
                if ((!isset($this->context->cart->id_address_invoice) || empty($this->context->cart->id_address_invoice)) || !Address::isCountryActiveById((int)$this->context->cart->id_address_invoice)) {
                    $this->context->cart->id_address_invoice = (int)$customerAddresses[0]['id_address'];
                    $update = 1;
                }

                /* Update cart addresses only if needed */
                if (isset($update) && $update) {
                    $this->context->cart->update();
                    if (!$this->context->cart->isMultiAddressDelivery()) {
                        $this->context->cart->setNoMultishipping();
                    }
                    // Address has changed, so we check if the cart rules still apply
                    CartRule::autoRemoveFromCart($this->context);
                    CartRule::autoAddToCart($this->context);
                }
            }

            /* If delivery address is valid in cart, assign it to Smarty */
            if (isset($this->context->cart->id_address_delivery)) {
                $deliveryAddress = new Address((int)$this->context->cart->id_address_delivery);
                if (Validate::isLoadedObject($deliveryAddress) && ($deliveryAddress->id_customer == $customer->id)) {
                    $this->context->smarty->assign('delivery', $deliveryAddress);
                }
            }

            /* If invoice address is valid in cart, assign it to Smarty */
            if (isset($this->context->cart->id_address_invoice)) {
                $invoiceAddress = new Address((int)$this->context->cart->id_address_invoice);
                if (Validate::isLoadedObject($invoiceAddress) && ($invoiceAddress->id_customer == $customer->id)) {
                    $this->context->smarty->assign('invoice', $invoiceAddress);
                }
            }
        }
        if ($oldMessage = Message::getMessageByCartId((int)$this->context->cart->id)) {
            $this->context->smarty->assign('oldMessage', $oldMessage['message']);
        }
    }


}
