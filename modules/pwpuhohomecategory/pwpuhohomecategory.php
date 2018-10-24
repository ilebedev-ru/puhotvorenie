<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwpuhohomecategory extends Module
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

        $this->displayName = $this->l("Категории на главной");
        $this->description = $this->l("Категории на главной");
        
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
        $prod = new Product(428);
		
		$percent=0;
		$cartRule=new CartRule(5);
		if($cartRule->id)
		{
			$percent=$cartRule->reduction_percent;
		}
        // $this->context->controller->addCSS($this->_path.'css/productscategory.css', 'all');
        // $this->context->controller->addJS($this->_path.'js/productscategory.js');
        // $this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll', 'bxslider'));    
        $this->context->smarty->assign(array(
                'productName' => $prod->name,
                'prodPrice' => $prod->price,
                'prod' => $prod,
				'percent'=>$percent
            ));
		return $this->display(__FILE__, 'pwpuhohomecategory.tpl');
	}

}


