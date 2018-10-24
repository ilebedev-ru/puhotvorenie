<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwtitleproductshome extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("pwtitleproductshome");
        $this->description = $this->l("Вывод кол-ва товаров с минимальной и максимальной ценой");
        
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if ( !parent::install() 
			OR !$this->registerHook(Array(
				'displayHome',
			))
            
        ) return false;

        return true;
    }
	public function hookdisplayHome($params){
        $this->context->controller->addCSS($this->_path.'css/pwtitleproducthome.css', 'all');

        $countProducts = Db::getInstance()->getValue("SELECT COUNT(id_product) FROM `" . _DB_PREFIX_ . "product_shop` WHERE `active` = 1");
        $minPrice = Db::getInstance()->getValue("SELECT `price` FROM `" . _DB_PREFIX_ . "product_shop` WHERE `active` = 1 ORDER BY `price` ASC");
        $maxPrice = Db::getInstance()->getValue("SELECT `price` FROM `" . _DB_PREFIX_ . "product_shop` WHERE `active` = 1 ORDER BY `price` DESC");

        $this->smarty->assign(array(
            'minPrice' => round($minPrice),
            'maxPrice' => round($maxPrice),
            'countProducts' => $countProducts,
        ));

		return $this->display(__FILE__, 'pwtitleproductshome.tpl');
	}


}


