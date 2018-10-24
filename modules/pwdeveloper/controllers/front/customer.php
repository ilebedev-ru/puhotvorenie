<?php
include_once('PWModuleFrontController.php');
class PwdeveloperCustomerModuleFrontController extends PWModuleFrontController {
	
    public function initContent() 
	{
        parent::initContent();
		$customers = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'customer WHERE active = 1 AND is_guest = 0');
		$this->context->smarty->assign('customers', $customers);
		$this->setTemplate('customer.tpl');
    }
	
	public function postProcess()
	{
		if(Tools::isSubmit('customer')){
			$customer = new Customer(Tools::getValue('customer'));
			$this->context->smarty->assign('cust', $customer);
		}
		if(Tools::isSubmit('addCustomer')){
			$customer = new Customer();
			$customer->firstname = Tools::getValue('firstname');
			$customer->lastname = Tools::getValue('lastname');
			$customer->email = Tools::getValue('email');
			$customer->passwd = Tools::encrypt(Tools::getValue('password'));
			if($customer->validateFields(true)){
				$customer->save();
			}
		}
		if(Tools::isSubmit('editCustomer')){
			$customer = new Customer(Tools::getValue('id_customer'));
			if(Validate::isLoadedObject($customer)){
				$customer->firstname = Tools::getValue('firstname');
				$customer->lastname = Tools::getValue('lastname');
				$customer->email = Tools::getValue('email');
				if(Tools::getValue('password')){
					$customer->passwd = Tools::encrypt(Tools::getValue('password'));
				}
				if($customer->validateFields(true)){
					$customer->save();
				}
			}
		}
		if(Tools::getValue('auth')){
			$customer = new Customer(Tools::getValue('auth'));
			if(Validate::isLoadedObject($customer)){
                if (method_exists('Context', 'updateCustomer')) {
                    $this->context->updateCustomer($customer);
                } else {
                    $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
                    $this->context->cookie->id_customer = (int)($customer->id);
                    $this->context->cookie->customer_lastname = $customer->lastname;
                    $this->context->cookie->customer_firstname = $customer->firstname;
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
	}
 
}