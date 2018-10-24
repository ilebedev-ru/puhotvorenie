<?php
class NewsCore extends Module
{
    public function __construct() 
    {
        $this->name = 'newscore';
        $this->tab = 'News';
        $this->version = '0.8';

        parent::__construct();

        $this->displayName      = $this->l('Ядро новостей');
        $this->description      = $this->l('Позволяет добавлять новости на ваш сайт');
        $this->confirmUninstall = $this->l('Вы уверены, что хотите удалить все свои новости?');
    }


    public function install()
    {
		global $cookie;
        if (parent::install() == false or !$this->registerHook('header')) {
            return false;
        }
        $id_lang = intval($cookie->id_lang) ? intval($cookie->id_lang) : (int)_PS_LANG_DEFAULT_;
        $query = "
        CREATE TABLE IF NOT EXISTS `". _DB_PREFIX_ . "news` (
            `id_entry`      int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_employee`   int(10) unsigned NOT NULL,
            `status`        tinyint(1) unsigned NOT NULL DEFAULT '0',
			`product_list`  text,
            `date_add`      datetime NOT NULL,
            `date_upd`      datetime NOT NULL, 
            PRIMARY KEY (
                `id_entry`
            )
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		
        if (!Db::getInstance()->Execute($query)) return false;
		
		$query = "INSERT INTO `ps_news` (`id_entry`, `id_employee`, `status`, `product_list`, `date_add`, `date_upd`) VALUES
		(1, 1, 1, NULL, '2013-09-18 12:49:30', '2013-09-18 12:55:50'),
		(2, 1, 1, NULL, '2013-09-18 12:58:13', '2013-09-18 12:58:13');";
		
		if (!Db::getInstance()->Execute($query)) return false;

        $query = "
        CREATE TABLE IF NOT EXISTS  `". _DB_PREFIX_ . "news_lang` (
            `id_entry`          int(10) unsigned NOT NULL,
            `id_lang`           int(10) unsigned NOT NULL,
			`name`       		varchar(128) NOT NULL,
            `meta_title`        varchar(255) NOT NULL,
            `meta_description`  varchar(255) DEFAULT NULL,
            `meta_keywords`     varchar(255) DEFAULT NULL,
            `description_short` text,
            `content`           longtext,
            `link_rewrite`      varchar(128) NOT NULL, 
            PRIMARY KEY (
                `id_entry`,
                `id_lang`
            )
        )  ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		
		if (!Db::getInstance()->Execute($query)) return false;
		
		$query = "INSERT INTO `ps_news_lang` (`id_entry`, `id_lang`, `name`, `meta_title`, `meta_description`, `meta_keywords`, `description_short`, `content`, `link_rewrite`) VALUES
		(1, ".$id_lang.", 'Сайт открыт!', 'Сайт открыт! Сайт открыт!', '', '', '<p>Сайт открыт, наша компания работает на благо клиентов!<br></p>\r\n', '<p>Сайт открыт, наша компания работает на благо клиентов! Сайт открыт, наша компания работает на благо клиентов! Сайт открыт, наша компания работает на благо клиентов! Сайт открыт, наша компания работает на благо клиентов! Сайт открыт, наша компания работает на благо клиентов! Сайт открыт, наша компания работает на благо клиентов!</p>\r\n', 'sayt-otkryt'),
		(2, ".$id_lang.", 'Расширение ассортимента', 'Расширение ассортимента! Расширение ассортимента!', '', '', '<p>Мы добавили в наш интернет магазин новую продукцию, не забудьте с ней ознакомится.<br></p>', '<p>Мы добавили в наш интернет магазин новую продукцию, не забудьте с ней ознакомится! Мы добавили в наш интернет магазин новую продукцию, не забудьте с ней ознакомится! Мы добавили в наш интернет магазин новую продукцию, не забудьте с ней ознакомится!</p>', 'rasshirenie-assortimenta');";

        if (!Db::getInstance()->Execute($query)) return false;
		
		
		$query = 'INSERT INTO `' . _DB_PREFIX_ . 'meta` (`page`) VALUES ("news");';
		if (!Db::getInstance()->Execute($query)) {
            return false;
        }
		$id_meta = Db::getInstance()->Insert_ID();

		$query = "INSERT INTO `" . _DB_PREFIX_ . "meta_lang` (
					`id_meta` ,
					`id_lang` ,
					`title` ,
					`description` ,
					`keywords` ,
					`url_rewrite`
					)
					VALUES (
					".$id_meta.", '".$id_lang."', 'Новости', 'Последние события на сайте', 'новости, статьи', 'info'
					)";
		if (!Db::getInstance()->Execute($query)) {
            return false;
        }
		$htaccess = Configuration::get('PS_HTACCESS_SPECIFIC')."\n";
		$htaccess.= "RewriteRule ^info/entry/([0-9]+)-([a-zA-Z0-9-]*).html(.*)$ /news.php?id_entry=$1 [QSA,L,E]
		RewriteRule ^info/$ /news.php [L,E]
RewriteRule ^info/category/([0-9]+)-([a-zA-Z0-9-]*).html(.*)$ /news.php?category_id=$1 [QSA,L,E]
RewriteRule ^info/category/([0-9]+)-([a-zA-Z0-9-]*)/entry/([0-9]+)-([a-zA-Z0-9-]*)(.*)$ /news.php?category_id=$1&id_entry=$3 [QSA,L,E]\n";
		Configuration::updateValue('PS_HTACCESS_SPECIFIC',  $htaccess, true);
		Tools::generateHtaccess(dirname(__FILE__).'/../../.htaccess', Configuration::get('PS_REWRITING_SETTINGS'), Configuration::get('PS_HTACCESS_CACHE_CONTROL'), $htaccess, Configuration::get('PS_HTACCESS_DISABLE_MULTIVIEWS'));
		
		mkdir("../img/preview");
        
        return true;
    }


    public function uninstall() 
    {
        if (parent::uninstall() == false 
            OR !Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'news`')
            OR !Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'news_lang`')
			OR !Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'meta` WHERE page = "news"')
			OR !Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE url_rewrite = "info"')
			) {
            return false;
        }
        return true;
    }
	
	public function twitterPost($message){
		include_once(_PS_MODULE_DIR_ . 'newscore/twitteroauth.php');
		define("CONSUMER_KEY", "BHWbW7vOfVg40PN3MeKDag");
		define("CONSUMER_SECRET", "tFW1d9OdzcfcJNrN8OsKbXmPwVZKTRuHhJlDz9Gv0A");
		define("OAUTH_TOKEN", "374475935-3v3Tj4CCeXzomGsJ5JQqB4bFiEsK5V74zBYjwCDK");
		define("OAUTH_SECRET", "QrLPpPepBbwxEWyRF69oT7pre5qMFSfHu8Tb9FQDVKc");

		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$content = $connection->get('account/verify_credentials');

		$connection->post('statuses/update', array('status' => $message));

	}

    public function getContent() 
    {
        global $currentIndex;
        echo '<h2>' . $this->displayName . '</h2>';
        require_once(dirname(__FILE__).'/classes/NewsHandler.php');
        $newsHandler = new NewsHandler();
        $newsHandler->classPath = _PS_MODULE_DIR_ . 'newscore/classes/News.php';
        $newsHandler->className = 'News';
        $newsHandler->postProcess();
        $newsHandler->display();
    }
	
	public function hookHeader()
	{
		Tools::addCSS(($this->_path).'news.css', 'all');
	}
}
?>
