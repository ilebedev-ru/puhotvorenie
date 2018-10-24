<?
require_once(INSTALL_PATH.'/../images.inc.php');
function createMultiLangField($field)
{
	$languages = Language::getLanguages(false);
	$res = array();
	foreach ($languages as $lang)
		$res[$lang['id_lang']] = $field;
	return $res;
}
function copyImage($id_product, $id_image, $method = 'auto', $key = 'image_product')
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
function addProductImage($product, $method = 'auto', $key = 'image_product')
{
global $errors;
	/* Adding a new product image */
	if (isset($_FILES[$key]['name']) && $_FILES[$key]['name'] != null)
	{
		if (!count($errors) && isset($_FILES[$key]['tmp_name']) && $_FILES[$key]['tmp_name'] != null)
		{
			if (!Validate::isLoadedObject($product))
				$errors[] = Tools::displayError('Cannot add image because product add failed.');
			elseif (substr($_FILES[$key]['name'], -4) == '.zip')
				return $this->uploadImageZip($product);
			else
			{
				$image = new Image();
				$image->id_product = (int)$product->id;
				$_POST['id_product'] = $image->id_product;
				$image->position = Image::getHighestPosition($product->id) + 1;
				if (($cover = Tools::getValue('cover')) == 1)
					Image::deleteCover($product->id);
				$image->cover = !$cover ? !count($product->getImages(_PS_LANG_DEFAULT_)) : true;
				$image->legend = createMultiLangField($product->name[6]);
				if (!count($errors))
				{
					if (!$image->add())
						$errors[] = Tools::displayError('Error while creating additional image');
					else
						copyImage($product->id, $image->id, $method, $key);
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

 function getParentsCategories($id_category)
{
global $cookie;
	//get id_lang
	$id_lang = is_null($id_lang) ? _USER_ID_LANG_ : (int)$id_lang;

	$categories = null;
	$id_current = (int)$this->id;
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
?>