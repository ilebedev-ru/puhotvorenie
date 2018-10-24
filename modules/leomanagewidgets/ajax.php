<?php
define('_PS_ADMIN_DIR_', getcwd());
include( _PS_ADMIN_DIR_ .'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/leomanagewidgets.php');

$mod = new leomanagewidgets();
$module = array();
if(Tools::isSubmit('editPosition') && Tools::getValue('module') && Tools::getValue('id_shop') && Tools::getValue('secure_key') == $mod->secure_key){
	$hook = Tools::getValue('hook');
	$module = Tools::getValue('module');
	$id_shop = Tools::getValue('id_shop');
	if($module){
		$res = true;
		foreach($module as $position => $id){
			$res &= Db::getInstance()->execute(
				'UPDATE `'._DB_PREFIX_.'leomanagewidgets_shop` SET position = '.(int)($position).' WHERE id_leomanagewidgets = '.(int)($id)
			);
		}
	}
}
