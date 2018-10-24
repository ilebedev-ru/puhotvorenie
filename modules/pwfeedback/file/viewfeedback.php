<?php

include(dirname(__FILE__).'/config/config.inc.php');
include(dirname(__FILE__).'/init.php');
	
$fdbs = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'feedback WHERE status = 1 ORDER BY `date` DESC');
$smarty->assign('fdbs', $fdbs);
	
		
include(dirname(__FILE__).'/header.php');
$smarty->display(_PS_MODULE_DIR_.'feedback/viewfeedback.tpl');
include(dirname(__FILE__).'/footer.php');
?>