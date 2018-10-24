<?php


class OrderConfirmationController extends OrderConfirmationControllerCore
{
    public function init()
    {
        FrontController::init();

        $this->id_cart = (int)(Tools::getValue('id_cart', 0));
        $is_guest = false;

        /* check if the cart has been made by a Guest customer, for redirect link */
        if (Cart::isGuestCartByCartId($this->id_cart) || Tools::isSubmit('pwexpressorder')) {
            $is_guest = true;
            $redirectLink = 'index.php?controller=guest-tracking';
        } else {
            $redirectLink = 'index.php?controller=history';
        }

        $this->id_module = (int)(Tools::getValue('id_module', 0));
        $this->id_order = Order::getOrderByCartId((int)($this->id_cart));
        $this->secure_key = Tools::getValue('key', false);
        $order = new Order((int)($this->id_order));
        if ($is_guest) {
            $customer = new Customer((int)$order->id_customer);
            $redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
        }
        /*if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key)) {
            Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
        }
        $this->reference = $order->reference;
        if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key) {
            Tools::redirect($redirectLink);
        }
        $module = Module::getInstanceById((int)($this->id_module));
        if ($order->module != $module->name) {
            Tools::redirect($redirectLink);
        }*/
    }

}