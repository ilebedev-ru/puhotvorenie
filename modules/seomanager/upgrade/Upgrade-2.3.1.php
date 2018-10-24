<?php
/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/


if (!defined('_PS_VERSION_'))
	exit;
function upgrade_module_2_3_1($object)
{
	//install new hook
	$object->registerHook('displayLeftColumnProduct');
	//sql update
	//return Db::getInstance()->execute('select * from ');
}
