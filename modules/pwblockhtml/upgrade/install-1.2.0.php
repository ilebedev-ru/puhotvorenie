<?php
if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_1_2_0($object)
{
    $object->registerHook('DisplayBackOfficeHeader');
    $object->deleteTab();
    $object->createTab($object->l('Блок HTML'));
    $object->unregisterHook('ActionAdminControllerSetMedia');
    $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwblockhtml_shop` (
        `id_pwblockhtml_shop` int(11) NOT NULL AUTO_INCREMENT,
        `id_pwblockhtml` int(11) NOT NULL,
        `id_shop` int(11) NOT NULL,
        PRIMARY KEY (`id_pwblockhtml_shop`),
        CONSTRAINT `blockhtmlshop` UNIQUE (
        `id_pwblockhtml`,
        `id_shop`
        )
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
    try{
        Db::getInstance()->execute($query);
    }catch(Exception $e){
        d($e);
    }
    return true;
}