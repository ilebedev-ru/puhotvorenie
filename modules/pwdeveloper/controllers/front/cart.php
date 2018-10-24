<?php
include_once('PWModuleFrontController.php');
class PwdeveloperCartModuleFrontController extends PWModuleFrontController {
	
    public function initContent() 
	{
        parent::initContent();
        $carts = Db::getInstance()->executeS('SELECT c.id_cart, cc.firstname, cc.lastname, cc.email FROM '._DB_PREFIX_.'cart c
        JOIN '._DB_PREFIX_.'customer cc ON (c.id_customer = cc.id_customer) WHERE c.id_shop='.$this->context->shop->id.
        ' ORDER by id_cart DESC LIMIT 50');
        $orders = Db::getInstance()->executeS('SELECT o.id_order, o.id_cart, cc.firstname, cc.lastname, cc.email, o.total_paid, o.id_currency FROM '._DB_PREFIX_.'orders o
        JOIN '._DB_PREFIX_.'customer cc ON (o.id_customer = cc.id_customer) WHERE o.id_shop='.$this->context->shop->id.
        ' ORDER by id_order DESC LIMIT 50');
        $this->context->smarty->assign(array(
            'carts' => $carts,
            'orders' => $orders,
        ));
		$this->setTemplate('cart.tpl');
    }
	
	public function postProcess()
	{
        $id_cart = Tools::getValue('id_cart')?Tools::getValue('id_cart'):Tools::getValue('id_order');
        if($id_cart){
            $cart = new Cart($id_cart);
            $customer = new Customer($cart->id_customer);
			if(Validate::isLoadedObject($customer) && Tools::getValue('forceCustomer')){
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
				$this->context->cookie->write();
			}
            $newcart = $cart->duplicate();
            if($newcart['success']){
                $cart = $newcart['cart'];
                $this->context->cart = $cart;
                $this->context->cart->id_customer = (int)$this->context->customer->id;
                $this->context->cart->secure_key = (int)$this->context->customer->secure_key;
                $this->context->cart->save();
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
                $this->context->cookie->write();
                echo 'Success';
            }
        }
	}
 
}