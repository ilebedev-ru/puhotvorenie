<?php
/**
 * @varsion 0.2 - Добавлена рандомизация характеристик
 */
include_once('PWModuleFrontController.php');

class PwdeveloperProductsModuleFrontController extends PWModuleFrontController {
	
    public function initContent() 
	{
        parent::initContent();
		$this->setTemplate('products.tpl');
		$categories = Category::getSimpleCategories(1);
		$this->context->smarty->assign('categories', $categories);
    }
	
	public function postProcess()
	{
		if(Tools::isSubmit('submitProduct')){
			$product = new Product();
			$name = Tools::getValue('name');
			$product->name = PWTools::createMultiLangField($name);
			$product->quantity = 100;
			$product->link_rewrite = PWTools::createMultiLangField(Tools::link_rewrite($name));
			if($_POST['price1'] && $_POST['price1'] > 0 ){
				if($_POST['price2'] && $_POST['price2'] > $_POST['price1']) $price = rand($_POST['price1'], $_POST['price2']);
				else $price = $_POST['price1'];
				$product->price = $price;
			}
			if($_POST['id_category']){
				$product->id_category_default = $_POST['id_category'];
				$categories = Array(1, $_POST['id_category']);
			}
			
			$product->description = PWTools::createMultiLangField(Tools::getValue('description'));
			$product->add();
			$product->addToCategories($categories);
			//var_dump($_FILES['image']);
			if($_FILES['image']['name']) PWTools::addProductImage($product, 'auto', 'image');
			if($_FILES['image2']['name']) PWTools::addProductImage($product, 'auto', 'image2');
			if($_FILES['image3']['name']) PWTools::addProductImage($product, 'auto', 'image3');
			if($_FILES['image4']['name']) PWTools::addProductImage($product, 'auto', 'image4');
			
			echo '<p class="success"><a href="'.$this->context->link->getProductLink($product).'">Добавлено</a></p>';
		}
		if(Tools::isSubmit('submitProductCopy')){
			$count = (int)Tools::getValue('count');
			$id_copy_product = (int)Tools::getValue('id_copy_product');
			if($count>0 && $id_copy_product> 0){
				$this->processDuplicate($id_copy_product, $count);
				echo '<p class="success">'.$count.' товаров добавлено</p>';
			}
		}
	}

    protected function setCover($id_product, $id_image){
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image_shop` image_shop, `'._DB_PREFIX_.'image` i
			SET image_shop.`cover` = NULL
			WHERE i.`id_product` = '.(int)$id_product.' AND i.id_image = image_shop.id_image
			AND image_shop.id_shop='.(int)Context::getContext()->shop->id);
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'image_shop`
			SET `cover` = 1 WHERE `id_image` = '.(int)$id_image);
    }

	/**
	 * @param $id_product
	 * @param int $i Сколько товаров копировать
	 * @return int Всё ли хорошо
	 */
	public function processDuplicate($id_product, $i=1)
	{
		for($i=1;$i<=$_POST['count'];$i++){
			if (Validate::isLoadedObject($product = new Product($id_product)))
			{
				$id_product_old = $product->id;
				//if (Shop::getContext() == Shop::CONTEXT_GROUP){
					$shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
                    //d($shops);
					foreach ($shops as $shop)
						if ($product->isAssociatedToShop($shop['id_shop']))
						{
							$product_price = new Product($id_product_old, false, null, $shop['id_shop']);
							$product->price = $product->price = rand($product_price->price*0.5, $product_price->price*2);
						}
				//}
				unset($product->id);
				unset($product->id_product);
				$product->indexed = 0;
				$product->active = 1;
				$product->name = PWTools::createMultiLangField($product->name[$this->context->cookie->id_lang]." ".$i);
				$product->reference.="_".$i;
				if ($product->add()
					&& Category::duplicateProductCategories($id_product_old, $product->id)
					&& ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
					&& GroupReduction::duplicateReduction($id_product_old, $product->id)
					&& Product::duplicateAccessories($id_product_old, $product->id)
					&& Product::duplicateFeatures($id_product_old, $product->id)
					&& Product::duplicateSpecificPrices($id_product_old, $product->id)
					&& Pack::duplicate($id_product_old, $product->id)
					&& Product::duplicateCustomizationFields($id_product_old, $product->id)
					&& Product::duplicateTags($id_product_old, $product->id)
					&& Product::duplicateDownload($id_product_old, $product->id))
				{
					if ($product->hasAttributes()) Product::updateDefaultAttribute($product->id);

					if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images))
						$this->errors[] = Tools::displayError('An error occurred while copying images.');
					else {
						/* Устанавливаем для всех разную обложку */
						$images = $product->getWsImages();
						$newArray = Array();
						foreach($images as $key=>$smth){
							$newArray[] = $smth['id'];
						}
                        $id_rand_image = $newArray[rand(0, count($newArray)-1)];
						$this->setCover($product->id, $id_rand_image);

						/* Варьируем характеристики - удобно для фильтра */
						$features = $product->getFeatures();
						$features_all = FeatureCore::getFeatures($this->context->cookie->id_lang);
						if($features){
							foreach($features as &$f){
								if($f['custom'] == 0){
									$values = FeatureValue::getFeatureValuesWithLang($this->context->cookie->id_lang, $f['id_feature']);
									if(count($values)>1) {
										$newArray = Array();
										foreach ($values as $fv) {
											//if($fv['id_feature_value'] == $f['id_feature_value']) continue;
											$newArray[] = $fv['id_feature_value'];
										}
										$new_id_feature_value = $newArray[rand(0, count($newArray)-1)];
										$sql = 'UPDATE`'._DB_PREFIX_.'feature_product` SET id_feature_value = '.$new_id_feature_value.'
										WHERE id_product = '.$product->id.' AND id_feature = '.$f['id_feature'];
										Db::getInstance()->Execute($sql);
									}
								}
							}
						}
						/* @TODO Добавление количества для каждой комбинации и для каждого магазина */
						StockAvailable::updateQuantity($product->id, 0, "100");

					}
				}
			}
		}
	}
}