<?php
/*
 * Для версии 1.5 и 1.6
 */
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once _PS_MODULE_DIR_.'pwexpressorder/pwexpressorder.php';
require_once _PS_MODULE_DIR_.'pwexpressorder/classes/PWExpressOrderClass.php';
class PWExpressOrderDisplayModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $errors = Array();
        $pwExpressOrder = new PWExpressOrderClass();
        $pwExpressOrderModule = new PWExpressOrder();
        $conf = $pwExpressOrder->prepareData();
        // Add cart if no cart found
        if (!$this->context->cart->id) {
            if (Context::getContext()->cookie->id_guest) {
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $this->context->cart->mobile_theme = $guest->mobile_theme;
            }
            $this->context->cart->add();
            if ($this->context->cart->id) {
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
            }
        }
        if (isset($_POST['id_product'])) {
            $id_product = (int)(Tools::getValue('id_product', NULL));
            $product = new Product($id_product, true, $this->context->language->id);
            if (!$product->checkQty(1) && !Configuration::get('PS_ORDER_OUT_OF_STOCK')) $errors[] = Tools::displayError('There isn\'t enough product in stock.');
            if (empty($this->errors)) $this->context->cart->updateQty(1, (int)($id_product));
        }
        if (Tools::isSubmit('submitPwExpressOrder') && empty($this->errors)) {
            $pwExpressOrder->cleanCartAddress();
            if(!$this->context->cart->nbProducts()) $errors[] = Tools::displayError('Ваша корзина пуста');
            $errors = array_merge($errors, $pwExpressOrder->validate());

            $newCustomer = false;
            if (!sizeof($errors)) {
                $_POST['email'] = $pwExpressOrder->clientData['email'];
                $result = Db::getInstance()->getRow('
				SELECT *
				FROM `' . _DB_PREFIX_ . 'customer`
				WHERE `active` = 1
				AND `email` = \'' . pSQL($pwExpressOrder->clientData['email']) . '\'
				AND `deleted` = 0
				AND `is_guest` = 0');
                if ($result['id_customer'] && $pwExpressOrder->clientData['email']) {
                    $customer = new Customer($result['id_customer']);
                    //$customer->lastname2 = $addrLastName2; //Для очтества
                } else {
                    $customer = new Customer();
                    $newCustomer = true;
                    $customer->email = $pwExpressOrder->clientData['email'];
                    //$customer->lastname2 = $addrLastName2; //Для отчесества
                    $customer->birthday = date("Y-m-d", strtotime("-18 years"));
                    $customer->passwd = Tools::encrypt($pwExpressOrder->clientData['passwd']);
                }
                $customer->firstname = $pwExpressOrder->clientData['firstname'];
                $customer->lastname = $pwExpressOrder->clientData['lastname'];
                $errors = array_unique(array_merge($this->errors, $customer->validateController()));
                if (!sizeof($errors)) {
                    $errors = array_unique(array_merge($errors, $customer->validateFieldsRequiredDatabase()));
                    if (!sizeof($errors)) {
                        $customer->save();
                        if(isset($this->context->cookie->id_customer) &&  $this->context->cookie->id_customer != $customer->id){
                            $id_cart = $this->context->cart->id;
                            $this->context->cookie->mylogout();
                            $this->context->cookie->id_cart = $id_cart;
                        }
                        $address = new Address();
                        $address->alias = "мой адрес";
                        $address->phone_mobile = Tools::getValue('phone_mobile');
                        $address->lastname = $customer->firstname;
                        $address->firstname = $customer->lastname;
                        $address->phone = Tools::getValue('phone');
                        $address->id_customer = intval($customer->id);
                        $address->city = Tools::getValue('city');
                        $address->id_country = intval(Tools::getValue('id_country'));
                        if (!$address->id_country)
                            $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                        $this->context->country = new Country($address->id_country);
                        $address->id_state = 743; //Подставляем Москву
                        $address->other = Tools::getValue('other');
                        $address->postcode = (Tools::getValue('zip') ? Tools::getValue('zip') : null);
                        $address->company = Tools::getValue('company');
                        $address->address1 = Tools::getValue('address1');
                        $address->address2 = Tools::getValue('address2');
                        $errors = array_unique(array_merge($errors, $address->validateController()));
                        if (!sizeof($errors)) {
                            $customer->save();
                            if ($newCustomer) if (!Mail::Send((int)($this->context->cookie->id_lang), 'account', Mail::l('Welcome!'), array('{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{email}' => $customer->email, '{passwd}' => $pwExpressOrder->clientData['passwd']), $customer->email, $customer->firstname . ' ' . $customer->lastname)) $customer->active = 1;
                            $id_address_found = PWExpressOrderClass::getSimilarAddress($address, $customer->id);
                            if($id_address_found) $address = new Address($id_address_found, $this->context->cookie->id_lang);
                            else {
                                if (!$address->add()) {
                                    $errors[] = Tools::displayError('Возникла ошибка при создании адреса...');
                                }
                            }
                            $address->save();
                            $this->context->cookie->id_customer = intval($customer->id);
                            $this->context->cookie->customer_lastname = $customer->lastname;
                            $this->context->cookie->customer_firstname = $customer->firstname;
                            $this->context->cookie->passwd = $customer->passwd;
                            $this->context->cookie->logged = 1;
                            $this->context->cookie->email = $customer->email;
                            $this->context->cart->delivery_option = "";
                            $this->context->cart->id_customer = $customer->id;
                            $this->context->cart->id_address_delivery = $address->id;
                            $this->context->cart->id_address_invoice = $address->id;
                            if (Tools::getValue('addGiftBox') === 'on') {
                                if ($this->context->cart->gift_message != 'Заказана подарочная упаковка') {
                                    $this->context->cart->gift_message .= 'Заказана подарочная упаковка';
                                    $this->context->cart->gift = 1;
                                }
                            } else {
                                $this->context->cart->gift_message .= 'Подарочная упаковка не заказана';
                            }
                            $this->context->cart->secure_key = $customer->secure_key;
                            $this->context->cart->update();
                            $this->context->cart->updateAddressId(key($this->context->cart->getPackageList(true)), $this->context->cart->id_address_delivery); //Выставляем корректный адрес у товаров, иначе будет выставляться не тот адрес
                            if (Tools::isSubmit('id_carrier')) {
                                $id_carrier = Tools::getValue('id_carrier');
                            }else {
                                $id_carrier = Carrier::getDefaultCarrierSelection(Carrier::getCarriers($this->context->language->id));
                            }
                            $delivery_option_list = $this->context->cart->getDeliveryOptionList(null, true);
                            $key = $id_carrier .','; //Разобраться
                            Cache::clean('*');
                            foreach ($delivery_option_list as $id_address => $options) {
                                if (isset($options[$key])) {
                                    $this->context->cart->id_carrier = (int)$id_carrier;
                                    $this->context->cart->setDeliveryOption(array($address->id => $key)); //В этой строке вся соль, нужно адрес актуальный подставлять
                                    $this->context->cart->update();
                                    if (isset($this->context->cookie->id_country))
                                        unset($this->context->cookie->id_country);
                                    if (isset($this->context->cookie->id_state))
                                        unset($this->context->cookie->id_state);
                                    break;
                                }
                            }
                            Cache::clean('*');
                            $this->context->cart->update();
                            $this->context->customer = $customer;
                            if (!@$conf['eo_payments']) {
                                $total = $this->context->cart->getOrderTotal(true, 3);
                                $pwExpressOrderModule->validateOrder((int)$this->context->cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $pwExpressOrderModule->displayName, null, array(), null, false, $customer->secure_key);
                                Tools::redirectLink(__PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . intval($this->context->cart->id) . '&id_module=' . intval($pwExpressOrderModule->id) . '&id_order=' . intval($pwExpressOrderModule->currentOrder));
                            } else {
                                Tools::redirectLink(__PS_BASE_URI__ . 'order.php?step=3');
                            }
                        }
                    }
                }
            }
            $this->errors = $errors;
        }
        $this->setTemplate('display.tpl');
    }
}
?>