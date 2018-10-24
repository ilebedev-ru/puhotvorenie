<?php
include_once('PWModuleFrontController.php');
class PwdeveloperCategoriesModuleFrontController extends PWModuleFrontController {
	
    public function initContent() 
	{
        parent::initContent();
		$this->setTemplate('categories.tpl');
		$categories = Category::getSimpleCategories(1);
		$this->context->smarty->assign('categories', $categories);
    }
	
	public function postProcess()
	{
		require_once(__DIR__.'/../../lib/PWTools.php');
		if(Tools::isSubmit('submitCatList')){
			$rootCategory = Category::getRootCategory();
			$categorylist = Tools::getValue('catlist');
			$arr = preg_split('/\\r\\n?|\\n/', $categorylist);
			$id_last = array();
			foreach($arr as $row){
				$level = 1;
				$row = trim($row);
				if(strlen($row)){
                    for($i=10;$i>1;$i--){
                        if(substr($row,0,($i-1)) == $this->getMinus($i)){
                            $level = $i;
                            $row = substr($row,($i-1));
                            break;
                        }
                    }
                    if($level>1)
                        $id_parent = $id_last[($level-1)];
                    else
                        $id_parent = $rootCategory->id;

                    $category = $this->createCategory($row, $id_parent);
                    if($category->add()){
                        $id_last[$level] = $category->id;
                    }
				}
			}
			echo 'Успешно добавлено';
		}
		if(Tools::isSubmit('submitCategoryReplicate')){
            $categoryList = preg_split('/\\r\\n?|\\n/',Tools::getValue('catlist'));
            $id_from = Tools::getValue('id_from');
            $id_from_obj = new Category($id_from);
            if(!$id_from_obj->id) die('Категория откуда берутся товары - не существует');
            $id_parent = Tools::getValue('id_parent', Category::getRootCategory());

            if(count($categoryList)>0){
                foreach($categoryList as $category){
                    $rand = rand(100,500);
                    $products = $id_from_obj->getProducts(Configuration::get('PS_LANG_DEFAULT'),1,100, null,null,false, true, true, $rand);
                    if(trim($category)) {
                        $categoryObj = $this->createCategory($category, $id_parent);
                        if($categoryObj->add()) {
                            $catseo = new Catseo();
                            $catseo->id_category = $categoryObj->id;
                            //$catseo->title = $categoryObj->meta_title = PWTools::createMultiLangField($category.' люстры');
                            $catseo->save();
                            $categoryObj->save();
                            echo 'Категория '.$categoryObj->name[Configuration::get('PS_LANG_DEFAULT')].' добавлена<br/>';
                            foreach ($products as $product) {
                                if ($product['quantity'] > 0) {
                                    $result = Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'category_product (id_product, id_category) VALUES (' . $product['id_product'] . ',' . $categoryObj->id . ')');
                                }
                            }
                        }
                    }
                }
            }
        }
	}

	public function createCategory($name, $id_parent){
        $category = new Category();
        $category->name = PWTools::createMultiLangField(Tools::ucfirst($name));
        $category->link_rewrite = PWTools::createMultiLangField(self::str2url($name));
        $category->id_parent = $id_parent;
        $category->active = 1;
        return $category;
    }

    public function getMinus($count){
        $return = "";
        for($i=1;$i<$count;$i++) $return.="-";
        return $return;
    }

    public static function str2url($str)
    {

        $allow_accented_chars = false;

        $str = trim($str);

        if (function_exists('mb_strtolower'))
            $str = mb_strtolower($str, 'utf-8');
        if (!$allow_accented_chars)
            $str = Tools::replaceAccentedChars($str);

        // Remove all non-whitelist chars.
        if ($allow_accented_chars)
            $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\p{L}]/u', '', $str);
        else
            $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-]/','', $str);

        $str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $str);
        $str = str_replace(array(' ', '/'), '-', $str);

        // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
        // This way we lose fewer special chars.
        if (!function_exists('mb_strtolower'))
            $str = strtolower($str);

        return $str;
    }
 
}