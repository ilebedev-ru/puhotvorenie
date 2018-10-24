<?php
set_time_limit(0);
include_once('PWModuleFrontController.php');
class PwdeveloperCleanerModuleFrontController extends PWModuleFrontController {
	
	public $errors;
	
    public function initContent() 
	{
        parent::initContent();
		$this->setTemplate('cleaner.tpl');
		$this->context->smarty->assign(array(
			'errors' => $this->errors,
		));
    }
	
	public function postProcess()
	{
		if(Tools::isSubmit('cleanProducts')){
			$this->cleanProducts();
		}
	}
	
	protected function cleanProducts()
	{
        set_time_limit(0);
		$products = Product::getProducts($this->context->cookie->id_lang, 0, 99999, 'name', 'ASC');
        foreach($products as $product){
            $obj = new Product($product['id_product']);
            if($obj->id){
                if(!$obj->delete()) $this->errors[] = 'Не получилось удалить товар '.$obj->id;
            }else{
                $this->errors[] = 'Не получилось инициализировать товар '.$product['id_product'];
            }
        }
        $this->cleanSQL();
        $this->cleanImages();

		$this->errors[] = 'Товары очищены';
	}

    protected function cleanImages(){
        Image::deleteAllImages(_PS_PROD_IMG_DIR_);
        if (!file_exists(_PS_PROD_IMG_DIR_)) {
            mkdir(_PS_PROD_IMG_DIR_);
        }
    }

    protected function cleanSQL(){
        $sql = Array();

        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_shop`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_lang`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'category_product`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_tag`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_lang`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_shop`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price_priority`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_carrier`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'cart_product`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'compare_product`');
        if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) { //check if table exist
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'favorite_product`');
        }
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attachment`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_country_tax`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_download`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_group_reduction_cache`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_sale`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_supplier`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'scene_products`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'warehouse_product_location`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_available`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_mvt`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization_field`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supply_order_detail`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
        Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'pack`');

        if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) { //check if table exist
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'favorite_product`');
        }
        if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'smartseoplus_urls\' '))) { //check if table exist
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'smartseoplus_urls` WHERE `object_type` = "product"');
        }
    }
 
}