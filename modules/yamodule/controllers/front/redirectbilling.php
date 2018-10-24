<?php

/**
 * Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
 *
 * @category  Front Office Features
 * @package   Yandex Payment Solution
 * @author    Yandex.Money <cms@yamoney.ru>
 * @copyright © 2015 NBCO Yandex.Money LLC
 * @license   https://money.yandex.ru/doc.xml?id=527052
 */
include_once('advancedModuleFrontController.php');
class YamoduleRedirectBillingModuleFrontController extends AdvancedModuleFrontController
{
    public $display_header = true;
    public $display_column_left = true;
    public $display_column_right = false;
    public $display_footer = true;
    public $ssl = true;

    public function postProcess()
    {
        parent::postProcess();

        $this->log_on = Configuration::get('YA_P2P_LOGGING_ON');
        $cart = $this->context->cart;
        if ($cart->id_customer == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active
        ) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $this->myCart = $this->context->cart;
        $total_to_pay = $cart->getOrderTotal(true);
        $rub_currency_id = Currency::getIdByIsoCode('RUB');
        if ($cart->id_currency != $rub_currency_id) {
            $from_currency = new Currency($cart->id_currency);
            $to_currency = new Currency($rub_currency_id);
            $total_to_pay = Tools::convertPriceFull($total_to_pay, $from_currency, $to_currency);
        }

        $total_to_pay = number_format($total_to_pay, 2, '.', '');
        $this->module->payment_status = false;
        $code = Tools::getValue('code');
        $cnf = Tools::getValue('cnf');
        if (empty($code)) {
            Tools::redirect('index.php?controller=order&step=3');
        } elseif (!empty($code) && $cnf) {
            $fio = Tools::getValue('ym_billing_fio', '');
            $params = array(
                'total_sum' => $total_to_pay,
            );
            if (empty($fio)) {
                $params['empty'] = true;
            } else {
                $parts = explode(' ', trim($fio));
                foreach ($parts as $index => $val) {
                    if (empty($val)) {
                        unset($parts[$index]);
                    }
                }
                if (count($parts) == 3) {
                    $order = $this->updateStatus();
                    $params['error'] = false;
                    $params['fio'] = implode(' ', $parts);
                    $params['formId'] = Configuration::get('YA_BILLING_ID');
                    $params['narrative'] = $this->parsePlaceholders(Configuration::get('YA_BILLING_PURPOSE'), $order);
                    $params['payment_link'] = 'https://money.yandex.ru/fastpay/confirm';
                } else {
                    $params['error'] = true;
                    $params['fio'] = $fio;
                }
            }
            $this->context->smarty->assign($params);
        }
    }

    /**
     * @return OrderCore
     */
    public function updateStatus()
    {
        $this->log_on = Configuration::get('YA_P2P_LOGGING_ON');

        $cart = $this->context->cart;
        if ($cart->orderExists()) {
            $ord = new Order((int)Order::getOrderByCartId($cart->id));
        } else {
            $ord = $this->module->validateOrder(
                $cart->id,
                _PS_OS_PREPARATION_,
                $cart->getOrderTotal(true, Cart::BOTH),
                $this->module->displayName." Яндекс.Платёжка",
                null,
                array(),
                null,
                false,
                $cart->secure_key
            );
            $id = $this->module->currentOrder;
            $ord = new Order((int)Order::getOrderByCartId($id));
        }
        if ($ord) {
            $history = new OrderHistory();
            $history->id_order = $ord->id;
            $state = Configuration::get('YA_BILLING_END_STATUS');
            if (empty($state)) {
                $state = Configuration::get('PS_OS_PAYMENT');
            }
            $history->changeIdOrderState($state, $ord->id);
            $history->addWithemail(true);
        }
        if ($this->log_on) {
            $this->module->logSave(
                'payment_billing: #'.$this->module->currentOrder.' '.$this->module->l('Order success')
            );
        }
        return $ord;
    }

    public function initContent()
    {
        parent::initContent();
        $cart = $this->context->cart;
        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'this_path' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
        ));
        $link = $this->context->smarty->getTemplateVars('payment_link');
        if (empty($link)) {
            $this->context->smarty->assign('payment_link', '');
        }
        $fio = $this->context->smarty->getTemplateVars('fio');
        if (empty($fio)) {
            $customer = new CustomerCore($cart->id_customer);
            if (!ValidateCore::isLoadedObject($customer)) {
                Tools::redirect('index.php?controller=order&step=1');
            }
            $fio = array();
            if (!empty($customer->lastname)) {
                $fio[] = $customer->lastname;
            }
            if (!empty($customer->firstname)) {
                $fio[] = $customer->firstname;
            }
            $this->context->smarty->assign('fio', implode(' ', $fio));
        }
        $this->context->controller->addJS($this->module->getPathUri().'/views/js/ym-billing.js');

        $this->setTemplate('yabilling.tpl');
    }

    /**
     * @param string $template
     * @param OrderCore $order
     * @return string
     */
    private function parsePlaceholders($template, $order)
    {
        $replace = array(
            '#order_id#' => $order->id,
        );
        foreach ($order->getFields() as $field => $value) {
            $replace['#' . $field . '#'] = $value;
        }
        return strtr($template, $replace);
    }
}