<?php
/* Data sample for moduleleobtslider*/
$query = "CREATE TABLE IF NOT EXISTS `_DB_PREFIX_leobtslider` (
  `id_leobtslider_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_leobtslider_slides`,`id_shop`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
INSERT INTO `_DB_PREFIX_leobtslider` VALUES
('1','LEO_ID_SHOP'),
('2','LEO_ID_SHOP'),
('3','LEO_ID_SHOP');
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_leobtslider_slides` (
  `id_leobtslider_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_leobtslider_slides`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
INSERT INTO `_DB_PREFIX_leobtslider_slides` VALUES
('1','1','1'),
('2','2','1'),
('3','3','1');
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_leobtslider_slides_lang` (
  `id_leobtslider_slides` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `legend` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id_leobtslider_slides`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
$dataLang = Array("en"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","fr"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","de"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","br"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","es"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","pl"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","ru"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","it"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
","nl"=>"INSERT INTO `_DB_PREFIX_leobtslider_slides_lang` VALUES
('1','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-1','http://www.prestashop.com','ede0e95db8ca0de2d561d0eec1a3cdc0.jpg'),
('2','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-2','http://www.prestashop.com','ab0a75aa2a3ed15f70c04bb53aac19a2.jpg'),
('3','LEO_ID_LANGUAGE','Lorem ipsum dolor dorest','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc facilisis fringilla nisi euismod Morbi sed adipiscing eleifend, dolor risus congue mi aliquet dolor tellus et ante. ','sample-3','http://www.prestashop.com','857132a2e63f14ec64380d875d8f386e.jpg');
");