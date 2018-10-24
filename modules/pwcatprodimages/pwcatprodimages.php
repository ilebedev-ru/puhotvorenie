<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwcatprodimages extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = "Фотографии товаров в категории";
        $this->description = "Меняет фотографию товара при наведении";
    }

    public function install()
    {
        return (parent::install()  AND $this->registerHook('displayHeader') AND $this->registerHook('actionProductListModifier'));
    }

	public function hookdisplayHeader($params){
		$this->context->controller->addCSS($this->_path.$this->name.'.css', 'all');
		$this->context->controller->addJS($this->_path.$this->name.'.js');
	}

	public function hookactionProductListModifier($params){
		foreach($params['cat_products'] as $i => $product){
			$params['cat_products'][$i]['images'] = $this->getImages($product['id_product'], $this->context->language->id, $product['id_image']);
		}
	}
	
	private function getImages($id_product, $id_lang, $id_image = 0)
    {
		$images = Db::getInstance()->executeS('
			SELECT i.`id_image`
			FROM `'._DB_PREFIX_.'image` i
			'.Shop::addSqlAssociation('image', 'i').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			WHERE i.`id_product` = '.(int)$id_product.'
			ORDER BY `position`'
        );
		$ret = array();
		foreach($images as $image){
			if(!strripos($id_image, $image['id_image']) > 0){
				$ret[] =  $image['id_image'];
			}
		}
        return $ret;
    }


}


