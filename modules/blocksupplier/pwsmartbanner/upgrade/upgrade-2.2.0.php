<?php


function upgrade_module_2_2_0($module)
{
    $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaproduct` (
    	`id_product` INT(11) NOT NULL,
        `export` TINYINT(2) NULL DEFAULT NULL,
        PRIMARY KEY (`id_product`)
        );';
	return Db::getInstance()->execute($sql) and $module->registerHook(array('ActionAdminProductsListingFieldsModifier', 'ActionAdminControllerSetMedia', 'DisplayBackOfficeTop', 'ActionProductUpdate', 'ActionProductAdd'));
}