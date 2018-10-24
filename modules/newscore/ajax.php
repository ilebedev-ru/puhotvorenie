<?
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
if ($cookie->isLoggedBack()) die();

if($_REQUEST['action'] == "generate"){
	$qty = (int)$_REQUEST['qty'];
	if($qty > 0){
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE active = 1 ORDER BY RAND() LIMIT '.$qty.'');
		if($result){
			$values = array_map('array_pop', $result);
			die(implode(",", $values));
		}else die('0');
	}
}
?>