CREATE TABLE IF NOT EXISTS `ps_leomanagewidgets` (
  `id_leomanagewidgets` int(11) NOT NULL AUTO_INCREMENT,
  `hook` varchar(25) DEFAULT NULL,
  `task` varchar(25) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_leomanagewidgets`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ps_leomanagewidgets_shop` (
  `id_leomanagewidgets` int(11) NOT NULL DEFAULT '0',
  `id_shop` int(11) NOT NULL DEFAULT '0',
  `position` int(11) DEFAULT NULL,
  `title` text,
  `configs` text,
  PRIMARY KEY (`id_leomanagewidgets`,`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ps_leomanagewidgets_exceptions` (
  `id_leomanagewidgets` int(11) NOT NULL DEFAULT '0',
  `id_shop` int(11) NOT NULL DEFAULT '0',
  `hook` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_leomanagewidgets`,`id_shop`,`hook`,`file_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;