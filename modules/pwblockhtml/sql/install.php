<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pwblockhtml`';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwblockhtml` (
    `id_pwblockhtml` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) DEFAULT NULL,
    `hooks` text,
    `html` text,
    `html_editor` varchar(255) DEFAULT NULL,
    `need_css` tinyint(4) DEFAULT NULL,
    `css` text,
    `css_editor` varchar(255) DEFAULT NULL,
    `need_js` tinyint(4) DEFAULT NULL,
    `js` text,
    `js_editor` varchar(255) DEFAULT NULL,
    `order` int(11) DEFAULT NULL,
    `active` tinyint(4) DEFAULT NULL,
    `date_add` datetime DEFAULT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_pwblockhtml`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwblockhtml_shop` (
    `id_pwblockhtml_shop` int(11) NOT NULL AUTO_INCREMENT,
    `id_pwblockhtml` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    PRIMARY KEY (`id_pwblockhtml_shop`),
    CONSTRAINT `blockhtmlshop` UNIQUE (
    `id_pwblockhtml`,
    `id_shop`
    )
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
    if ( !Db::getInstance()->execute($query) )
        return false;
