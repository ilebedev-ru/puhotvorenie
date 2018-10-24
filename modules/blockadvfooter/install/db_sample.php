<?php
/* Data sample for moduleblockadvfooter*/
$query = "CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block` (
  `id_loffc_block` int(11) NOT NULL AUTO_INCREMENT,
  `width` float(10,2) NOT NULL,
  `show_title` tinyint(1) NOT NULL,
  `id_position` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_loffc_block`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
INSERT INTO `_DB_PREFIX_loffc_block` VALUES
('1','28.00','1','2'),
('2','0.00','1','2'),
('3','0.00','1','2'),
('4','0.00','1','2'),
('13','20.00','1','2');
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_item` (
  `id_loffc_block_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_loffc_block` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `link` varchar(2000) NOT NULL,
  `linktype` varchar(25) NOT NULL,
  `link_content` varchar(2000) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `hook_name` varchar(100) NOT NULL,
  `latitude` varchar(25) NOT NULL,
  `longitude` varchar(25) NOT NULL,
  `addthis` tinyint(1) NOT NULL,
  `show_title` tinyint(1) NOT NULL DEFAULT '1',
  `target` varchar(20) NOT NULL DEFAULT '_self',
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id_loffc_block_item`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
INSERT INTO `_DB_PREFIX_loffc_block_item` VALUES
('1','4','link','','link','history','','','','','0','1','','0'),
('3','3','link','','link','prices-drop','','','','','0','1','','0'),
('4','13','custom_html','','','','','','','','0','0','','0'),
('14','4','link','','link','order-slip','','','','','0','1','_self','0'),
('15','4','link','','link','addresses','','','','','0','1','_self','0'),
('16','4','link','','link','identity','','','','','0','1','_self','0'),
('17','4','link','','link','discount','','','','','0','1','_self','0'),
('18','2','link','','cms','1','','','','','0','1','_self','0'),
('19','2','link','','cms','2','','','','','0','1','_self','1'),
('21','2','link','','cms','4','','','','','0','1','_self','2'),
('22','2','link','','cms','5','','','','','0','1','_self','3'),
('23','3','link','','link','new-products','','','','','0','1','_self','1'),
('24','3','link','','link','best-sales','','','','','0','1','_self','2'),
('25','3','link','','link','stores','','','','','0','1','_self','3'),
('26','3','link','','link','contact','','','','','0','1','_self','4'),
('29','1','custom_html','','','','','','','','0','0','','0'),
('30','2','link','','cms','3','','','','','0','1','_self','4');
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_item_lang` (
  `id_loffc_block_item` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id_loffc_block_item`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_item_shop` (
  `id_loffc_block_item` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_loffc_block_item`,`id_shop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `_DB_PREFIX_loffc_block_item_shop` VALUES
('1','LEO_ID_SHOP'),
('3','LEO_ID_SHOP'),
('4','LEO_ID_SHOP'),
('14','LEO_ID_SHOP'),
('15','LEO_ID_SHOP'),
('16','LEO_ID_SHOP'),
('17','LEO_ID_SHOP'),
('18','LEO_ID_SHOP'),
('19','LEO_ID_SHOP'),
('21','LEO_ID_SHOP'),
('22','LEO_ID_SHOP'),
('23','LEO_ID_SHOP'),
('24','LEO_ID_SHOP'),
('25','LEO_ID_SHOP'),
('26','LEO_ID_SHOP'),
('29','LEO_ID_SHOP'),
('30','LEO_ID_SHOP');
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_lang` (
  `id_loffc_block` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id_loffc_block`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_shop` (
  `id_loffc_block` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_loffc_block`,`id_shop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `_DB_PREFIX_loffc_block_shop` VALUES
('1','LEO_ID_SHOP'),
('2','LEO_ID_SHOP'),
('3','LEO_ID_SHOP'),
('4','LEO_ID_SHOP'),
('13','LEO_ID_SHOP');
";
$dataLang = Array("en"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','About us'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','My account'),
('13','LEO_ID_LANGUAGE','Address');
","fr"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","de"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","br"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","es"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","pl"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","ru"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","it"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
","nl"=>"INSERT INTO `_DB_PREFIX_loffc_block_item_lang` VALUES
('1','LEO_ID_LANGUAGE','My orders',''),
('3','LEO_ID_LANGUAGE','Specials',''),
('4','LEO_ID_LANGUAGE','Store Location','<address><strong>Warehouse Offices</strong><br /> 12345 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address><address><strong>Retail Store</strong><br /> 54321 Street name, California, USA<br /> 0123 456 789 / 0123 456 788</address>'),
('14','LEO_ID_LANGUAGE','My credit slips',''),
('15','LEO_ID_LANGUAGE','My addresses',''),
('16','LEO_ID_LANGUAGE','My personal info',''),
('17','LEO_ID_LANGUAGE','My vouchers',''),
('18','LEO_ID_LANGUAGE','Delivery',''),
('19','LEO_ID_LANGUAGE','Legal Notice',''),
('21','LEO_ID_LANGUAGE','About us',''),
('22','LEO_ID_LANGUAGE','Secure payment',''),
('23','LEO_ID_LANGUAGE','New products',''),
('24','LEO_ID_LANGUAGE','Best sellers',''),
('25','LEO_ID_LANGUAGE','Our stores',''),
('26','LEO_ID_LANGUAGE','Contact us',''),
('29','LEO_ID_LANGUAGE','About us','<div class=\"box-services\">\r\n<p>Monday - Friday .................. 8.00 to 18.00</p>\r\n<p>Saturday ......................... 9.00 to 21.00</p>\r\n<p>Sunday ........................... 10.00 to 21.00</p>\r\n<span class=\"iconbox pull-left\"><em class=\"fa fa-phone\"> </em></span>\r\n<div class=\"media-body\"><span>Call us: <strong class=\"h4\"> 0123 456 789</strong></span></div>\r\n</div>'),
('30','LEO_ID_LANGUAGE','Terms and conditions','');
INSERT INTO `_DB_PREFIX_loffc_block_lang` VALUES
('1','LEO_ID_LANGUAGE','My account'),
('2','LEO_ID_LANGUAGE','information'),
('3','LEO_ID_LANGUAGE','Information'),
('4','LEO_ID_LANGUAGE','Our Offers'),
('13','LEO_ID_LANGUAGE','Our offer');
");