<?php
/*
define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility
echo PS_ADMIN_DIR;


require_once(dirname(__FILE__).'/init.php');*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
if ($cookie->isLoggedBack()) die();
 
if ($_FILES['file']['type'] == 'image/png' 
|| $_FILES['file']['type'] == 'image/jpg' 
|| $_FILES['file']['type'] == 'image/gif' 
|| $_FILES['file']['type'] == 'image/jpeg'
|| $_FILES['file']['type'] == 'image/pjpeg')
{	
    // setting file's mysterious name
    $filename = md5(date('YmdHis')).'.jpg';
    $file = dirname(__FILE__).'/img/'.$filename;
    // copying
    copy($_FILES['file']['tmp_name'], $file);

    // displaying file    
	$array = array(
		'filelink' => '/modules/newscore/img/'.$filename
	);
	
	echo stripslashes(json_encode($array));   
    
}
 
?>