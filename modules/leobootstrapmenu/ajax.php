<?php
/**
 * Leo bootstrap menu Module
 * 
 * @version		$Id: file.php $Revision
 * @package		modules
 * @subpackage	$Subpackage.
 * @copyright	Copyright (C) September 2012 LeoTheme.Com <@emai:leotheme@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */
 
define('_PS_ADMIN_DIR_', getcwd());
include_once(_PS_ADMIN_DIR_.'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/leobootstrapmenu.php');

$leobootstrapmenu = new leobootstrapmenu();


if (Tools::getValue('updatePosition')){
	$list = Tools::getValue('list');
	$root = 1;
	$child = array();
	foreach( $list as $id => $parentId ){
		if( $parentId <=0 ){
			$parentId = $root;
		}
		$child[$parentId][] = $id;
	}
	$res = true;
	foreach ($child as $id_parent => $menus ){
		$i = 0;
		foreach( $menus as $id_btmegamenu ){
			$res &= Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'btmegamenu` SET `position` = '.(int)$i.', id_parent = '.(int)$id_parent.' 
				WHERE `id_btmegamenu` = '.(int)$id_btmegamenu
			);
			$i++;
		}
	}
	echo (int)($res); die;
}

