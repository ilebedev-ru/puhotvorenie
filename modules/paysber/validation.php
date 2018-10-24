<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/paysber.php');

$paysber = new paysber();
if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$paysber->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'paysber')
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
	$paysber->validateOrder((int)$cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $paysber->displayName, NULL, array(), NULL, false, $customer->secure_key);
	$order = new Order((int)$paysber->currentOrder);
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)$paysber->id.'&id_order='.(int)$paysber->currentOrder);
}
else
{
	/* or ask for confirmation */ 
	$smarty->assign(array(
		'total' => $cart->getOrderTotal(true, Cart::BOTH),
		'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/paysber/'
	));

	$smarty->assign('this_path', __PS_BASE_URI__.'modules/paysber/');
	$template = 'validation.tpl';
	echo Module::display('paysber', $template);
}

include(dirname(__FILE__).'/../../footer.php');