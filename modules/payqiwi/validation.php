<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/payqiwi.php');

$payqiwi = new payqiwi();
if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$payqiwi->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'payqiwi')
	{
		$authorized = true;
		break;
	}
if (!$authorized)
	die(Tools::displayError('This payment method is not available.'));
	
$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

/* Validate order */
if (Tools::getValue('confirm'))
{
	$customer = new Customer((int)$cart->id_customer);
	$total = $cart->getOrderTotal(true, Cart::BOTH);
	$payqiwi->validateOrder((int)$cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $payqiwi->displayName, NULL, array(), NULL, false, $customer->secure_key);
	$order = new Order((int)$payqiwi->currentOrder);
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)$payqiwi->id.'&id_order='.(int)$payqiwi->currentOrder);
}
else
{
	/* or ask for confirmation */ 
	$smarty->assign(array(
		'total' => $cart->getOrderTotal(true, Cart::BOTH),
		'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/payqiwi/'
	));

	$smarty->assign('this_path', __PS_BASE_URI__.'modules/payqiwi/');
	$template = 'validation.tpl';
	echo Module::display('payqiwi', $template);
}

include(dirname(__FILE__).'/../../footer.php');