<?php
if (!defined('_PS_VERSION_'))
	exit;

require_once('classes/catseo.php');

class pwcatseo extends Module
{
	public function __construct()
	{
		$this->name = 'pwcatseo';
		$this->tab = 'other';
		$this->version = '0.5.9';
		$this->author = 'PrestaWeb.ru';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = "Поля в категории";
		$this->description = "Добавляет дополнительные поля в категорию";
	}

	public function install()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwcatseo`(
			  `id_pwcatseo` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `id_category` int(10) NOT NULL DEFAULT "0",
			  PRIMARY KEY (`id_pwcatseo`),
			  UNIQUE KEY `id_category` (`id_category`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $sql_lang = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwcatseo_lang`(
			  `id_pwcatseo` int(10) unsigned NOT NULL,
			  `id_lang` int(10) NOT NULL,
			  `text` TEXT,
			  `title` varchar(255),
			  PRIMARY KEY (`id_pwcatseo`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		return (parent::install() AND Db::getInstance()->Execute($sql) AND Db::getInstance()->Execute($sql_lang)
			AND $this->registerHook('pwcatseo') AND $this->registerHook('footer')
			AND $this->registerHook('actionCategoryDelete') AND $this->registerHook('actionAdminCategoriesFormModifier')
		    AND $this->registerHook('actionAdminCategoriesControllerSaveAfter') AND $this->forceInstallHook());
	}

	
	public function forceInstallHook()
	{
		$category_file = file_get_contents(_PS_THEME_DIR_.'category.tpl');
		file_put_contents(__DIR__.'/category.tpl', $category_file); //backup
		$category_file = str_replace('{hook h="pwcatseo" mod="pwcatseo"}','',$category_file); //чтобы не клонировать много раз при многократных установках
		$category_file = str_replace('{elseif $category->id}', '{hook h="pwcatseo" mod="pwcatseo"}{elseif $category->id}', $category_file);
		
		return file_put_contents(_PS_THEME_DIR_.'category.tpl', $category_file);
	}
	
	public function restoreCategoryFile()
	{
		$category_file = file_get_contents(_PS_THEME_DIR_.'category.tpl');
		$category_file = str_replace('{hook h="pwcatseo" mod="pwcatseo"}','',$category_file);
		file_put_contents(_PS_THEME_DIR_.'category.tpl', $category_file);
		return true;
	}
	
	public function uninstall()
	{
		$sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pwcatseo`';
		$sql_lang = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pwcatseo_lang`';
		return parent::uninstall() && Db::getInstance()->Execute($sql) && Db::getInstance()->Execute($sql_lang);// && $this->restoreCategoryFile();
	}
    
    public static function getLangValue($name, $default = '')
    {
        $ret = array();
        foreach(Language::getLanguages() as $lang){
            $ret[$lang['id_lang']] = Tools::getValue($name.'_'.$lang['id_lang'], $default);
        }
        return $ret;
    }

	public function hookActionCategoryDelete($params)
	{
		$id_category = $params['category']->id;
		$catseo = Catseo::getInstanceByCategory($id_category);
        $catseo->delete();
	}
	
	public static function setText($id_category, $text, $title)
	{
		return Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'pwcatseo` (`id_category`, `text`, `title`)
											VALUES ('.(int)$id_category.', "'.pSQL($text, true).'", "'.pSQL($title).'")
											ON DUPLICATE KEY UPDATE `text` = "'.pSQL($text, true).'", `title` = "'.pSQL($title).'"');
	}
	
	public function hookpwcatseo($params)
	{
		$id_category = Tools::getValue('id_category');
		$category = new Category($id_category, $this->context->language->id);
		$content = $category->description;
        $this->smarty->assign('pwtext', $content);
		return $this->display(__FILE__, 'pwcatseo.tpl');
	}
    
    public function hookFooter($params)
    {
        if($this->context->controller instanceof CategoryController){
            $category = $this->context->smarty->getTemplateVars('category');
            $catseo = Catseo::getInstanceByCategory(Tools::getValue('id_category'), $this->context->language->id);
            $category->pwcatseo = $category->description;
            $category->description = $catseo->text;
            $category->name = (trim($catseo->title)?$catseo->title:$category->name);
            $p = Tools::getValue('p', 1);
            if($p != 1){
                $category->description = null;
                $category->id_image = null;
            }
            if (trim($catseo->title)) {
                $this->context->smarty->assign('h1', trim($catseo->title));
            }
            $this->context->smarty->assign('category', $category);
        }
    }
    
    public function hookactionAdminCategoriesFormModifier($params)
    {
        if (isset($params['object'])) {
            $category = $params['object'];
        } else {
            $category = new Category((int)Tools::getValue('id_category'));
        }
        $params['fields'][0]['form']['input'][] = array(
            'type' => 'hidden',
            'label' => '',
            'name' => 'submitPwCatSeo',
            'required' => false,
            'lang' => false,
        );
        $params['fields'][0]['form']['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Заголовок'),
            'name' => 'title',
            'required' => false,
            'lang' => true,
        );
        $params['fields'][0]['form']['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Описание 2'),
            'name' => 'text',
            'autoload_rte' => true,
            'required' => false,
            'lang' => true,
        );
        $catseo = Catseo::getInstanceByCategory(Tools::getValue('id_category'));
        $params['fields_value']['title'] = $catseo->title;
        $params['fields_value']['text'] = $catseo->text;
        $params['fields_value']['submitPwCatSeo'] = '1';
    }
    
    public function hookactionAdminCategoriesControllerSaveAfter($params)
    {
        $catseo = Catseo::getInstanceByCategory(Tools::getValue('id_category'));
        if (Tools::isSubmit('submitPwCatSeo')) {
            $catseo->title = self::getLangValue('title');
            $catseo->text = self::getLangValue('text');
            try{
                $catseo->update();
            } catch (Exception $e) {
                $this->context->controller->errors[] = $e->getMessage();
            }
        }
    }
}


