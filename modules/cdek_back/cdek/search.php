<?php
require('../../config/config.inc.php');
$q = Tools::getValue('q');
$sql = 'SELECT FullName, PostCodeList FROM `'._DB_PREFIX_ .'cdek_city_database`
        WHERE CityName LIKE \'%'.$q.'%\'';
$arr = Db::getInstance()->executeS($sql);

foreach ($arr as $key => &$a){
    $first_list = explode(',', $a['PostCodeList']);
    if (!$first_list[0] || $first_list[0] == '' || $first_list[0] == '000001'){
        unset($arr[$key]);
    }
    $a['PostCodeList'] = $first_list[0];
}

echo json_encode(array_values($arr));
