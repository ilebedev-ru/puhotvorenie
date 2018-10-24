<?php
/*
 * Для версии 1.4
 */
$useSSL = true;
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
require_once _PS_MODULE_DIR_.'pwexpressorder/pwexpressorder.php';

$pwExpressOrder = new PWExpressOrderClass();
$pwExpressOrderModule = new PWExpressOrder();
$conf = $pwExpressOrder->prepareData();

if (!isset($cart) OR !$cart->id)
{
	$cart = new Cart();
	$cart->id_lang = (int)($cookie->id_lang);
	$cart->id_currency = (int)($cookie->id_currency);
	$cart->id_guest = (int)($cookie->id_guest);
	if ($cookie->id_customer)
	{
		$cart->id_customer = (int)($cookie->id_customer);
		$cart->id_address_delivery = (int)(Address::getFirstCustomerAddressId($cart->id_customer));
		$cart->id_address_invoice = $cart->id_address_delivery;
	}
	else
	{
		$cart->id_address_delivery = 0;
		$cart->id_address_invoice = 0;
	}
	$cart->add();
	$cookie->id_cart = $cart->id;
}

//One click
if(isset($_POST['id_product'])){
	$idProduct = (int)(Tools::getValue('id_product', NULL));
	$idProductAttribute = (int)(Tools::getValue('id_product_attribute', Tools::getValue('ipa')));
	$customizationId = (int)(Tools::getValue('id_customization', 0));
	$qty = 1;
	
	$producToAdd = new Product((int)($idProduct), true, (int)($cookie->id_lang));
	if ((!$producToAdd->id OR !$producToAdd->active) AND !$delete) $errors[] = Tools::displayError('Не получилось добавить товар в корзину');
	$updateQuantity = $cart->updateQty((int)($qty), (int)($idProduct));
}

if($conf['eo_country_show'] & $conf['eo_state_show']) $js_files = array(_THEME_JS_DIR_.'tools/statesManagement.js');
if (isset($_POST['country']) AND is_numeric($_POST['country'])) $selectedCountry = intval($_POST['country']);
if (!isset($selectedCountry)) $selectedCountry = intval(Configuration::get('PS_COUNTRY_DEFAULT'));
$smarty->assign('sl_country', $selectedCountry);

$errors = array();

if (Tools::isSubmit('submitPwExpressOrder') && !sizeof($errors)) {

    $errors = array_merge($errors, $pwExpressOrder->validate());

	$new = false;
	if (!sizeof($errors)) {
		$_POST['email'] = $pwExpressOrder->clientData['email'];
		$result = Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_	.'customer`
			WHERE `active` = 1
			AND `email` = \''.pSQL($pwExpressOrder->clientData['email']).'\'
			AND `deleted` = 0
			AND `is_guest` = 0');
		if ($result['id_customer'] && strlen($pwExpressOrder->clientData['email'])>1) {
			$customer = new Customer($result['id_customer']);
		} else {
			$customer = new Customer();
			$new = true;
			$customer->email = $email;
			$customer->birthday = date("Y-m-d", strtotime("-18 years"));
			$customer->passwd = $passwd;
			$errors = array_unique(array_merge($errors, $customer->validateControler()));
			if (!sizeof($errors)) $customer->save();
		}
        $customer->firstname = $pwExpressOrder->clientData['firstname'];
        $customer->lastname = $pwExpressOrder->clientData['lastname'];
		
		if (!sizeof($errors)) {

			$address = new Address();
			$address->alias = "мой адрес";
			$address->phone_mobile = Tools::getValue('phone_mobile');
			$address->phone = Tools::getValue('phone');
			$address->id_customer = intval($customer->id);
			$address->city = Tools::getValue('city');
			
			$address->id_country = intval(Tools::getValue('id_country'));
			if(!$address->id_country) $address->id_country = 177; //Россия по умолчанию
			$address->id_state = 743; //Москва по умолчанию
			$address->other = Tools::getValue('other');
			$address->postcode = (Tools::getValue('zip')?Tools::getValue('zip'):null);
			$address->company = Tools::getValue('company');
			$address->address1 = Tools::getValue('address1');
			$address->address2 = Tools::getValue('address2');
			$address->lastname = $customer->firstname;
			$address->firstname = $customer->lastname;
			//$address->newname = $addrLastName2; //Отчество
			$errors = array_unique(array_merge($errors, $address->validateControler()));
			if (!sizeof($errors)) {
				$customer->save();
				if($new) if (!Mail::Send((int)($cookie->id_lang), 'account', Mail::l('Добро пожаловать!'), array('{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{email}' => $customer->email, '{passwd}' => $passwd), $customer->email, $customer->firstname.' '.$customer->lastname)) $customer->active = 1;
                $id_address_found = PWExpressOrderClass::getSimilarAddress($address, $customer->id);

                if($id_address_found) $address = new Address($id_address_found, $cookie->id_lang);
                else {
                    if (!$address->add()) {
                        $errors[] = Tools::displayError('Возникла ошибка при создании адреса...');
                    }
                }

                $cookie->id_customer = intval($customer->id);
                $cookie->customer_lastname = $customer->lastname;
                $cookie->customer_firstname = $customer->firstname;
                $cookie->passwd = $customer->passwd;
                $cookie->logged = 1;
                $cookie->email = $customer->email;
				
				if(isset($_POST['id_carrier'])) $cart->id_carrier = (int) $_POST['id_carrier'];
				else $cart->id_carrier = intval(Configuration::get('PS_CARRIER_DEFAULT'));

				$cart->secure_key = $customer->secure_key;
				$cart->id_customer = $customer->id;
				$cart->id_address_delivery = $cart->id_address_invoice = $address->id;
				$cart->update();
				
				if(!$conf['eo_payments']){
					$total = $cart->getOrderTotal(true, 3);
                    $pwExpressOrderModule->validateOrder(intval($cart->id), _PS_OS_PREPARATION_, $total, $pwExpressOrderModule->displayName);
					$order = new Order(intval($pwExpressOrder->currentOrder));
					Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.intval($cart->id).'&id_module='.intval($pwExpressOrderModule->id).'&id_order='.intval($pwExpressOrderModule->currentOrder));
				}else{
					Tools::redirectLink(__PS_BASE_URI__.'order.php?step=3');
				}
			}
		}
	}
}
include(dirname(__FILE__) . '/../../header.php');
$smarty->assign('countries', Country::getCountries(intval($cookie->id_lang), true));
$smarty->assign('conf', $conf);
$smarty->assign('errors', $errors);
Tools::safePostVars();
$smarty->display(_PS_ROOT_DIR_.'/modules/pwexpressorder/form.tpl');

include(dirname(__FILE__) . '/../../footer.php');
?>