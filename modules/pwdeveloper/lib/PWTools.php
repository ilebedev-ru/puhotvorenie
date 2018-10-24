<?php
abstract class PWTools
{
	public static function createMultiLangField($field)
	{
		$languages = Language::getLanguages(false);
		$res = array();
		foreach ($languages as $lang)
			$res[$lang['id_lang']] = $field;
		return $res;
	}
	
	public static function copyImage($id_product, $id_image, $method = 'auto', $key = 'image_product')
	{
		global $errors;
		if (!isset($_FILES[$key]['tmp_name']))
			return false;
		else
		{
			$image = new Image($id_image);

			if (!$new_path = $image->getPathForCreation())
				$errors[] = Tools::displayError('An error occurred during new folder creation');
			if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES[$key]['tmp_name'], $tmpName))
				$errors[] = Tools::displayError('An error occurred during the image upload');
			elseif (!imageResize($tmpName, $new_path.'.'.$image->image_format))
				$errors[] = Tools::displayError('An error occurred while copying image.');
			elseif ($method == 'auto')
			{
				$imagesTypes = ImageType::getImagesTypes('products');
				foreach ($imagesTypes AS $k => $imageType)
					if (!imageResize($tmpName, $new_path.'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format))
						$errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
			}
			@unlink($tmpName);
			Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_product));
		}
	}
	/*
	*Add image for product
	*
	*return (array)categories
	*/
	public static function addProductImage($product, $method = 'auto', $key = 'image_product')
	{
		global $errors;
		//require_once(INSTALL_PATH.'/../images.inc.php');
		/* Adding a new product image */
		if (isset($_FILES[$key]['name']) && $_FILES[$key]['name'] != null)
		{
			if (!count($errors) && isset($_FILES[$key]['tmp_name']) && $_FILES[$key]['tmp_name'] != null)
			{
				if (!Validate::isLoadedObject($product))
					$errors[] = Tools::displayError('Cannot add image because product add failed.');
				else
				{
					$image = new Image();
					$image->id_product = (int)$product->id;
					$_POST['id_product'] = $image->id_product;
					$image->position = Image::getHighestPosition($product->id) + 1;
					if (($cover = Tools::getValue('cover')) == 1)
						Image::deleteCover($product->id);
					$image->cover = !$cover ? !count($product->getImages(Configuration::get('PS_LANG_DEFAULT'))) : true;
					$image->legend = self::createMultiLangField($product->name[Configuration::get('PS_LANG_DEFAULT')]);
					if (!count($errors))
					{
						if (!$image->add())
							$errors[] = Tools::displayError('Error while creating additional image');
						else
							self::copyImage($product->id, $image->id, $method, $key);
						$id_image = $image->id;
					}
				}
			}
		}
		if (isset($image) AND Validate::isLoadedObject($image) AND !file_exists(_PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format))
			$image->delete();
		if (count($errors))
			return false;
		@unlink(_PS_TMP_IMG_DIR_.'/product_'.$product->id.'.jpg');
		@unlink(_PS_TMP_IMG_DIR_.'/product_mini_'.$product->id.'.jpg');
		return ((isset($id_image) AND is_int($id_image) AND $id_image) ? $id_image : true);
	}
	/*
	*Get parent categories By id_category
	*
	*return (array)categories
	*/
	public static function getParentsCategories($id_category, $id_lang = null)
	{
		global $cookie;
		$id_lang = is_null($id_lang) ? _USER_ID_LANG_ : (int)$id_lang;

		$categories = null;
		$id_current = $id_category;
		while (true)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$id_lang.')
				WHERE c.`id_category` = '.(int)$id_current.' AND c.`id_parent` != 0
			');

			if (isset($result[0]))
				$categories[] = $result[0];
			else if (!$categories)
				$categories = array();

			if (!$result || $result[0]['id_parent'] == 1)
				return $categories;
			$id_current = $result[0]['id_parent'];
		}
	}
	/*
	* Update Product Quantity By Reference
	*
	* Return bool
	*/
	public static function updateQuantityByRef($quantity, $reference)
	{
		if(!is_numeric($quantity) || empty($reference)){
			return false;
		}
		if($id_product = self::getIdProductByRef($reference)){
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET `quantity` = '.(int)$quantity.' WHERE `id_product` = '.$id_product);
		}
		if($id_product_attribute = self::getIdProductAttrByRef($reference)){
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product_attribute` SET `quantity` = '.(int)$quantity.' WHERE `id_product_attribute` = '.$id_product_attribute);
		}
		if(!$id_product && !$id_product_attribute){
			return true;
		}
		return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stock_available` SET `quantity` = '.(int)$quantity.' WHERE '.
											($id_product?'`id_product` = '.(int)$id_product.' AND `id_product_attribute`=0':'').
											($id_product_attribute?'`id_product_attribute` = '.(int)$id_product_attribute:''));
	}
	/*
	*Get id_product By Product reference
	*
	*return (int)id_product
	*/
	public static function getIdProductByRef($reference)
	{
		return Db::getInstance()->getValue('
		SELECT `id_product`
		FROM `'._DB_PREFIX_.'product` p
		WHERE p.reference = "'.pSQL($reference).'"');
	}
	/*
	*Get id_product_attribute By Product_attribute reference
	*
	*return (int)id_product_attribute
	*/
	public static function getIdProductAttrByRef($reference)
	{
		return Db::getInstance()->getValue('
		SELECT `id_product_attribute`
		FROM `'._DB_PREFIX_.'product_attribute` pa
		WHERE pa.reference = "'.pSQL($reference).'"');
	}
	
	/*
	*Get customer cookie by any location
	*return object Cookie
	*/
	public static function getCustomerCookie($context)
	{
		if ($context->shop->getGroup()->share_order){
			$cookie = new Cookie('ps-sg'.$context->shop->getGroup()->id, '');
		}else{
			$force_ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
			$cookie_lifetime = defined('_PS_ADMIN_DIR_') ? (int)Configuration::get('PS_COOKIE_LIFETIME_BO') : (int)Configuration::get('PS_COOKIE_LIFETIME_FO');
			if ($cookie_lifetime > 0)
				$cookie_lifetime = time() + (max($cookie_lifetime, 1) * 3600);
			$domains = null;
			if ($context->shop->domain != $context->shop->domain_ssl)
			  $domains = array($context->shop->domain_ssl, $context->shop->domain);
			$cookie = new Cookie('ps-s'.$context->shop->id, '', $cookie_lifetime, $domains, false, $force_ssl);
		}
		return $cookie;
	}
	
	/*
	*Get admin cookie by any location
	*return object Cookie
	*/
	public static function getAdminCookie()
	{
        $cookie_lifetime = 60*60*24;
		return new Cookie('psAdmin', '', $cookie_lifetime);
	}
}
?>