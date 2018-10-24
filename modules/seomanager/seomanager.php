<?php
/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/

if (!defined('_PS_VERSION_'))
	exit;

class SEOManager extends Module {
	private $html = '';
	private $postErrors = array ();
	private $shortcodetab = array();
	/**
	 *
	 */
	public function __construct()
	{
		$this->name = 'seomanager';
		$this->tab = 'seo';
		$this->version = '2.4.9';
		$this->author = 'onasus.com';
		$this->bootstrap = true;
		/* initialisation du tableau des shortcodes possibles*/
		$this->shortcodetab = array(
			'category'		=> array('{CATEGORY_NAME}', '{CATEGORY_DESC}', '{CATEGORY_NAME_PARENT}', '{SHOP_NAME}'),
			'cms'			=> array('{CMS_NAME}', '{SHOP_NAME}'),
			'index'			=> array('{SHOP_NAME}'),
			'manufacture'	=> array('{SHOP_NAME}', '{MANUFACTURER_NAME}'),
			'product'		=> array('{PRODUCT_NAME}', '{PRODUCT_DESC}', '{PRODUCT_DESC_SHORT}'
								, '{PRODUCT_ID}', '{PRODUCT_REF}'
								, '{PRODUCT_SUPPLIER_REF}', '{PRODUCT_PRICE}', '{PRODUCT_PRICE_TAX}', '{PRODUCT_CONDITION}', '{PRODUCT_EAN13}', '{MANUFACTURER_NAME}', '{SUPPLIER_NAME}'
								, '{PRODUCT_UPC}', '{CATEGORY_NAME}', '{CATEGORY_NAME_PARENT}', '{SHOP_NAME}'),
			'search'		=> array('{SHOP_NAME}'),
			'supplier'		=> array('{SHOP_NAME}', '{SUPPLIER_NAME}'),
			'contactus'		=> array('{SHOP_NAME}')
		);
		/**/
		parent::__construct ();
		/**/
		$this->displayName = $this->l('SEO Manager');
		$this->description = $this->l('Generate your Search Engine Optimization meta data with 1 click');
	}
	/**
	 *
	 * @return boolean
	 */
	public function install()
	{
		if (! parent::install() 
		|| !Configuration::updateValue($this->name.'-config', $this->initSeoMngrConfigField())
		|| !$this->registerHook('header'))
			return false;
		return true;
	}
	
	public function hookHeader()
	{
		$page = (int)Tools::getValue('p', false);
		$shop_name = Configuration::get('PS_SHOP_NAME');
		
		$page_name = '';
		if (Tools::getValue('id_product'))
			$page_name = 'product';
		if (Tools::getValue('id_category'))
			$page_name = 'category';
		elseif (Tools::getValue('id_manufacturer'))
			$page_name = 'manufacturer';
		elseif (Tools::getValue('id_supplier'))
			$page_name = 'supplier';
		elseif (Tools::getValue('id_cms') || Tools::getValue('id_cms_category'))
			$page_name = 'cms';
		
		$metas = Meta::getMetaTags($this->context->language->id, $page_name);

		if($page > 1)
		{
			if($metas['meta_title'] != '')
				if(stristr($metas['meta_title'],$shop_name))
					$metas['meta_title'] = str_ireplace($shop_name,$this->l('page').' '.$page.' - '.$shop_name, $metas['meta_title']);
				else
					$metas['meta_title'].' - '.$this->l('page').' '.$page;
			
			$this->context->smarty->assign(
				array(
					'meta_title' => $metas['meta_title'],
					'meta_description' => $metas['meta_description'] != '' ? $metas['meta_description'].' - '.$this->l('page').' '.$page : ''
				)
			);
		}
		return $this->display(__FILE__, 'views/header.tpl');
	}
	
	public function uninstall()
	{
		if (! parent::uninstall ())
			return false;
		return true;
	}
	
	public function initSeoMngrConfigField()
	{
		$tab = array();
		$tab[$this->name.'categorytitlemeta'] 		= '{CATEGORY_NAME}-{CATEGORY_DESC}-{CATEGORY_NAME_PARENT}-{SHOP_NAME}';
		$tab[$this->name.'categorytitlemeta'] 		= '{CATEGORY_NAME}-{CATEGORY_DESC}-{CATEGORY_NAME_PARENT}-{SHOP_NAME}'; 
		$tab[$this->name.'categorydescrmeta'] 		= '{CATEGORY_NAME}-{CATEGORY_DESC}-{CATEGORY_NAME_PARENT}-{SHOP_NAME}'; 
		$tab[$this->name.'cmstitlemeta'] 			= '{CMS_NAME}-{SHOP_NAME}';
		$tab[$this->name.'cmsdescrmeta'] 			= '{CMS_NAME}-{SHOP_NAME}';
		$tab[$this->name.'indextitlemeta'] 			= '{SHOP_NAME}'; 
		$tab[$this->name.'indexdescmeta'] 			= '{SHOP_NAME}'; 
		$tab[$this->name.'manufacturetitlemeta'] 	= '{MANUFACTURER_NAME}-{SHOP_NAME}'; 
		$tab[$this->name.'manufacturedescmeta']		= '{MANUFACTURER_NAME}-{SHOP_NAME}'; 
		$tab[$this->name.'producttitlemeta'] 		= '{PRODUCT_NAME}-{PRODUCT_ID}-{PRODUCT_REF}-{PRODUCT_DESC_SHORT}-{CATEGORY_NAME}-{CATEGORY_NAME_PARENT}-{SHOP_NAME}';
		$tab[$this->name.'productdescemeta'] 		= '{PRODUCT_NAME}-{PRODUCT_ID}-{PRODUCT_REF}-{PRODUCT_EAN13}-{PRODUCT_DESC_SHORT}-{CATEGORY_NAME}-{CATEGORY_NAME_PARENT}-{SHOP_NAME}'; 
		$tab[$this->name.'searchtitlemeta'] 		= '{SHOP_NAME}'; 
		$tab[$this->name.'searchdescmeta'] 			= '{SHOP_NAME}'; 
		$tab[$this->name.'suppliertitlemeta'] 		= '{SHOP_NAME}-{SUPPLIER_NAME}'; 
		$tab[$this->name.'supplierdescmeta'] 		= '{SHOP_NAME}-{SUPPLIER_NAME}'; 
		$tab[$this->name.'contactustitlemeta'] 		= '{SHOP_NAME}'; 
		$tab[$this->name.'contactusdescmeta'] 		= '{SHOP_NAME}';

		$result = array();
		foreach (Language::getLanguages(false) as $language)
			$result[$language['id_lang']] =  $tab;
		
		return serialize($result);
	}
	
	public function getSeoMngrConfigField($field)
	{
		$lang = (int)Tools::getValue('seomngr_lang',1);
		$seomngr_config = unserialize(Configuration::get($this->name.'-config'));
		return $seomngr_config[(int)$lang][$field];
	}
	
	public function setSeoMngrConfigField($field, $value = false)
	{
		$lang = (int)Tools::getValue('seomngr_lang',1);
		$seomngr_config = unserialize(Configuration::get($this->name.'-config'));
		$seomngr_config[(int)$lang][$field] = $value;
		return Configuration::updateValue($this->name.'-config', serialize($seomngr_config));
	}
	
	public function getContent()
	{
		$output = '<script language="JavaScript" type="text/javascript" src="'.
		$this->_path.'js/seomanager.js">
		</script><h2>'.$this->l('SEO Manager Configuration:');

		foreach (Language::getLanguages(false) as $language)
			$output .= '<a href="#" style="margin-left:5px"><img id="select'.$language['iso_code'].'" onclick="changeLanguage(this)" name='.$language['id_lang'].
			' src="'._THEME_LANG_DIR_.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'"></a>';

		$output .= '</h2>';

		if (Tools::isSubmit('submitSeomanager'))
		{
			include_once (dirname( __FILE__ ).'/seomanagerhelper.php');
			include_once (dirname( __FILE__ ).'/DeBug.php');
			$shopid = $this->context->shop->id;
			$shopname = $this->context->shop->name;
			$languageid = Tools::getValue('languageName');

			$seohelper = new SEOManagerHelper();

			$overrideProducts = Tools::getValue('overrideProducts');

			###############################################category####################################################
			$categorytype = Tools::getValue('categoryrad');
			$categorytitle = Tools::getValue('categorytitlemeta');
			$categorydesc = Tools::getValue('categorydescmeta');
			$this->setSeoMngrConfigField($this->name.'categorytitlemeta', $categorytitle);
			$this->setSeoMngrConfigField($this->name.'categorydescrmeta', $categorydesc);
			$seohelper->updateCategory($categorytitle, $categorytype, $languageid, $shopid, $categorydesc, $shopname, $overrideProducts);
			###############################################end category####################################################

			############################################### CMS ####################################################
			$cmstype = Tools::getValue('cmsrad');
			$cmstitle = Tools::getValue('cmsitlemeta');
			$cmsdesc = Tools::getValue('cmsdescmeta');
			$this->setSeoMngrConfigField($this->name.'cmstitlemeta', $cmstitle );
			$this->setSeoMngrConfigField($this->name.'cmsdescrmeta', $cmsdesc );

			$seohelper->updateCMS($cmstitle, $cmstype, $languageid, $cmsdesc, $shopname, $shopid);//$cmstitle, $type, $languageID, $cmsdesc, $shopname
			############################################### END CMS ####################################################

			############################################### INDEX ####################################################
			$indextype = Tools::getValue('indexrad');
			$indextitle = Tools::getValue('indextitlemeta');
			$indexdesc = Tools::getValue('indexdescmeta');
			$this->setSeoMngrConfigField($this->name.'indextitlemeta', $indextitle );
			$this->setSeoMngrConfigField($this->name.'indexdescmeta', $indexdesc );

			$seohelper->updateIndex($indextitle, $indextype, $languageid, $indexdesc, $shopname, $shopid);
			############################################### END INDEX ####################################################

			############################################### MANUFACTURER ####################################################
			$manfacaturertype = Tools::getValue('manufacturerad');
			$manfacaturertitle = Tools::getValue('manufacturetitlemeta');
			$manfacaturerdesc = Tools::getValue('manufacturedescmeta');
			$this->setSeoMngrConfigField($this->name.'manufacturetitlemeta', $manfacaturertitle );
			$this->setSeoMngrConfigField($this->name.'manufacturedescmeta', $manfacaturerdesc );

			$seohelper->updateManufacture($manfacaturertitle, $manfacaturertype, $manfacaturerdesc, $languageid, $shopname, $shopid);
			############################################### END  MANUFACTURER ####################################################

			############################################### CONTACT US ####################################################
			$contactustype = Tools::getValue('contactusrad');
			$contactustitle = Tools::getValue('contactustitlemeta');
			$contactusdesc = Tools::getValue('contactusdescmeta');
			$this->setSeoMngrConfigField($this->name.'contactustitlemeta', $contactustitle );
			$this->setSeoMngrConfigField($this->name.'contactusdescmeta', $contactusdesc);

			$seohelper->updateContactus($contactustype, $contactustitle, $contactusdesc, $shopid, $shopname, $languageid);
			############################################### END CONTACT US ####################################################

			############################################### SUPPLY ####################################################
			$supplytype = Tools::getValue('supplyrad');
			$supplytitle = Tools::getValue('supplytitlemeta');
			$supplydesc = Tools::getValue('supplydescmeta');
			$this->setSeoMngrConfigField($this->name.'suppliertitlemeta', $supplytitle );
			$this->setSeoMngrConfigField($this->name.'supplierdescmeta', $supplydesc);

			$seohelper->updateSupplier($supplytype, $supplytitle, $supplydesc, $shopid, $shopname, $languageid);
			############################################### END SUPPLY ####################################################

			############################################### SEARCH ####################################################
			$searchtype = Tools::getValue('searchrad');
			$searchtitle = Tools::getValue('searchtitlemeta');
			$searchdesc = Tools::getValue('searchdescmeta');
			$this->setSeoMngrConfigField($this->name.'searchtitlemeta', $searchtitle );
			$this->setSeoMngrConfigField($this->name.'searchdescmeta', $searchdesc);

			$seohelper->updateSearch($searchtype, $searchtitle, $searchdesc, $shopid, $shopname, $languageid);
			############################################### END SEARCH ####################################################

			############################################### PRODUCT ####################################################
			$producttype = Tools::getValue('productrad');
			$producttitle = Tools::getValue('producttitlemeta');
			$productdesc = Tools::getValue('productdescmeta');
			//$overrideProducts = Tools::getValue('overrideProducts');
			$filteredCats = array(0);
			if (Tools::getIsset('productcatselected'))
				$filteredCats = Tools::getValue('productcatselected');

			$this->setSeoMngrConfigField($this->name.'producttitlemeta', $producttitle);
			$this->setSeoMngrConfigField($this->name.'productdescemeta', $productdesc);

			$seohelper->updateProductCatFiltered($producttype, $producttitle, $productdesc, $filteredCats, $shopid, $shopname, $languageid, $overrideProducts);
			############################################### END PRODUCT ####################################################

			$output .= ' <div class="conf confirm">'.$this->l('Updated Success').'</div>';
		}
		return $output.$this->displayForm ();
		//return $this->display(__FILE__, 'views/admin-list.tpl');
	}

	/**
	 * @return string
	 */
	public function displayForm()
	{
		$seomngr_lang = (int)Tools::getValue('seomngr_lang',1);
		return '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="'.$this->_path.'/js/admin.js"></script>
		<link href="'.$this->_path.'views/bt/css/bootstrap.css" rel="stylesheet">
		<form class="form-horizontal" role="form" action="'.Tools::safeOutput ( $_SERVER['REQUEST_URI'] ).'" method="post" enctype="multipart/form-data">
		<fieldset>
		<legend>'.$this->l('Language Selected : ').
		'<img id="selectedLanguage"  src="'._THEME_LANG_DIR_.$seomngr_lang.'.jpg" alt="" style="vertical-align:baseline"/></legend>

		<div class="form-group">
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="overrideProducts" value="yes"> '.$this->l('Override existing product or category meta data').'
				</label>
				</div>
				<input type="hidden" name="languageName" id="languageID" value='.$seomngr_lang.'>
			</div>
		</div>'.
		$this->_drawCategoryForm ().'<hr/>'.$this->_drawCmsForm ().'<hr/>'.
		$this->_drawIndexForm ().'<hr/>'.$this->_drawManufactureForm().'<hr/>'.$this->_drawProductForm().'<hr/>'.
		$this->_drawSearchForm().'<hr/>'.$this->_drawSupplyForm().'<hr/>'.$this->_drawContactusForm().'

		<div class="form-group">
			<div class="col-lg-offset-2 col-lg-10">
				<button type="submit" class="btn btn-success" name="submitSeomanager">Submit</button>
			</div>
		</div>
		</fieldset>
		</form>
		<script src="'.$this->_path.'views/bt/js/bootstrap.min.js"></script>
		<!--script src="'.$this->_path.'views/js/tooltip.js"></script>
		<script src="'.$this->_path.'views/js/popover.js"></script-->
		<script src="'.$this->_path.'views/js/seomanager.js"></script>
		';
	}
	
	/**
	 * @return string
	 */
	public function _drawCategoryForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['category'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('category pages').'</h4></div>
		<br/><br/>
		<div class="form-group"><!-- category title -->
			<label for="categorytitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="categorytitlemeta" name="categorytitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'categorytitlemeta').'" >
				<div id="categorytitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-category" class="shortcodeselector"  data-metainput="categorytitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-category" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-category" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>


		</div><!-- /category title -->

		<div class="form-group"><!-- category description -->
			<label for="categorydescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="categorydescmeta" name="categorydescmeta" value="'.$this->getSeoMngrConfigField($this->name.'categorydescrmeta').'">
				<div id="categorydescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-category" class="shortcodeselector"  data-metainput="categorydescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-category" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-category" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>

			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="categoryrad" > '.$this->l('Update Categories').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="categoryradhidden"/>
			</div>
		</div><!--/ end category description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	public function _drawCmsForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['cms'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('CMS pages').'</h4></div>

		<div class="form-group"><!-- CMS title -->
			<label for="cmsitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="cmstitlemeta" name="cmsitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'cmstitlemeta').'">
				<div id="cmstitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-cms" class="shortcodeselector"  data-metainput="cmstitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-cms" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-cms" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>

		</div><!-- /end CMS title -->
		<div class="form-group"><!-- CMS description -->
			<label for="cmsdescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="cmsdescmeta" name="cmsdescmeta" value="'.$this->getSeoMngrConfigField($this->name.'cmsdescrmeta').'">
				<div id="cmsdescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-cms" class="shortcodeselector"  data-metainput="cmsdescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-cms" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-cms" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>

			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="cmsrad" > '.$this->l('Update CMS pages').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="cmsradhidden"/>
			</div>
		</div><!-- /end CMS description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	public function _drawIndexForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['index'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('index page').'</h4></div>
		<div class="form-group"><!-- Index title -->
			<label for="indextitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="indextitlemeta" name="indextitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'indextitlemeta').'">
				<div id="indextitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-index" class="shortcodeselector"  data-metainput="indextitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-index" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-index" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
		</div><!-- /Index title -->
		<div class="form-group"><!-- Index description -->
			<label for="indexdescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="indexdescmeta" name="indexdescmeta" value="'.$this->getSeoMngrConfigField($this->name.'indexdescmeta').'">
				<div id="indexdescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-index" class="shortcodeselector"  data-metainput="indexdescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-index" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-index" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="indexrad" > '.$this->l('Update Index page').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="indexradhidden"/>
			</div>
		</div><!-- /end Index description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	public function _drawManufactureForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['manufacture'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('manufacturer pages').'</h4></div>
		<div class="form-group"><!-- Manufacturer title -->
			<label for="manufacturetitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="manufacturetitlemeta" name="manufacturetitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'manufacturetitlemeta').'">
				<div id="manufacturetitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-manufacture" class="shortcodeselector"  data-metainput="manufacturetitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-manufacture" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-manufacture" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
		</div><!-- /end Manufacturer title -->

		<div class="form-group"><!-- Manufacturer description -->
			<label for="manufacturedescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="manufacturedescmeta" name="manufacturedescmeta" value="'.$this->getSeoMngrConfigField($this->name.'manufacturedescmeta').'">
				<div id="manufacturedescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-manufacture" class="shortcodeselector"  data-metainput="manufacturedescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-manufacture" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-manufacture" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="manufacturerad" > '.$this->l('Update Manufacturer pages').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="manufactureradhidden"/>
			</div>
		</div><!-- /end Manufacturer description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return type
	 */
	private function _getCategories()
	{
		include_once (dirname( __FILE__ ).'/DeBug.php');
		// global $smarty, $cookie;

		$cache_id = 'seomgt_getCategoriesWithoutParent_'.(int)Context::getContext()->language->id;
		if (!Cache::isStored($cache_id))
		{
			$sql  = '
			SELECT c.id_category, cl.`name`
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)Context::getContext()->language->id.' AND cl.id_shop = '.Context::getContext()->shop->id.')
			LEFT JOIN '._DB_PREFIX_.'category_shop cs ON(c.id_category = cs.id_category AND cs.id_shop = '.Context::getContext()->shop->id.')
			WHERE c.active = 1';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			//DeBug::logmessage($sql);
			Cache::store($cache_id, $result);
		}

		$categories = Cache::retrieve($cache_id);

		$data_all = array();
		$data_all[] = array();
		$data_all[] = array('value' => '0', 'name' => 'All');
		foreach ($categories as $cat)
		{
			$id_cat = $cat['id_category'];
			$cat_name = $cat['name'];
			$data_all[] = array('value' => $id_cat, 'name' => $cat_name);
		}
		return array('catogories' => $data_all);
	}


	/**
	 * @return string
	 */
	public function _drawProductForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['product'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$data_cat = $this->_getCategories();
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('Product pages').'</h4></div>
		<div class="form-group"><!-- Product title -->
			<div style="margin-bottom:6px;" class="row">
				<div class="col-md-2 col-md-offset-3">
				<select multiple="multiple" name="productcatselected[]" size="6" class="form-control">';
		foreach ($data_cat['catogories'] as $item)
		{
			$name = isset($item['name'])?$item['name']:'';
			$value = isset($item['value'])?$item['value']:'';
			if (Tools::strlen($name) == 0) continue;
			$output .= '<option  value="'.$value.'">'.$name.'</option>';
		}

			$output .= '</select>
				</div>
			</div>
			<label for="producttitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="producttitlemeta" name="producttitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'producttitlemeta').'">
				<div id="producttitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-product" class="shortcodeselector"  data-metainput="producttitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-product" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-product" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
		</div><!-- /end Product title -->

		<div class="form-group"><!-- Product description -->
			<label for="productdescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="productdescmeta" name="productdescmeta" value="'.$this->getSeoMngrConfigField($this->name.'productdescemeta').'">
				<div id="productdescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-product" class="shortcodeselector"  data-metainput="productdescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-product" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-product" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="productrad" > '.$this->l('Update Product pages').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="productradhidden"/>
			</div>
		</div><!-- /end Product description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	public function _drawSearchForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['search'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('Search page').'</h4></div>

		<div class="form-group"><!-- Search title -->
			<label for="searchtitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="searchtitlemeta" name="searchtitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'searchtitlemeta').'">
				<div id="searchtitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-search" class="shortcodeselector"  data-metainput="searchtitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-search" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-search" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
		</div><!-- /end Search title -->

		<div class="form-group"><!-- Search description -->
			<label for="searchdescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="searchdescmeta" name="searchdescmeta" value="'.$this->getSeoMngrConfigField($this->name.'searchdescmeta').'">
				<div id="searchdescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-search" class="shortcodeselector"  data-metainput="searchdescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-search" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-search" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="searchrad" > '.$this->l('Update Search page').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="searchradhidden"/>
			</div>
		</div><!-- /end Search description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	public function _drawSupplyForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['supplier'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('Supplier pages').'</h4></div>

		<div class="form-group"><!-- Supplier title -->
			<label for="supplytitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="supplytitlemeta" name="supplytitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'suppliertitlemeta').'">
				<div id="supplytitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-supply" class="shortcodeselector"  data-metainput="supplytitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-supply" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-supply" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
		</div><!-- /end Supplier title -->

		<div class="form-group"><!-- Supplier description -->
			<label for="supplydescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="supplydescmeta" name="supplydescmeta" value="'.$this->getSeoMngrConfigField($this->name.'supplierdescmeta').'">
				<div id="supplydescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-supply" class="shortcodeselector"  data-metainput="supplydescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-supply" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-supply" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="supplyrad" > '.$this->l('Update Supplier page').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="supplyradhidden"/>
			</div>
		</div><!-- /end Supplier description -->
		';
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	public function _drawContactusForm()
	{
		$options = '<option value="">'.$this->l('Choose a shortcode').'</option>';
		foreach ($this->shortcodetab['contactus'] as $code)
			$options .= '<option value="'.$code.'">'.$code.'</option>';
		$output = '
		<div class="col-lg-offset-2 col-lg-10"><h4>'.$this->l('Contact page').'</h4></div>

		<div class="form-group"><!-- Contact title -->
			<label for="contactustitlemeta" class="col-lg-2 control-label">'.$this->l('Title:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="contactustitlemeta" name="contactustitlemeta" value="'.$this->getSeoMngrConfigField($this->name.'contactustitlemeta').'">
				<div id="contactustitlemeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="title-shortcodeselector-contactus" class="shortcodeselector"  data-metainput="contactustitlemeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="title-shortcodeadder-contactus" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="title-shortcodereset-contactus" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>

		</div><!-- /end Contact title -->

		<div class="form-group"><!-- Contact description -->
			<label for="contactusdescmeta" class="col-lg-2 control-label">'.$this->l('Description:').'</label>
			<div class="col-lg-5 tooltip-demo">
				<input class="form-control" id="contactusdescmeta" name="contactusdescmeta" value="'.$this->getSeoMngrConfigField($this->name.'contactusdescmeta').'">
				<div id="contactusdescmeta-tags"></div>
			</div>
			<div class="col-lg-5">
				<div class="col-md-6">
					<select id="desc-shortcodeselector-contactus" class="shortcodeselector"  data-metainput="contactusdescmeta">
						'.$options.'
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" id="desc-shortcodeadder-contactus" class="btn btn-info shortcodeadder">
						'.$this->l('Add').'
					</button>
					<button type="button" id="desc-shortcodereset-contactus" class="btn btn-danger shortcodereset">
						'.$this->l('Empty').'
					</button>
				</div>
			</div>
			<div class="col-lg-offset-2 col-lg-10">
				<div class="checkbox">
				<label>
					<input type="checkbox" name="contactusrad" > '.$this->l('Update Contact page').'
				</label>
				</div>
				<input type="hidden" value="Customer" name="contactusradhidden"/>
			</div>
		</div><!-- /end Contact description -->
		';
		return $output;
	}
}