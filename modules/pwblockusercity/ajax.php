<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(dirname(__FILE__).'/classes/CityRule.php');

$method = Tools::getValue('method');
if($method == "changeCity"){
	$city = (string)$_POST['city'];
	if(version_compare(_PS_VERSION_, '1.5', '<')){
		global $cookie;
		$cookie->city =  $city;
        $cookie->write();
	}else{
		Context::getContext()->cookie->__set("city", $city);
	}

	echo Tools::jsonEncode(Array('delivery' => CityRule::getForCity($city)));
	exit();	
}

include($_SERVER['DOCUMENT_ROOT']."/modules/blockusercity/vendor/allcities.php");

$term = trim($_GET['q']);
$term = mb_strtolower($term, 'UTF-8');
$result = array();

foreach($cities as $city){
	$tmp = $city;
	$city = mb_strtolower($city, 'UTF-8');
	if (stripos(strtolower($city), $term) !== false) {
    	$result[] = $tmp;          
	}
}
print json_encode($result);
?>