<?php
/**
  * Social Network connect modules
  * frsnconnect 0.15 by froZZen
  */

if (!defined('_PS_VERSION_'))
  exit;
 
function upgrade_module_0_15_3($object) {

    $sql = '
        INSERT INTO `'._DB_PREFIX_.'sn_service` 
        (`id_sn_service`, `sn_service_name`, `sn_service_name_full`, `class`) VALUES
        (8, "ld", "LinkedIn", "LDOAuthSrv")';
    Db::getInstance()->Execute(trim($sql));

    return true;
    
}

?>
