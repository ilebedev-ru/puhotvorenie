<?php

function upgrade_module_0_5_1($object)
{
    $sql_lang = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwcatseo_lang`(
			  `id_pwcatseo` int(10) unsigned NOT NULL,
			  `id_lang` int(10) NOT NULL,
			  `text` TEXT,
			  `title` varchar(255),
			  PRIMARY KEY (`id_pwcatseo`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
    
    Db::getInstance()->Execute($sql_lang);
    $object->uninstallOverrides();
    $object->registerHook('Footer');
    removeDirectory(_PS_MODULE_DIR_.'pwcatseo/override');
    $langs = Language::getLanguages();
    foreach ($langs as $language) {
        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'pwcatseo_lang (
                SELECT id_pwcatseo, '.$language['id_lang'].' as id_lang, text, title FROM ps_pwcatseo)';
        Db::getInstance()->execute($sql);
    }
    // try{
        // Db::getInstance()->execute('ALTER TABLE `sg98_meta`
            // DROP COLUMN `text`,
            // DROP COLUMN `title`;');
    // } catch(Exception $e) {
        
    // }
    return true;
}

function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
       foreach($objs as $obj) {
         is_dir($obj) ? removeDirectory($obj) : unlink($obj);
       }
    }
    rmdir($dir);
}