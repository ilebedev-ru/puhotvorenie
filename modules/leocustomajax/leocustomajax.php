<?php
/*
* 2011-2013 LeoTheme.com
*
*/

if (!defined('_PS_VERSION_'))
	exit;

class Leocustomajax extends Module
{
	public function __construct()
	{
		$this->name = 'leocustomajax';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'LeoTheme';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Leo Custom Ajax');
		$this->description = $this->l('Display product number of category and show rating.');
	}
	
	public function install()
	{
		if (parent::install() == false ||
			!$this->registerHook('footer') ||
                        !$this->registerHook('productfooter') ||
			!Configuration::updateValue('leo_customajax_pn', 1) ||
                        !Configuration::updateValue('leo_customajax_img', 1) ||
                        !Configuration::updateValue('leo_customajax_tran', 1) ||
                        !Configuration::updateValue('leo_customajax_qv', 1) ||
			!Configuration::updateValue('leo_customajax_rt', 1)) 
				return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall() ||
			!$this->unregisterHook('footer') ||
                        !$this->unregisterHook('productfooter') ||
			!Configuration::deleteByName('leo_customajax_pn') ||
			!Configuration::deleteByName('leo_customajax_rt') ||
                        !Configuration::deleteByName('leo_customajax_tran') ||
                        !Configuration::deleteByName('leo_customajax_qw') ||
                        !Configuration::deleteByName('leo_customajax_img')
			)
				return false;
		return true;
		
		return (parent::uninstall() || $this->unregisterHook('header'));
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitCustomAjax'))
		{
			
			Configuration::updateValue('leo_customajax_pn', Tools::getValue('leo_customajax_pn'));
			Configuration::updateValue('leo_customajax_rt', Tools::getValue('leo_customajax_rt'));
                        Configuration::updateValue('leo_customajax_img', Tools::getValue('leo_customajax_img'));
                        Configuration::updateValue('leo_customajax_tran', Tools::getValue('leo_customajax_tran'));
                        Configuration::updateValue('leo_customajax_qv', Tools::getValue('leo_customajax_qv'));
			
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				
				  
				<label>'.$this->l('Transition Product Image.').'</label>
				<div class="margin-form">
					<input type="radio" name="leo_customajax_tran" id="tran_display_on" value="1" '.(Tools::getValue('leo_customajax_tran', Configuration::get('leo_customajax_tran')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="tran_display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="leo_customajax_tran" id="tran_display_off" value="0" '.(!Tools::getValue('leo_customajax_tran', Configuration::get('leo_customajax_tran')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="tran_display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
                                        <br/>'.$this->l('You can add this code in tpl file of module you want to Transition product image:').'
                                        <br/>
                                        <textarea style="width: 772px; height: 111px;">
                                        Put this code
                                        <span class="product-additional" rel="{$product.id_product}"></span>
                                        inner
                                        <a href="{$product.link}" title="{$product.name|escape:html:\'UTF-8\'}" class="product_image"></a>
                                        
                                        </textarea>
				</div>
               
                             
                <label>'.$this->l('Show quick View.').'</label>
				<div class="margin-form">
					<input type="radio" name="leo_customajax_qv" id="qv_display_on" value="1" '.(Tools::getValue('leo_customajax_qv', Configuration::get('leo_customajax_qv')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="qv_display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="leo_customajax_qv" id="qv_display_off" value="0" '.(!Tools::getValue('leo_customajax_qv', Configuration::get('leo_customajax_qv')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="qv_display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
                                        <br/>'.$this->l('You can add this code in tpl file of module which you want to show quickview button:').'
                                        <br/>
                                        <textarea style="width: 772px; height: 111px;">
                                        Put this code
                                        <a class="quick-view" title="{l s=\'Quick View\'}" href="{if $product.link|strpos:"?"}{$product.link|cat:\'&content_only=1\'|escape:\'htmlall\':\'UTF-8\'}{else}{$product.link|cat:\'?content_only=1\'|escape:\'htmlall\':\'UTF-8\'}{/if}">{l s=\'Quick View\'}</a>
                                        
                                        </textarea>
                                        <br/><p>'.$this->l('Please edit {l s=\'Quick View\'} to {l s=\'Quick View\' mod=\'XXX\'} when you use this code in module, with XXX is module name').'</p>
                                        <br/><p>'.$this->l('You can edit fancybox width in file /modules/leocustomajax/footer.tpl line 73: $(\'.quick-view\').fancybox(...').'</p>
				</div>
                                
                                 
				<center><input type="submit" name="submitCustomAjax" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
        public function hookProductFooter($params)
	{
            $leo_customajax_qv = Configuration::get('leo_customajax_qv');
            $this->smarty->assign('on_ajaxcart', (int)(Configuration::get('PS_BLOCK_CART_AJAX')));
            if($leo_customajax_qv && (int)Tools::getValue('content_only')) return $this->display(__FILE__, 'quick_view.tpl');
	}
	
	public function hookFooter()
	{
		$leo_customajax_img = Configuration::get('leo_customajax_img');
		$leo_customajax_tran = Configuration::get('leo_customajax_tran');
		$leo_customajax_qv = Configuration::get('leo_customajax_qv');
		$leo_customajax_pn = Configuration::get('leo_customajax_pn');
		$leo_customajax_rt = Configuration::get('leo_customajax_rt');
		$this->smarty->assign(array(
			'leo_customajax_img'=>$leo_customajax_img,
			'leo_customajax_tran'=>$leo_customajax_tran,
			'leo_customajax_qv'=>$leo_customajax_qv,
			'leo_customajax_pn'=>$leo_customajax_pn,
			'leo_customajax_rt'=>$leo_customajax_rt
		));
	   
		if($leo_customajax_qv) $this->context->controller->addjqueryPlugin('fancybox');
		
		$this->context->controller->addJS(($this->_path).'leocustomajax.js');
		if($leo_customajax_img || $leo_customajax_tran || $leo_customajax_qv || $leo_customajax_pn || $leo_customajax_rt) 
		$this->context->controller->addCSS(($this->_path).'leocustomajax.css', 'all');
		
		if($leo_customajax_img){
			$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll'));
		}
		return $this->display(__FILE__, 'footer.tpl');
	}
        
        
        /**
	 * Get Grade By product
	 *
	 * @return array Grades
	 */
	public static function getGradeByProducts($listProduct)
	{
		$validate = Configuration::get('PRODUCT_COMMENTS_MODERATE');
                $id_lang = (int)Context::getContext()->language->id;    

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT pc.`id_product_comment`, pcg.`grade`, pccl.`name`, pcc.`id_product_comment_criterion`, pc.`id_product`
		FROM `'._DB_PREFIX_.'product_comment` pc
		LEFT JOIN `'._DB_PREFIX_.'product_comment_grade` pcg ON (pcg.`id_product_comment` = pc.`id_product_comment`)
		LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion` pcc ON (pcc.`id_product_comment_criterion` = pcg.`id_product_comment_criterion`)
		LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_lang` pccl ON (pccl.`id_product_comment_criterion` = pcg.`id_product_comment_criterion`)
		WHERE pc.`id_product` in ('.$listProduct.')
		AND pccl.`id_lang` = '.(int)$id_lang.
		($validate == '1' ? ' AND pc.`validate` = 1' : '')));
	}
        
        /**
	 * Return number of comments and average grade by products
	 *
	 * @return array Info
	 */
	public static function getGradedCommentNumber($listProduct)
	{
		$validate = (int)Configuration::get('PRODUCT_COMMENTS_MODERATE');
                
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT COUNT(pc.`id_product`) AS nbr, pc.`id_product` 
		FROM `'._DB_PREFIX_.'product_comment` pc
		WHERE `id_product` in ('.$listProduct.')'.($validate == '1' ? ' AND `validate` = 1' : '').'
		AND `grade` > 0 GROUP BY pc.`id_product`');
		return $result;
	}
        
        
        
        public static function getByProduct($id_product)
	{
		$id_lang = (int)Context::getContext()->language->id;
                
                if (!Validate::isUnsignedId($id_product) ||
			!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		$alias = 'p';
		$table = '';
		// check if version > 1.5 to add shop association
		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$table = '_shop';
			$alias = 'ps';
		}
		return Db::getInstance()->executeS('
			SELECT pcc.`id_product_comment_criterion`, pccl.`name`
			FROM `'._DB_PREFIX_.'product_comment_criterion` pcc
			LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_lang` pccl
				ON (pcc.id_product_comment_criterion = pccl.id_product_comment_criterion)
			LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_product` pccp
				ON (pcc.`id_product_comment_criterion` = pccp.`id_product_comment_criterion` AND pccp.`id_product` = '.(int)$id_product.')
			LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_category` pccc
				ON (pcc.`id_product_comment_criterion` = pccc.`id_product_comment_criterion`)
			LEFT JOIN `'._DB_PREFIX_.'product'.$table.'` '.$alias.'
				ON ('.$alias.'.id_category_default = pccc.id_category AND '.$alias.'.id_product = '.(int)$id_product.')
			WHERE pccl.`id_lang` = '.(int)($id_lang).'
			AND (
				pccp.id_product IS NOT NULL
				OR ps.id_product IS NOT NULL
				OR pcc.id_product_comment_criterion_type = 1
			)
			AND pcc.active = 1
			GROUP BY pcc.id_product_comment_criterion
		');
	}
        
        public function hookProductMoreImg($listPro)
	{
            $id_lang = Context::getContext()->language->id;
            //get product info
            $productList = $this->getProducts($listPro,$id_lang);
            
            $this->smarty->assign(array(
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
                'mediumSize' => Image::getSize(ImageType::getFormatedName('medium'))	
            ));
            
            $obj = array();
            foreach ($productList as $key=>$product){
                $this->smarty->assign('product',$product);
                $obj[] = array("id"=>$product["id_product"],"content"=>utf8_encode($link->getImageLink($product["link_rewrite"], $product["id_image"], 'home_default')));
            }
            return $obj;
            //return Tools::jsonEncode($obj);
	}
        
        public function hookProductOneImg($listPro)
	{
            global $link;
            $id_lang = Context::getContext()->language->id;
            $where  = " WHERE i.`id_product` IN (".$listPro.") AND i.`cover`=0";
            $order  = " ORDER BY i.`id_product`,`position`";
            $limit  = " LIMIT 0,1";
            //get product info
            $listImg = $this->getAllImages($id_lang, $where, $order, $limit);
            $savedImg = array();
            $obj = array();
            foreach ($listImg as $product){
                if(!in_array($product["id_product"], $savedImg))
                $obj[] = array("id"=>$product["id_product"],"content"=>utf8_encode($link->getImageLink("", $product["id_image"], 'home_default')));
                $savedImg[] = $product["id_product"];
            }
            //print_r($obj);die;
            return $obj;
	}
        
        public function getProducts($productList,$id_lang)
	{                        
		$sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
				WHERE pl.`id_lang` = '.(int)$id_lang.
                                        ' AND p.`id_product` in ('.$productList .')';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		
                if($productList){
                    $tmpImg = array();
                    $coverImg = array();
                    $where  = " WHERE i.`id_product` IN (".$productList.")";
                    $order  = " ORDER BY i.`id_product`,`position`";

                    switch(Configuration::get('LEO_MINFO_SORT')){
                            case "position2":
                                    break;
                            case "random":
                                    $order = " ORDER BY RAND()";
                                    break;
                            default:
                                    $order = " ORDER BY i.`id_product`,`position` DESC";
                    }


                    $listImg = $this->getAllImages($id_lang, $where, $order);
                    foreach($listImg as $val){
                        $tmpImg[$val["id_product"]][$val["id_image"]] = $val;
                        if($val["cover"]==1)
                        $coverImg[$val["id_product"]] = $val["id_image"];
                    }
                }
                foreach ($result as &$val){
                    if(isset($tmpImg[$val["id_product"]])){
                        $val["images"] =  $tmpImg[$val["id_product"]];
                        $val["id_image"] =  $coverImg[$val["id_product"]];
                    }else{
                        $val["images"] =  array();
                    }
                }
                
                return Product::getProductsProperties($id_lang, $result);
	}
        
        
        public function getAllImages($id_lang, $where, $order){
		$sql = 'SELECT i.`id_product`, image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`, pl.`link_rewrite`
				FROM `'._DB_PREFIX_.'image` i
				'.Shop::addSqlAssociation('image', 'i').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (i.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
				'.$where.' '.$order;
		
		return Db::getInstance()->executeS($sql);
	}
        
}