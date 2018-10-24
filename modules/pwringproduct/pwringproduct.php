<?php
/*
 * Исправлен баг отображения неактивных товаров
 */
if (!defined('_PS_VERSION_'))
    exit;

class PWRingProduct extends Module
{
    private $_html;

    public function __construct()
    {
        $this->name = 'pwringproduct';
        $this->version = '1.2';
        $this->author = 'PrestaWeb.ru';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;
        $this->count_products = Configuration::get('PWRINGPRODUCT_HOW_MANY_PRODUCTS');

        parent::__construct();

        $this->displayName = $this->l('Кольцевая перелинковка');
        $this->description = $this->l('Товары на странице категории с кольцевой перелинковкой.');

        if (!$this->isRegisteredInHook('header'))
            $this->registerHook('header');
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('productfooter') || !$this->registerHook('header') || !Configuration::updateValue('PWRINGPRODUCT_HOW_MANY_PRODUCTS', 4))
            return false;
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('PWRINGPRODUCT_HOW_MANY_PRODUCTS'))
            return false;
        return true;
    }

    public function getContent()
    {
        $this->_html = '';
        if (Tools::isSubmit('submitCross'))
        {
            Configuration::updateValue('PWRINGPRODUCT_HOW_MANY_PRODUCTS', (int)Tools::getValue('how_many_products'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully'));
        }
        return $this->_html. '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
			<label for="how_many_products">Сколько товаров показывать?</label>
			<div class="margin-form">
				 <input type="text" name="how_many_products" value = "'.(Configuration::get('PWRINGPRODUCT_HOW_MANY_PRODUCTS') ? (int)Configuration::get('PWRINGPRODUCT_HOW_MANY_PRODUCTS') : 8).'"/>
				<p class="clear">'.$this->l('Максимальное количество отображаемых товаров.').'</p>
			</div>
			<center><input type="submit" name="submitCross" value="'.$this->l('Save').'" class="button" /></center>
		</fieldset>
		</form>';
    }
    private function getCurrentProduct($products, $id_current)
    {
        if ($products)
            foreach ($products as $key => $product)
                if ($product['id_product'] == $id_current)
                    return $key;
        return false;
    }

    public function hookProductFooter($params)
    {
        global $smarty, $cookie, $link;

        $idProduct = (int)Tools::getValue('id_product');
        $product = new Product((int)$idProduct);

        /* If the visitor has came to this product by a category, use this one */
        if (isset($params['category']->id_category))
            $category = $params['category'];
        /* Else, use the default product category */
        else
        {
            if (isset($product->id_category_default) && $product->id_category_default > 1)
                $category = new Category((int)$product->id_category_default);
        }

        if (!isset($category) || !Validate::isLoadedObject($category) || !$category->active)
            return;

        $categoryProducts = Array();
        //$categoryProducts = $category->getProducts((int)$cookie->id_lang, 1, 100); /* 100 products max. */
        $position = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT position FROM `'._DB_PREFIX_.'category_product` WHERE id_category = '.$product->id_category_default.' AND id_product = '.$product->id);
        $nextProductsIDs = $this->getSiblings('>'.$position, $product); //Получаем товары после
        $prevProductsIDs = $this->getSiblings('>=0', $product); //Получаем товары с начала(на тот случай, если после не хватит)

        $i = 0;
        $IDs = Array();
        $nextProducts = array();
        if(count($nextProductsIDs)){
            foreach($nextProductsIDs as $nextID) $IDs[] = $nextID['id_product'];
            $nextProducts = $this->getProducts($IDs);
            $categoryProducts = $nextProducts;
        }
        $count = $this->count_products - count($nextProducts);
        if($count > 0){
            if(count($prevProductsIDs)){
                foreach($prevProductsIDs as $prevID) $IDs[] = $prevID['id_product'];
                $prevProducts = $this->getProducts($IDs);
                $newArray = array_slice($prevProducts, 0, $count);
                $categoryProducts = array_merge($categoryProducts, $newArray);
            }
            $categoryProducts2 = Array();
            foreach($categoryProducts as $product){
                $categoryProducts2[$product['id_product']] = $product;
            }
        }
        //if(isset($categoryProducts2[Tools::getValue('id_product')])) unset($categoryProducts2[Tools::getValue('id_product')]);

		if(!isset($categoryProducts2)) $categoryProducts2 = $categoryProducts;
        $smarty->assign(
		array(
            'categoryProducts' => $categoryProducts2
			)
			);

        return $this->display(__FILE__, 'pwringproduct.tpl');
    }

    public function getProducts($IDs = Array(), $id_lang = 0){
        global $cookie;
        if(!$id_lang) $id_lang = $cookie->id_lang;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`,
			pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`,
			cl.`name` category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 new
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			WHERE p.id_product IN ('.implode(',', $IDs).') AND p.active=1
			GROUP BY p.`id_product`');
        if($products) return Product::getProductsProperties($id_lang, $products);
        return Array();
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'pwringproduct.css', 'all');
    }

    /**
     * @param $position
     * @param $product
     * @return array
     */
    private function getSiblings($position_statement = '>=0', $product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT p.id_product FROM `' . _DB_PREFIX_ . 'category_product` cp
        LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = cp.id_product
         WHERE cp.position ' .$position_statement . ' AND p.active = 1 AND p.id_product != ' . $product->id . ' AND cp.id_category = ' . $product->id_category_default . ' ORDER BY cp.`position` ASC LIMIT ' . $this->count_products);
    }
}
