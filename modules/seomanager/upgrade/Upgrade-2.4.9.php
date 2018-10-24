<?php
/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/


if (!defined('_PS_VERSION_'))
	exit;
function upgrade_module_2_4_9($object)
{
	if (!$object->isRegisteredInHook('header'))
		return $object->registerHook('header') && Configuration::updateValue($object->name.'-config', $object->initSeoMngrConfigField());
		
	return Configuration::updateValue($object->name.'-config', $object->initSeoMngrConfigField());
}
