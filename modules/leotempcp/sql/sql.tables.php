<?php
$query = " 
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_leohook` (
  `id_hook` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `theme` varchar(50) NOT NULL,
  `name_hook` varchar(100) NOT NULL,
  PRIMARY KEY (`id_hook`,`id_module`,`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
INSERT INTO `_DB_PREFIX_leohook` (`id_hook`, `id_module`, `id_shop`, `theme`, `name_hook`) VALUES
(0, 16, 1, 'leofash', 'displayLeftColumn');
";
?>