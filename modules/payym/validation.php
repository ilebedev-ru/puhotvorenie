<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/payym.php');

$payym = new payym();
if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$payym->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'payym')
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
	$payym->validateOrder((int)$cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $payym->displayName, NULL, array(), NULL, false, $customer->secure_key);
	$order = new Order((int)$payym->currentOrder);
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)$payym->id.'&id_order='.(int)$payym->currentOrder);
}
else
{
	/* or ask for confirmation */ 
	$smarty->assign(array(
		'total' => $cart->getOrderTotal(true, Cart::BOTH),
		'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/payym/'
	));

	$smarty->assign('this_path', __PS_BASE_URI__.'modules/payym/');
	$template = 'validation.tpl';
	echo Module::display('payym', $template);
}

include(dirname(__FILE__).'/../../footer.php');