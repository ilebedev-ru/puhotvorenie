<?php
/* SSL Management */
$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
$conf = unserialize(Configuration::get('EO_CONFIG'));
$gift = array();

//Cart
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
	(int)$cookie->id_cart = $cart->id;
}
//One click
if(isset($_POST['id_product'])){
	$idProduct = (int)(Tools::getValue('id_product', NULL));
	$idProductAttribute = (int)(Tools::getValue('id_product_attribute', Tools::getValue('ipa')));
	$customizationId = (int)(Tools::getValue('id_customization', 0));
	$qty = 1;
	$cart->giftAllowed = 1;
	$producToAdd = new Product((int)($idProduct), true, (int)($cookie->id_lang));
	if ((!$producToAdd->id OR !$producToAdd->active) AND !$delete) $errors[] = Tools::displayError('Не получилось добавить товар в корзину');
	
	$updateQuantity = $cart->updateQty((int)($qty), (int)($idProduct));
	$cart->update();
}
function getAddresses2($id_customer)
{
	return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
	SELECT *
	FROM `'._DB_PREFIX_.'address`
	WHERE `id_customer` = '.(int)($id_customer).'
	AND `deleted` = 0');
}


if($conf['eo_country_show'] & $conf['eo_state_show']) { 
	$js_files = array(
		_THEME_JS_DIR_.'tools/statesManagement.js'
	);
}

if($conf['eo_carriers']){
	$carriers = Carrier::getCarriers(intval($cookie->id_lang),true);
	$smarty->assign('carriers', $carriers);
}

if (isset($_POST['country']) AND is_numeric($_POST['country'])) $selectedCountry = intval($_POST['country']);
if (!isset($selectedCountry)) $selectedCountry = intval(Configuration::get('PS_COUNTRY_DEFAULT'));
$smarty->assign('sl_country', $selectedCountry);

if($cart->id_carrier) $smarty->assign('id_carrier', $cart->id_carrier);

$errors = array();
/* проверяем минимальный заказ */
$orderTotal = $cart->getOrderTotal();
$orderTotalDefaultCurrency = Tools::convertPrice($cart->getOrderTotal(true, 1), Currency::getCurrency(intval(Configuration::get('PS_CURRENCY_DEFAULT'))));
$minimalPurchase = floatval(Configuration::get('PS_PURCHASE_MINIMUM'));
$hideForm = false;
if ($orderTotalDefaultCurrency < $minimalPurchase) {
	$errors[] = Tools::displayError('Общая сумма заказа должна быть не менее ').' '.Tools::displayPrice($minimalPurchase, Currency::getCurrency(intval($cart->id_currency)));
	$hideForm = true;
} elseif ($orderTotal == 0) {
	$errors[] = Tools::displayError('Ваша корзина пуста');
	$hideForm = true;
}
$smarty->assign('hideForm', $hideForm);

$back = Tools::getValue('back');
if (!empty($back)) $smarty->assign('back', Tools::safeOutput($back));

if($cookie->id_customer > 0){
	$customer = new Customer(intval($cookie->id_customer));
	$adresses = getAddresses2(intval($cookie->id_customer));
	if(count($adresses)){
		$address = $adresses[count($adresses)-1];
	}
	if($customer){
		$smarty->assign('address',$address);
		$smarty->assign('customer', $customer);
	}
}

$gift['enable'] = Configuration::get('PS_GIFT_WRAPPING');
if ($gift['enable'] == 1) {
	if (Tools::getValue('gift') == 'on') $cart->gift = 1;
	$gift['price'] = Configuration::get('PS_GIFT_WRAPPING_PRICE');
	$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
	$gift['currency'] = $currency->sign;
}
else $cart->gift = 0;

if (Tools::isSubmit('submitAccount') && !sizeof($errors)) {
	$create_account = 1;
	if ($conf['eo_email_show'] && $conf['eo_email_required'] && !Validate::isEmail($_POST['email'])) {
		$errors[] = Tools::displayError('Укажите E-mail');
	} else {
		if ($_POST['email']) {
			$email = $_POST['email'];
			$smarty->assign('email_type_by_user', 1);
		} else {
			$email = 'guest'.uniqid().'@'.uniqid().'.ru';
		}
	}
	if($conf['eo_company_show'] && $conf['eo_company_required'] && !$_POST['company']) {
		$errors[] = Tools::displayError('Укажите компанию');
	}
	if($conf['eo_fname_show'] && $conf['eo_fname_required'] && !$_POST['firstname']) {
		$errors[] = Tools::displayError('Укажите свое имя.');
	} else {
		$_POST['firstname'] = ($_POST['firstname']?$_POST['firstname']:'Клиент ');
		$firstname = $_POST['firstname'];
	}
	if($conf['eo_lname_show'] && $conf['eo_lname_required'] && !$_POST['lastname']) {
		$errors[] = Tools::displayError('Укажите свою фамилию.');
	} else {
		$_POST['lastname'] = ($_POST['lastname']?$_POST['lastname']:'Фамилия');
		$lastname = $_POST['lastname'];
	}
	if(!$_POST['middlename']){
		
	}else{
		$middlename = mysql_escape_string(strip_tags($_POST['middlename']));
	}
	
	if($conf['eo_password_show'] && $conf['eo_password_required'] && !$_POST['password']) {
		$errors[] = Tools::displayError('Укажите пароль.');
	} else {
		$passwd = ($_POST['password']?$_POST['password']:substr(uniqid(rand(). true),0,6));
	}
	//$md5Passwd = md5($addrFirstName.' '.$addrLastName, true);
	//$passwd = uniqid(rand(). true);
	$_POST['passwd'] = $passwd;
	$_POST['confirm_passwd'] = $passwd;
	if($conf['eo_country_show'] && $conf['eo_country_required'] && (!$_POST['id_country'] || !is_numeric($_POST['id_country']))) {
		$errors[] = Tools::displayError('Выберите страну.');
	} else {
		$_POST['id_country'] = ($_POST['id_country']?intval($_POST['id_country']):Configuration::get('PS_COUNTRY_DEFAULT'));
	}
	if($conf['eo_state_show'] && $conf['eo_state_required'] && (!$_POST['id_state'] || !is_numeric($_POST['id_state']))) {
		$errors[] = Tools::displayError('Выберите регион.');
	} else {
		$_POST['id_state'] = intval($_POST['id_state']);
	}
	if($conf['eo_address_show'] && $conf['eo_address_required'] && !$_POST['address1']) {
		$errors[] = Tools::displayError('Укажите адрес.');
	} else {
		$_POST['address1'] = ($_POST['address1']?$_POST['address1']:'Адрес не указан');
	}
	if($conf['eo_address2_show'] && $conf['eo_address2_required'] && !$_POST['address2']) {
		$errors[] = Tools::displayError('Укажите доп. адрес.');
	}
	if($conf['eo_city_show'] && $conf['eo_city_required'] && !$_POST['city']) {
		$errors[] = Tools::displayError('Укажите город');
	} else {
		$_POST['city'] = ($_POST['city']?$_POST['city']:'Город не указан');
	}
	if($conf['eo_zip_show'] && $conf['eo_zip_required'] && !$_POST['zip']) {
		$errors[] = Tools::displayError('Укажите почтовый индекс');
	}
	if(strlen($_POST['zip'])>1 && !is_numeric($_POST['zip'])){
		$errors[] = Tools::displayError('Почтовый индекс указан не верно, он должен состоять только из цифр');
	}
	if($conf['eo_other_show'] && $conf['eo_other_required'] && !$_POST['other']) {
		$errors[] = Tools::displayError('Укажите дополнительную информацию');
	}
	if($conf['eo_phone_show'] && $conf['eo_phone_required'] && !$_POST['phone']) {
		$errors[] = Tools::displayError('Укажите телефон');
	}
	if($conf['eo_mobilephone_show'] && $conf['eo_mobilephone_required'] && !$_POST['phone_mobile']) {
		$errors[] = Tools::displayError('Укажите мобильный телефон');
	}

	$new = false;
	if (!sizeof($errors)) {
		$_POST['email'] = $email;
		$result = Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_	.'customer`
			WHERE `active` = 1
			AND `email` = \''.pSQL($email).'\'
			AND `deleted` = 0
			AND `is_guest` = 0');
		if ($result['id_customer'] && strlen($email)>1) {
			$customer = new Customer($result['id_customer']);
			$customer->firstname = $firstname;
			$customer->lastname = $lastname;
			//$customer->lastname2 = $addrLastName2;
		} else {
			$customer = new Customer();
			$new = true;
			$customer->email = $email;
			$customer->firstname = $firstname;
			$customer->lastname = $lastname;
			//$customer->lastname2 = $addrLastName2;
			$customer->birthday = date("Y-m-d", strtotime("-18 years"));
			$customer->passwd = $passwd;
			$customer->save();
		}

		//$customer->birthday = (empty($_POST['years']) ? '' : intval($_POST['years']).'-'.intval($_POST['months']).'-'.intval($_POST['days']));
		$errors = array_unique(array_merge($errors, $customer->validateControler()));
		if (!sizeof($errors)) {
			
			/* Customer and address, same fields, caching data */
			$address = new Address();
			$address->alias = "мой адрес";
			$address->phone_mobile = Tools::getValue('phone_mobile');
			$address->phone = Tools::getValue('phone');
			$address->id_customer = intval($customer->id);
			$address->city = Tools::getValue('city');
			$address->id_country = intval(Tools::getValue('id_country'));
			$address->id_state = 743;
			$address->other = Tools::getValue('other');
			$address->postcode = (Tools::getValue('zip')?Tools::getValue('zip'):null);
			$address->company = Tools::getValue('company');
			$address->address1 = Tools::getValue('address1');
			$address->address2 = Tools::getValue('address2');
			$address->lastname = $lastname;						$address->middlename = $middlename;
			$address->firstname = $firstname;
			$errors = array_unique(array_merge($errors, $address->validateControler()));
			if (!sizeof($errors)) {
				$customer->save();
				if($new) if (!Mail::Send((int)($cookie->id_lang), 'account', Mail::l('Добро пожаловать!'), array('{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{email}' => $customer->email, '{passwd}' => $passwd), $customer->email, $customer->firstname.' '.$customer->lastname)) $customer->active = 1;
				if (!$address->add()) {
					$errors[] = Tools::displayError('Возникла ошибка при создании адреса...');
				} else {
					$cookie->id_customer = intval($customer->id);
					$cookie->customer_lastname = $customer->lastname;
					$cookie->customer_firstname = $customer->firstname;
					$cookie->passwd = $customer->passwd;
					$cookie->logged = 1;
					$cookie->email = $customer->email;
				}
				//var_dump($address);
				$address->save();
				
				if(isset($_POST['id_carrier'])) $cart->id_carrier = (int) $_POST['id_carrier'];
				else $cart->id_carrier = intval(Configuration::get('PS_CARRIER_DEFAULT'));
				
				$cart->id_customer = $customer->id;
				$cart->id_address_delivery = $cart->id_address_invoice = $address->id;

				$cart->update();
				if(!$conf['eo_payment']){
					include_once '../cashondelivery/cashondelivery.php';
					$cashOnDelivery = new CashOnDelivery();
					$total = $cart->getOrderTotal(true, 3);
					$cashOnDelivery->validateOrder(intval($cart->id), _PS_OS_PREPARATION_, $total, $cashOnDelivery->displayName);
					$order = new Order(intval($cashOnDelivery->currentOrder));
					Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.intval($cart->id).'&id_module='.intval($cashOnDelivery->id).'&id_order='.intval($cashOnDelivery->currentOrder));
				}else{
					Tools::redirectLink(__PS_BASE_URI__.'order.php?step=3');
				}
			}
		}
	}
}
include(dirname(__FILE__).'/../../header.php');
$smarty->assign('countries', Country::getCountries(intval($cookie->id_lang), true));
$smarty->assign('conf', $conf);
$smarty->assign('gift', $gift);
$smarty->assign('errors', $errors);
Tools::safePostVars();
$smarty->display(_PS_ROOT_DIR_.'/modules/expressorder/makeorder.tpl');

include(dirname(__FILE__).'/../../footer.php');
?>
