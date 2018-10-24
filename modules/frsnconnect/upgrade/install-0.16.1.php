<?php
/**
  * Social Network connect modules
  * frsnconnect 0.16 by froZZen
  */

if (!defined('_PS_VERSION_'))
  exit;
 
function upgrade_module_0_16_1($object) {

    $sql = '
        UPDATE `'._DB_PREFIX_.'sn_service`
        SET `class` = "YAOAuthSrv"
        WHERE `id_sn_service` = 6';
    Db::getInstance()->Execute(trim($sql));

    return true;
    
}

?>
