<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/bankform.php');

if(isset($_GET['hash'])){
	
	$hash = mysql_escape_string($_GET['hash']);
	$result = Db::getInstance()->getRow('SELECT * FROM ps_bankforms WHERE hash = "'.$hash.'"');
	if(empty($result)) die("Такой квитанции нет");
	$bankform = new bankform();

		$smarty->assign(array(
			'firstname' => $result['firstname'],
			'lastname' => $result['lastname'],
			'city' => $result['city'],
			'addr' => $result['address'],
			'id_order' => $result['id_order'],
			'total_to_pay' => $result['price'],
			'compname' => $bankform->compname,
			'schet' => $bankform->schet,
			'inn' => $bankform->inn,
			'kpp' => $bankform->kpp,
			'bankname' => $bankform->bankname,
			'korschet' => $bankform->korschet,
			'bik' => $bankform->bik
		));
}else{
	if(!$id_order=Tools::getValue('id_order'))
		die('no id_order');

	$order=new Order(intval($id_order));
	if($order->id_customer!=$cookie->id_customer && !isset($_GET['a9'])){
		Tools::redirect('authentication.php?back='.urlencode('modules\bankform\form.php?id_order=').$id_order);
	}
	$price1 = ceil($order->total_paid)." руб.";
	$price2 = ceil($order->total_products_wt)." руб.";
	if(isset($_GET['wt_t'])){
		$price = $price2;
	}else $price = $price1;

	if(isset($_GET['real_price'])){
		$price = (int)$_GET['real_price']." руб.";
	}


$currency=new Currency($order->id_currency);
$addr= new Address($order->id_address_invoice);

$bankform = new bankform();

		$smarty->assign(array(
			'firstname' => $addr->firstname,
			'lastname' => $addr->lastname,
			'city' => $addr->city,
			'addr' => $addr->address1,
			'id_order' => $order->id,
			'total_to_pay' => $price,
			'compname' => $bankform->compname,
			'schet' => $bankform->schet,
			'inn' => $bankform->inn,
			'kpp' => $bankform->kpp,
			'bankname' => $bankform->bankname,
			'korschet' => $bankform->korschet,
			'bik' => $bankform->bik
		));
}
$smarty->display(dirname(__FILE__).'/'.'form.tpl');

?>