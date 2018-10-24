<?php
/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/

if(!defined('_PS_VERSION_'))
	exit;

require_once('DeBug.php');
require_once('SimpleLogger.php');
require_once('util.php');
class SEOManagerHelper extends Module
{
	private $_categorytitle 	= array('{CATEGORY_NAME}','{CATEGORY_DESC}', '{CATEGORY_NAME_PARENT}', '{SHOP_NAME}');
	private $_cmstitle 			= array('{CMS_NAME}', '{SHOP_NAME}');
	private $_indextitle 		= array('{SHOP_NAME}');
	private $_manufacturetitle 	= array('{MANUFACTURER_NAME}', '{SHOP_NAME}');
	private $_producttitle 		= array('{PRODUCT_NAME}', '{PRODUCT_DESC}', '{PRODUCT_DESC_SHORT}', '{PRODUCT_ID}', '{PRODUCT_REF}'
	, '{PRODUCT_SUPPLIER_REF}', '{PRODUCT_PRICE}', '{PRODUCT_CONDITION}', '{PRODUCT_EAN13}', '{MANUFACTURER_NAME}', '{SUPPLIER_NAME}'
	, '{PRODUCT_UPC}', '{CATEGORY_NAME}', '{CATEGORY_NAME_PARENT}', '{SHOP_NAME}', '{PRODUCT_PRICE_TAX}');
	private $_searchtitle 		= array('{SHOP_NAME}');
	private $_suppliertitle 	= array('{SHOP_NAME}', '{SUPPLIER_NAME}');
	private $_contactustitle 	= array('{SHOP_NAME}');

	public function __construct()
	{

	}	
	public function updateProductCatFiltered($type, $producttitle, $productdesc, $filteredCats, $shopid, $shopname, $languageid, $overrideProducts)
	{
		//add array and extract
		$pagename = 'product';
		//Logger::addLog($producttitle, 1, null, null, null, false);
		//Logger::addLog($pagename, 1, 1, 'string', 2, false);
		$logger = new  SimpleLogger(dirname(__FILE__).'/debug.log');
		$logger->d("product desc", $productdesc);
		$metaDescCdt = ' ';
		$metaTitleCdt = ' ';
		DeBug::logmessage("overrideProducts: ".$overrideProducts);
		if(isset($overrideProducts) && ($overrideProducts != 'yes'))
		{
			$metaTitleCdt = 'AND  meta_title = \'\''; //'AND meta_title IS NOT NULL AND meta_title <> \'\'';
			$metaDescCdt = 'AND  meta_description = \'\''; //'AND meta_description IS NOT NULL AND meta_description <> \'\'';
		}
		/**/
		if($this->isValidate($productdesc) && $this->isValidate($producttitle) && $this->IsHasInfo($this->_producttitle, $productdesc) && $this->IsHasInfo( $this->_producttitle, $producttitle))
		{
		
			$typelower = Tools::strtolower($type);
			if($typelower == 'on' || ($typelower == 'customer' && $this->isPageInTable($pagename)))
			{
				if($typelower == 'on')
					if(!$this->isPageInTable($pagename))
						$this->InsertPageTable($pagename, $shopid);
				/**/
				if(! isset($filteredCats))
					$filteredCats = array(0);
				$nCountCats = count($filteredCats);
				/*DeBug::logmessage("nCountCats: ".$nCountCats);
				for($i = 0; $i < $nCountCats; $i++)
				{
					DeBug::logmessage("filteredCats: ".$filteredCats[$i]);
				}*/
				$whereORConditionsCats = '';
				if(!in_array(0, $filteredCats))
				{
					$whereORConditionsCats = ' and cl.id_category='.$filteredCats[0];
					if(count($filteredCats) > 1) {						
						for($i = 1; $i < $nCountCats; $i++){
							$whereORConditionsCats .= ' OR cl.id_category='.$filteredCats[$i];
						}
					}

					/*if(count($filteredCats) == 1)
						$whereORConditionsCats = ' and cl.id_category='.$filteredCats[0];
					else
					{
						$whereORConditionsCats = ' and cl.id_category='.$filteredCats[0];
						for($i = 1; $i < $nCountCats; $i++)
							$whereORConditionsCats .= ' OR cl.id_category='.$filteredCats[$i];
					}
					//$whereORConditionsCats = ' and cl.id_category=3 OR cl.id_category=4  ';
					*/
				}
				$sql = 'SELECT pl.name as productname ,  pl.description as productDesc, pl.description_short as productDescShort, 
				cl.id_category as categoryid,cl.name as categoryname,pl.id_product as productid, pduct.ean13 as ean13, 
				pduct.upc as upc, pduct.reference as reference, pduct.supplier_reference as supplier_reference, 
				pduct.price as product_price, pduct.condition as product_condition, sup.name as supplier, manuf.name as manufacturer 
				FROM '._DB_PREFIX_.'product_lang pl 
				left join '._DB_PREFIX_.'product pduct on pl.id_product=pduct.id_product 
				left join '._DB_PREFIX_.'category_product cp on pduct.id_category_default=cp.id_category  
				left join '._DB_PREFIX_.'supplier sup on pduct.id_supplier=sup.id_supplier 
				left join '._DB_PREFIX_.'manufacturer manuf on pduct.id_manufacturer=manuf.id_manufacturer 
				left join '._DB_PREFIX_.'category_lang cl on cl.id_category=cp.id_category 
				WHERE pl.id_shop='.$shopid.' 
				AND pl.id_product=cp.id_product 
				AND cl.id_lang='.$languageid.' 
				AND  pl.id_lang='.$languageid.$whereORConditionsCats;
				//.'AND (pl.meta_description <> "" OR pl.meta_title <> "")';
				//DeBug::logmessage('List of product query: '.$sql);
				/*$sql2 = 'SELECT pl.name as productname ,  pl.description as productDesc, pl.description_short as productDescShort, cl.id_category as categoryid,cl.name as categoryname,
				pl.id_product as productid FROM '._DB_PREFIX_.'product_lang pl left join '._DB_PREFIX_.'category_product cp on pl.id_product=cp.id_product left join '._DB_PREFIX_.'
				category_lang cl on cl.id_category=cp.id_category where pl.id_shop='.$shopid.' and cl.id_lang='.$languageid.' and  pl.id_lang='.$languageid.$whereORConditionsCats;*/
				
				$items = Db::getInstance()->executeS($sql);
				//print_r($items);
				//DeBug::logmessage($items);
				foreach($items as $item)
				{
					$categoryname = $item['categoryname'];
					$categoryid = $item['categoryid'];
					$productname = $item['productname'];
					$productid = $item['productid'];
					$productDesc = self::htlmDecode(self::replaceNewLine($item['productDesc']));
					$productDescShort = self::htlmDecode(self::replaceNewLine($item['productDescShort']));
					
					$logger->d("productDescShort", $productDescShort);
					$productRef = $item['reference'];
					$productRefSupplier = $item['supplier_reference'];
					$productEan13 = $item['ean13'];
					$productUpc = $item['upc'];
					
					// Check language properly
// 					if($languageid == 1)
					// {
// 						$productPrice = number_format($item['product_price'], 2); //
// 						//number_format (number, decimals, decimalpoint, separator).
// 					}
// 					else{//French format
// 						$productPrice = number_format($item['product_price'], 2, ',', ' ');
// 					}
					//$productPrice = number_format($item['product_price'], 2, ',', ' ');
					$productPrice = Tools::displayPrice($item['product_price']);
					$productCondition = $item['product_condition'];
					$productSupplier = $item['supplier'];
					$productManufacturer = $item['manufacturer'];
					
					//getPriceStatic($id_product, $usetax = true, $id_product_attribute = null, $decimals = 6);
					$productPriceTax = Product::getPriceStatic($productid, true, null, 2);
					//$logger->d("Price with tax", $productPriceTax);
					
					$itemColumn = array(
						'categoryname' 	=> $categoryname ,
						'categoryid'	=> $categoryid,
						'productname'	=> $productname,
						'productId'		=> $productid,
						'productDesc'	=> $productDesc,
						'productDescShort'=> $productDescShort,
						'productRef'	=> $productRef,
						'productRefSupplier'=> $productRefSupplier,
						'productEan13'	=> $productEan13,
						'productUpc'	=> $productUpc,
						'productPrice'	=> $productPrice,
						'productPriceTax' => $productPriceTax,	
						'productCondition' => $productCondition,
						'supplierName' =>	$productSupplier,
						'manufacturerName' => $productManufacturer,
						'shopid' => $shopid,
						'languageid' => $languageid,
						'shopname' => $shopname
						
					);
										
					$meta_title = $this->getStringProduct5($producttitle, 70, $itemColumn);
					//$logger->d("meta_title", $meta_title);
					$sqlUpdateMetaTitle = 'UPDATE '._DB_PREFIX_.'product_lang set meta_title="'.
					html_entity_decode(Tools::stripslashes($this->cleanText($meta_title))).'" 
					where id_shop='.$shopid.
					' and id_lang='.$languageid.
					' and id_product='.$productid.' '.$metaTitleCdt;
					//$logger->d("sqlUpdateMetaTitle", $sqlUpdateMetaTitle);
					Db::getInstance()->execute($sqlUpdateMetaTitle);

					/**/
					$meta_description = $this->getStringProduct5($productdesc, 160, $itemColumn);
					$sqlUpdateMetaDesc = 'UPDATE '._DB_PREFIX_.'product_lang set meta_description="'.
					html_entity_decode(Tools::stripslashes($this->cleanText($meta_description))).
					'" where id_shop='.$shopid.
					' and id_lang='.$languageid.
					' and id_product='.$productid.' '.$metaDescCdt;
					//$logger->d("sqlUpdateMetaDesc", $sqlUpdateMetaDesc);
					Db::getInstance()->execute($sqlUpdateMetaDesc);
				}
			}
		}
	}

	 /**
	 *
	 * @param type $category
	 * @param type $type
	 * @param type $languageID
	 * @param type $shopid
	 * @param type $catedesc
	 * @param type $shopname
	 * @param type $overrideProducts
	 */
	public function updateCategory($category, $type, $languageID, $shopid, $catedesc, $shopname, $overrideProducts)
	{
		$pagename = 'category';
		$metaDescCdt = ' ';
		$metaTitleCdt = ' ';
		/**/
		if($overrideProducts === false)
		{
            $metaTitleCdt = ' AND  (meta_title = \'\' OR ISNULL(meta_title))';
            $metaDescCdt = ' AND  (meta_description = \'\' OR ISNULL(meta_description))';
        }
		/**/
		if($this->isValidate($category) && $this->IsHasInfo($this->_categorytitle, $category) && $this->IsHasInfo ($this->_categorytitle, $catedesc))
		{

			$typelower = Tools::strtolower($type);
			if($typelower == 'on' || $typelower == 'customer')
			{
				$sql = 'SELECT * FROM '._DB_PREFIX_.'category_lang where id_shop='.$shopid.' and id_lang='.$languageID;
				$items = Db::getInstance()->executeS($sql);
				if($typelower == 'on' || ($typelower == 'customer' && $this->isPageInTable($pagename)))
				{
					if(!$this->isPageInTable($pagename))
						$this->InsertPageTable($pagename, $shopid);
					/**/
					foreach($items as $item)
					{
						$categoryid = $item['id_category'];
						$categoryname = self::htlmDecode(self::replaceNewLine($item['name']));
						$categorydesc = self::htlmDecode(self::replaceNewLine($item['description']));

						$itemCatColumn = array(
							'categoryid' => $categoryid,
							'categoryname' => $categoryname,
							'categorydesc' => $categorydesc,
							'lanuageid' => $languageID,
							'shopid' => $shopid,
							'shopname' => $shopname
						);

						//$metaTitleCat = $this->TransStringCategory($category, 70, $itemCatColumn);
						//$metaDescriptionCat = $this->TransStringCategory($catedesc, 160, $itemCatColumn);

						$sqlUpdateMetaTitle = 'UPDATE '._DB_PREFIX_."category_lang SET meta_title= '".html_entity_decode(Tools::stripslashes( $this->cleanText( $this->TransStringCategory2($category, $languageID, $shopid, $categoryid, $categoryname, $categorydesc, $shopname))))." ' where id_shop=".$shopid.' and
						id_category='.$categoryid.' and id_lang='.$languageID.' '.$metaTitleCdt;

						//DeBug::logmessage($sqlUpdateMetaTitle);
						Db::getInstance()->execute($sqlUpdateMetaTitle);

						$sqlUpdateMetaDesc  = 'UPDATE '._DB_PREFIX_."category_lang SET meta_description= '".html_entity_decode(Tools::stripslashes($this->cleanText($this->TransStringCategory2($catedesc, $languageID, $shopid, $categoryid, $categoryname, $categorydesc, $shopname)))).
						" ' where id_shop=".$shopid.' and id_category='.$categoryid.' and id_lang='.$languageID.' '.$metaDescCdt;
                        //p($sqlUpdateMetaDesc);
						DeBug::logmessage($sqlUpdateMetaDesc);
						Db::getInstance()->execute($sqlUpdateMetaDesc);
					}
				}
			}
		}
	}
	/**
	 *
	 * @param type $cmstitle
	 * @param type $type
	 * @param type $languageID
	 * @param type $cmsdesc
	 * @param type $shopname
	 * @param type $shopid
	 */
	public function updateCMS($cmstitle, $type, $languageID, $cmsdesc, $shopname, $shopid)
	{
		$pagename = 'cms';
		if($this->isValidate($cmstitle) && $this->isValidate($cmsdesc)
		&& $this->IsHasInfo($this->_cmstitle, $cmsdesc)
		&& $this->IsHasInfo($this->_cmstitle, $cmstitle))
		{
			$typelower = Tools::strtolower($type);
			if($typelower == 'on' || $typelower == 'customer')
			{
				if($typelower == 'on' || ($typelower == 'customer' && $this->isPageInTable($pagename)))
				{
					{
						if(!$this->isPageInTable($pagename))
							$this->InsertPageTable($pagename, $shopid);
					}
					$sql = 'SELECT * FROM '._DB_PREFIX_.'cms_lang where id_lang='.$languageID;
					$itms = Db::getInstance()->executeS($sql);
					foreach ($itms as $item)
					{
						$idcms = $item['id_cms'];
						$cmsname = $item['meta_title'];
						$updatecms = 'UPDATE '._DB_PREFIX_.'cms_lang SET meta_title= " '.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStingCMS($cmsname, $shopname, $cmstitle)))).' " , meta_description= "'.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStingCMS($cmsname, $shopname, $cmsdesc)))).' " where id_cms='.$idcms.' and id_lang='.$languageID;
						/**/
						Db::getInstance()->execute($updatecms);
					}
				}
			}
		}
	}
	/**
	 *
	 * @param type $indextitle
	 * @param type $type
	 * @param type $languageID
	 * @param type $indexdesc
	 * @param type $shopname
	 * @param type $shopid
	 */
	public function updateIndex($indextitle, $type, $languageID, $indexdesc, $shopname, $shopid)
	{
		$pagename = 'index';
		if($this->isValidate($indexdesc) and $this->isValidate($indextitle) and $this->IsHasInfo($this->_indextitle, $indexdesc) and $this->IsHasInfo($this->_indextitle, $indextitle))
		{
			$typelower = Tools::strtolower( $type);
			if($typelower == 'on' || $typelower == 'customer')
			{
				if($typelower == 'on')
				{
					if(!$this->isPageInTable($pagename))
						$this->InsertPageTable($pagename, $shopid);
					/**/
					$sql = 'SELECT * FROM '._DB_PREFIX_.'meta_lang ml left join '._DB_PREFIX_.'meta m on m.id_meta=ml.id_meta where m.page="index" and id_shop='.$shopid.' and id_lang='.$languageID;
					$items = Db::getInstance()->executeS($sql);
					foreach($items as $item)
					{
						$sqlupdate = 'UPDATE '._DB_PREFIX_."meta_lang set title= '".html_entity_decode(Tools::stripslashes($this->cleanText($this->GetSringIndex($indextitle, $shopname)))).
						 "' , description= '".html_entity_decode(Tools::stripslashes($this->cleanText($this->GetSringIndex ($indexdesc, $shopname)))).
						"'  where id_meta=".$item['id_meta'].' and id_shop='.$shopid.' and id_lang='.$languageID;
						Db::getInstance()->execute($sqlupdate);
					}
				}
				if($typelower == 'customer')
				{
					if($this->isPageInTable($pagename))
					{
						$sql = 'SELECT * FROM '._DB_PREFIX_.'meta_lang ml left join '._DB_PREFIX_.'meta m on m.id_meta=ml.id_meta where m.page="index" and id_shop='.$shopid.' and id_lang='.$languageID;
						$items = Db::getInstance()->executeS($sql);
						foreach ($items as $item)
						{
							$sqlupdate = 'UPDATE '._DB_PREFIX_."meta_lang set title= '".$this->GetSringIndex($indextitle, $shopname)."'
							, description= '".$this->GetSringIndex($indexdesc, $shopname)."'  where id_meta=".$item['id_meta'].' and id_shop='.$shopid.' and id_lang='.$languageID;
							Db::getInstance()->execute($sqlupdate);
						}
					}
				}
			}
		}
	}

	/**
	 *
	 * @param type $contactustype
	 * @param type $contactustitle
	 * @param type $contactusdesc
	 * @param type $shopid
	 * @param type $shopname
	 * @param type $languageid
	 */
	public function updateContactus($contactustype, $contactustitle, $contactusdesc, $shopid, $shopname, $languageid)
	{
		$pagename = 'contact';
		if($this->isValidate($contactusdesc) and $this->isValidate($contactustitle)
		and $this->IsHasInfo($this->_contactustitle, $contactusdesc)
		and $this->IsHasInfo($this->_contactustitle, $contactustitle))
		{
			$typelower = Tools::strtolower($contactustype);
			if($typelower == 'on' || $typelower == 'customer')
			{
				if($typelower == 'on' || ($typelower == 'customer' and $this->isPageInTable ($pagename)))
				{
					{
						if(!$this->isPageInTable($pagename))
							$this->InsertPageTable($pagename, $shopid);
					}
					$sql = 'SELECT * FROM '._DB_PREFIX_.'meta_lang ml left join '._DB_PREFIX_.'meta m on m.id_meta=ml.id_meta where m.page="contact" and
					ml.id_shop='.$shopid.' and ml.id_lang='.$languageid;
					$items = Db::getInstance()->executeS($sql);
					foreach($items as $item)
					{
						$metaid = $item['id_meta'];
						$update = 'UPDATE '._DB_PREFIX_."meta_lang set title= '".html_entity_decode(Tools::stripslashes($this->cleanText ($this->GetStringContactus($contactustitle, $shopname))))."'  ,
						description= '".html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringContactus($contactusdesc, $shopname))))."' where id_meta=".$metaid.' and id_shop='.$shopid.' and id_lang='.$languageid;
						Db::getInstance()->execute($update);
					}
				}
			}
		}
	}

	/**
	 * @param unknown $supplytype
	 * @param unknown $supplymetatitle
	 * @param unknown $supplydesc
	 * @param unknown $shopid
	 * @param unknown $shopname
	 * @param unknown $languageid
	 */
	public function updateSupplier($supplytype, $supplymetatitle, $supplydesc, $shopid, $shopname, $languageid)
	{
		$pagename = 'supply';
		if($this->isValidate($supplymetatitle) and $this->isValidate($supplydesc) and $this->IsHasInfo($this->_suppliertitle, $supplydesc))
		{
			/**/
			$typelower = Tools::strtolower($supplytype);
			if($typelower == 'on' || ($typelower == 'customer' and $this->isPageInTable($pagename)))
			{
				if(!$this->isPageInTable($pagename))
					$this->InsertPageTable($pagename, $shopid);
				/**/
				$sql = 'SELECT * FROM '._DB_PREFIX_.'supplier sp left join '._DB_PREFIX_.'supplier_lang spl on sp.id_supplier=spl.id_supplier where spl.id_lang='.$languageid;
				$items = Db::getInstance()->executeS($sql);
				foreach($items as $item)
				{
					$supplyid = $item['id_supplier'];
					$supplyname = $item['name'];
					$update = 'UPDATE '._DB_PREFIX_.'supplier_lang SET meta_title= "'.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringSupply($supplyname, $shopname, $supplymetatitle)))).'" ,
					meta_description="'.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringSupply($supplyname, $shopname, $supplydesc)))).'" where id_supplier='.$supplyid.' and id_lang='.$languageid;
					/**/
					Db::getInstance()->execute($update);
				}
			}
		}
	}

	/**
	 *
	 * @param type $manufacturetitle
	 * @param type $type
	 * @param type $manufacturedesc
	 * @param type $languageID
	 * @param type $shopname
	 * @param type $shopid
	 */
	public function updateManufacture($manufacturetitle, $type, $manufacturedesc, $languageID, $shopname, $shopid)
	{
		$pagename = 'manufacturer';
		if($this->isValidate($manufacturedesc) and $this->isValidate($manufacturetitle) and $this->IsHasInfo($this->_manufacturetitle, $manufacturedesc) and $this->IsHasInfo($this->_manufacturetitle, $manufacturetitle))
		{
			$typelower = Tools::strtolower($type);
			if($typelower == 'on' || ($typelower == 'customer' and $this->isPageInTable($pagename)))
			{
				if($typelower == 'on')
					if(!$this->isPageInTable($pagename))
						$this->InsertPageTable($pagename, $shopid);
				/**/
				$sql = 'SELECT * FROM '._DB_PREFIX_.'manufacturer mf left join '._DB_PREFIX_.'manufacturer_lang mfl on mf.id_manufacturer=mfl.id_manufacturer where id_lang='.$languageID;
				$items = Db::getInstance()->executeS($sql);
				foreach($items as $item)
				{
					$idmanfac = $item['id_manufacturer'];
					$manfacname = $item['name'];
					$sqlupdate = 'UPDATE '._DB_PREFIX_.'manufacturer_lang set meta_title="' .html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringManufacture($manufacturetitle, $manfacname, $shopname)))).'"
					, meta_description= "'.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringManufacture($manufacturedesc, $manfacname, $shopname)))).'"  where id_manufacturer='.$idmanfac.' and id_lang='.$languageID;
					Db::getInstance()->execute($sqlupdate);
				}
			}
		}
	}

	/**
	 *
	 * @param type $type
	 * @param type $searchmetatitle
	 * @param type $searchmetadesc
	 * @param type $shopid
	 * @param type $shopname
	 * @param type $languageid
	 */
	public function updateSearch($type, $searchmetatitle, $searchmetadesc, $shopid, $shopname, $languageid)
	{
		$pagename = 'search';
		if($this->isValidate($searchmetadesc) and $this->isValidate($searchmetatitle) and $this->IsHasInfo($this->_searchtitle, $searchmetatitle) and $this->IsHasInfo($this->_searchtitle, $searchmetadesc))
		{
			/**/
			$typelower = Tools::strtolower($type);
			if($typelower == 'on' || ($typelower == 'customer' and $this->isPageInTable($pagename)))
			{
				if($typelower == 'on')
					if(!$this->isPageInTable($pagename))
						$this->InsertPageTable($pagename, $shopid);
				/**/
				$sql = 'SELECT * FROM '._DB_PREFIX_.'meta_lang ml left join '._DB_PREFIX_.'meta m on m.id_meta=ml.id_meta where m.page="search"
				and id_shop='.$shopid.' and id_lang='.$languageid;
				$items = Db::getInstance()->executeS($sql);
				foreach($items as $item)
				{
					$sqlupdate = 'UPDATE '._DB_PREFIX_.'meta_lang set title= "'.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringSearch($searchmetatitle, $shopname)))).' " , description= "'.html_entity_decode(Tools::stripslashes($this->cleanText($this->GetStringSearch($searchmetadesc, $shopname)))).'"  where id_meta='.$item['id_meta'].' and id_shop='.$shopid.' and id_lang='.$languageid;
					Db::getInstance()->execute($sqlupdate);
				}
			}
		}
	}
	
	/**
	 * 
	 * @param type $type
	 * @param type $producttitle
	 * @param type $productdesc
	 * @param type $shopid
	 * @param type $shopname
	 * @param type $languageid
	 */
	public function updateProduct($type, $producttitle, $productdesc, $shopid, $shopname, $languageid)
	{
		$pagename = 'product';
		if($this->isValidate($productdesc) and $this->isValidate($producttitle) and $this->IsHasInfo($this->_producttitle, $productdesc) and $this->IsHasInfo($this->_producttitle, $producttitle))
		{
			$typelower = Tools::strtolower( $type);
			if($typelower == 'on' || ($typelower == 'customer' and $this->isPageInTable($pagename)))
			{
				if($typelower == 'on')
					if(!$this->isPageInTable($pagename))
						$this->InsertPageTable($pagename, $shopid);
				$sql = 'SELECT pl.name as productname ,cl.id_category as categoryid,cl.name as 
				categoryname,pl.id_product as productid FROM '._DB_PREFIX_.'product_lang 
				pl left join '._DB_PREFIX_.'category_product cp on 
				pl.id_product=cp.id_product left join '._DB_PREFIX_.'category_lang cl on 
				cl.id_category=cp.id_category where pl.id_shop='.$shopid.' and cl.id_lang='.$languageid.' and  pl.id_lang='.$languageid;
				
				$items = Db::getInstance()->executeS($sql);
				foreach($items as $item)
				{
					$categoryname = $item['categoryname'];
					$categoryid = $item['categoryid'];
					$productname = $item['productname'];
					$productid = $item['productid'];
					$update = 'UPDATE '._DB_PREFIX_.'product_lang set meta_title="'.Tools::stripslashes($this->cleanText($this->GetStringProduct($categoryid, $shopid, $shopname, $languageid, $productname, $categoryname, $producttitle))).'" , meta_description="'.Tools::stripslashes($this->cleanText($this->GetStringProduct($categoryid, $shopid, $shopname, $languageid, $productname, $categoryname, $productdesc))).'" where 
					id_shop='.$shopid.' and id_lang='.$languageid.' and id_product='.$productid;
					Db::getInstance()->execute($update);
				}
			}
		}
	}
	
	/**
	 * 
	 * @param type $categoryid
	 * @param type $shopid
	 * @param type $shopname
	 * @param type $languageid
	 * @param type $productname
	 * @param type $categoryname
	 * @param type $producttitle
	 * @return type
	 */
	public function GetStringProduct($categoryid, $shopid, $shopname, $languageid, $productname, $categoryname, $producttitle)
	{
		$resultarray = $this->SplitString($producttitle);
		$string = $producttitle;
		for($i = 0; $i < count ($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
			elseif(Tools::strtoupper($resultarray[$i]) == 'PRODUCT_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $productname, $string);
			elseif(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $categoryname, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'CATEGORY_NAME_PARENT'))
				$string = str_replace( '{'.$resultarray[$i].'}', $this->GetCategoryParentName($categoryid, $shopid, $languageid), $string);
		}
		return $string;	
	}
	
	/**
	 * 
	 * @param type $keywords
	 * @param type $productname
	 * @param type $prodReference
	 * @param type $categoryname
	 * @param type $shopname
	 * @param type $supplierName
	 * @param type $manufacturerName
	 * @param type $categoryid
	 * @param type $shopid
	 * @param type $languageid
	 * @return type
	 */
	public function getStringProductKeywords($keywords, $productname, $prodReference, $categoryname, $shopname, $supplierName, $manufacturerName, $categoryid, $shopid, $languageid)
	{

		$resultarray = $this->SplitString($keywords);
		//DeBug::logmessage('keywords: '.$keywords);
		$actualMetaKeywords = $keywords;
		for($i = 0; $i < count ($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$actualMetaKeywords = str_replace('{'.$resultarray[$i].'}', $shopname, $actualMetaKeywords);
			elseif(Tools::strtoupper($resultarray[$i]) == 'PRODUCT_NAME')
				$actualMetaKeywords = str_replace('{'.$resultarray[$i].'}', $productname, $actualMetaKeywords);
			elseif(Tools::strtoupper($resultarray[$i]) == 'PRODUCT_REFERENCE')
				$actualMetaKeywords = str_replace('{'.$resultarray[$i].'}', $prodReference, $actualMetaKeywords);
			elseif(Tools::strtoupper($resultarray[$i]) == 'SUPPLIER_NAME')
				$actualMetaKeywords = str_replace('{'.$resultarray[$i].'}', $supplierName, $actualMetaKeywords);
			elseif(Tools::strtoupper($resultarray[$i]) == 'MANUFACTURER_NAME')
				$actualMetaKeywords = str_replace('{'.$resultarray[$i].'}', $manufacturerName, $actualMetaKeywords);
			elseif(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME')
				$actualMetaKeywords = str_replace('{'.$resultarray[$i].'}', $categoryname, $actualMetaKeywords);
			elseif(Tools::strtoupper($resultarray[$i] == 'CATEGORY_NAME_PARENT'))
				// $string = str_replace('{'.$resultarray[$i].'}', $this->GetCategoryParentName($categoryid, $shopid, $languageid), $actualMetaKeywords);
				str_replace('{'.$resultarray[$i].'}', $this->GetCategoryParentName($categoryid, $shopid, $languageid), $actualMetaKeywords);
		}
		DeBug::logmessage('actualMetaKeywords: '.$actualMetaKeywords);
		return strip_tags($actualMetaKeywords);
	}
	
	
	/**
	 * @param unknown $productInfo
	 * @param unknown $truncateLength
	 * @param unknown $parameterTags
	 */
	public function GetStringProduct5($productInfo, $truncateLength, $parameterTags = array()) {
		
		extract($parameterTags);
		
		$resultarray = $this->SplitString($productInfo);
		$string = $productInfo;
		for($i = 0; $i < count ($resultarray); $i ++) {
			if(strtoupper($resultarray[$i]) == 'SHOP_NAME') {
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
			}
			elseif(strtoupper($resultarray[$i]) == 'PRODUCT_NAME') {
				$string = str_replace('{'.$resultarray[$i] . '}',$productname, $string);
			}
			elseif(strtoupper($resultarray[$i]) == 'PRODUCT_DESC') {
				$string = str_replace('{'.$resultarray[$i]. '}', $productDesc, $string);
			}
			elseif(strtoupper($resultarray[$i]) == 'PRODUCT_DESC_SHORT') {
				$string = str_replace('{'.$resultarray[$i]. '}', $productDescShort, $string);
			}
			elseif(strtoupper($resultarray[$i]) == 'CATEGORY_NAME') {
				$string = str_replace('{'.$resultarray[$i]. '}', $categoryname, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'CATEGORY_NAME_PARENT')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $this->GetCategoryParentName($categoryid, $shopid, $languageid), $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_REF')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $productRef, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_ID')) {// TODO
				$string = str_replace('{'.$resultarray[$i]. '}', $productId, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_SUPPLIER_REF')) {// TODO
				$string = str_replace('{'.$resultarray[$i]. '}', $productRefSupplier, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_PRICE')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $productPrice, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_PRICE_TAX')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $productPriceTax, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_CONDITION')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $productCondition, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_EAN13')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $productEan13, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'PRODUCT_UPC')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $productUpc, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'MANUFACTURER_NAME')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $manufacturerName, $string);
			}
			elseif(strtoupper($resultarray[$i] == 'SUPPLIER_NAME')) {
				$string = str_replace('{'.$resultarray[$i]. '}', $supplierName, $string);
			}
		}
		
		$myOptions = array(
				'ellipsis' => '...',
				'exact' => false,
				'html' => false
		);
		return strip_tags(self::truncate($string, $truncateLength, $myOptions));
	
	}	
	
	/**
	 * 
	 * @param type $categoryid
	 * @param type $shopid
	 * @param type $shopname
	 * @param type $languageid
	 * @param type $productname
	 * @param type $categoryname
	 * @param type $productDesc
	 * @param type $productDescShort
	 * @param type $productRef
	 * @param type $productId
	 * @param type $productRefSupplier
	 * @param type $productPrice
	 * @param type $productCondition
	 * @param type $productEan13
	 * @param type $productUpc
	 * @param type $supplierName
	 * @param type $manufacturerName
	 * @param type $producttitle
	 * @return type
	 */
	public function GetStringProduct4($categoryid, $shopid, $shopname, $languageid, $productname, $categoryname, $productDesc, $productDescShort, $productRef, $productId, $productRefSupplier, $productPrice, $productCondition, $productEan13, $productUpc, $supplierName, $manufacturerName, $producttitle)
	{
		$resultarray = $this->SplitString($producttitle);
		$string = $producttitle;
		for($i = 0; $i < count ($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
			elseif(Tools::strtoupper($resultarray[$i]) == 'PRODUCT_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $productname, $string);
			elseif(Tools::strtoupper($resultarray[$i]) == 'PRODUCT_DESC')
				$string = str_replace('{'.$resultarray[$i].'}', $productDesc, $string);
			elseif(Tools::strtoupper($resultarray[$i]) == 'PRODUCT_DESC_SHORT')
				$string = str_replace('{'.$resultarray[$i].'}', $productDescShort, $string);
			elseif(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $categoryname, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'CATEGORY_NAME_PARENT'))
				$string = str_replace('{'.$resultarray[$i].'}', $this->GetCategoryParentName($categoryid, $shopid, $languageid), $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_REF'))
				$string = str_replace('{'.$resultarray[$i].'}', $productRef, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_ID'))
				$string = str_replace('{'.$resultarray[$i].'}', $productId, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_SUPPLIER_REF'))
				$string = str_replace('{'.$resultarray[$i].'}', $productRefSupplier, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_PRICE'))
				$string = str_replace('{'.$resultarray[$i].'}', $productPrice, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_CONDITION'))
				$string = str_replace('{'.$resultarray[$i].'}', $productCondition, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_EAN13'))
				$string = str_replace('{'.$resultarray[$i].'}', $productEan13, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'PRODUCT_UPC'))
				$string = str_replace('{'.$resultarray[$i].'}', $productUpc, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'MANUFACTURER_NAME'))
				$string = str_replace('{'.$resultarray[$i].'}', $manufacturerName, $string);
			elseif(Tools::strtoupper($resultarray[$i] == 'SUPPLIER_NAME'))
				$string = str_replace('{'.$resultarray[$i].'}', $supplierName, $string);
		}
		
		return strip_tags(Tools::truncate($string, 160));
	
	}
	
	 /**
	 * 
	 * @param type $categoryid
	 * @param type $shopid
	 * @param type $languageid
	 * @return type
	 */
	public function GetCategoryParentName($categoryid, $shopid, $languageid)
	{
		$parentname = '';
		$sql = 'SELECT * FROM '._DB_PREFIX_.'category_lang cl left join '._DB_PREFIX_.'category c on cl.id_category=c.id_category where c.id_category='.$categoryid.' and cl.id_shop='.$shopid.' and cl.id_lang='.$languageid;
		$result = Db::getInstance()->getRow($sql);
		$parentid = $result['id_parent'];
		if($parentid >= 0)
		{
			$sqlparent = 'SELECT * FROM '._DB_PREFIX_.'category_lang where id_shop='.$shopid.' and id_lang='.$languageid.' and id_category='.$parentid;
			$parentinfo = Db::getInstance()->getRow($sqlparent);
			if(!empty($parentinfo))
			{
				$parentname = $parentinfo['name'];
			}
		}
		return $parentname;
	}
	
	
	/**
	 * @param unknown $searchtitle
	 * @param unknown $shopname
	 * @return unknown
	 */
	public function GetStringSearch($searchtitle, $shopname)
	{
		$resultarray = $this->SplitString($searchtitle);
		$string = $searchtitle;
		for($i = 0; $i < count($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
		}
		return $string;
	}
	
	/**
	 * @param unknown $supplyname
	 * @param unknown $shopname
	 * @param unknown $supplytitle
	 * @return unknown
	 */
	public function GetStringSupply($supplyname, $shopname, $supplytitle)
	{
		$string = $supplytitle;
		$resultarray = $this->SplitString($supplytitle);
		for($i = 0; $i < count($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
			if(Tools::strtoupper($resultarray[$i]) == 'SUPPLIER_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $supplyname, $string);
		
		}
		return $string;
	}
	
	/**
	 * 
	 * @param type $contactustitle
	 * @param type $shopname
	 * @return type
	 */
	public function GetStringContactus($contactustitle, $shopname)
	{
		$resultarray = $this->SplitString($contactustitle);
		$string = $contactustitle;
		for($i = 0; $i < count($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
		}
		return $string;
	}
	
	/**
	 * 
	 * @param type $manufacturetitle
	 * @param type $manufacturename
	 * @param type $shopname
	 * @return type
	 */
	public function GetStringManufacture($manufacturetitle, $manufacturename, $shopname)
	{
		$resultarray = $this->SplitString($manufacturetitle);
		$string = $manufacturetitle;
		for($i = 0; $i < count($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
			if(Tools::strtoupper($resultarray[$i] == 'MANUFACTURER_NAME'))
				$string = str_replace('{'.$resultarray[$i].'}', $manufacturename, $string);
		}
		return $string;
	}
	
	/**
	 * 
	 * @param type $indextitle
	 * @param type $shopname
	 * @return type
	 */
	public function GetSringIndex($indextitle, $shopname)
	{
		$resultarray = $this->SplitString($indextitle);
		$string = $indextitle;
		if(Tools::strtoupper($resultarray[0]) == 'SHOP_NAME')
			$string = str_replace('{'.$resultarray[0].'}', $shopname, $string);
		return $string;
	}
	
	/**
	 * 
	 * @param type $cmsname
	 * @param type $shopname
	 * @param type $cmstitle
	 * @return type
	 */
	public function GetStingCMS($cmsname, $shopname, $cmstitle)
	{
		$resultarray = $this->SplitString($cmstitle);
		$string = $cmstitle;
		for($i = 0; $i < count($resultarray); $i ++)
		{
			//{CMS_NAME}', '{SHOP_NAME}
			if(Tools::strtoupper($resultarray[$i]) == 'CMS_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $cmsname, $string);
			if(Tools::strtoupper($resultarray[$i] == 'SHOP_NAME'))
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
		}
		return $string;
	}
	
	/**
	 * 
	 * @param type $targetarray
	 * @param type $source
	 * @return boolean
	 */
	public function IsHasInfo($targetarray, $source)
	{
		$s = $this->SplitString ($source);
		for($i = 0; $i < count($s); $i ++)
		{
			if(! in_array('{'.Tools::strtoupper($s[$i]).'}', $targetarray))
				return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * @param type $categoryid
	 * @param type $lanuageid
	 * @param type $shopid
	 * @param type $categoryname
	 * @return type
	 */
	public function GetParentCategory($categoryid, $lanuageid, $shopid, $categoryname)
	{
		$sql = 'SELEFCT name From '._DB_PREFIX_.'category_lang ctl left join '._DB_PREFIX_.'category ct on ctl.id_category=ct.id_category where ctl.id_shop ='.$shopid.' and id_lang ='.$lanuageid.' and name !='.$categoryname;
		$result = Db::getInstance()->getRow($sql);
		return $result;
	}
	
	/**
	 * 
	 * @param type $category
	 * @return type
	 */
	public function SplitString($category)
	{
		################################### content Brace string#################################################
		$strarray = explode('}', $category);
		$targetstr = null;
		$j = 0;
		if(count($strarray) > 0)
		{
			for($i = 0; $i < count($strarray); $i ++)
			{
				if(strpos($strarray[$i], '{') > - 1)
				{
					$temp = explode('{', $strarray[$i]);
					if(count($temp) > 1)
					{
						if(!empty($temp[1]))
						{
							$targetstr[$j] = $temp[1];
							$j = $j + 1;
						}
					}
					if(count($temp) == 1)
					{
						if(!empty($temp[0]))
						{
							$targetstr[$j] = $temp[0];
							$j = $j + 1;
						}
					}
				}
			}
		}
		return $targetstr;
	}
	
	/**
	 *
	 * @param type $categoryInfo
	 * @param type $truncateLength
	 * @param type $parameterTags
	 * @return type string
	 */
	public function TransStringCategory($categoryInfo, $truncateLength, $parameterTags = array())
	{
		extract($parameterTags);
		//$category, $lanuageid, $shopid, $categoryid, $categoryname, $categorydesc, $shopname
		$resultarray = $this->SplitString($categoryInfo);
		$string = $categoryInfo;
		for($i = 0; $i < count($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME_PARENT')
			{
				$name = $this->GetCategoryParentName($categoryid, $shopid, $lanuageid);
				//$this->GetParentCategory($categoryid, $lanuageid, $shopid, $categoryname);
				$string = str_replace('{'.$resultarray[$i].'}', $name, $string);
			}
			if(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $categoryname, $string);
			if(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_DESC')
				$string = str_replace('{'.$resultarray[$i].'}', $categorydesc, $string);
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
		}
		
		$myOptions = array(
				'ellipsis' => '...',
				'exact' => false,
				'html' => false
		);
		
		return strip_tags(self::truncate($string, $truncateLength, $myOptions));
	}
	
	/**
	 * 
	 * @param type $category
	 * @param type $lanuageid
	 * @param type $shopid
	 * @param type $categoryid
	 * @param type $categoryname
	 * @param type $categorydesc
	 * @param type $shopname
	 * @return type
	 */
	public function TransStringCategory2($category, $lanuageid, $shopid, $categoryid, $categoryname, $categorydesc, $shopname)
	{
		$resultarray = $this->SplitString($category);
		$string = $category;
		for($i = 0; $i < count($resultarray); $i ++)
		{
			if(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME_PARENT')
			{
				$name = $this->GetCategoryParentName($categoryid, $shopid, $lanuageid); 
				//$this->GetParentCategory($categoryid, $lanuageid, $shopid, $categoryname);
				$string = str_replace('{'.$resultarray[$i].'}', $name, $string);
			}
			if(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $categoryname, $string);
			if(Tools::strtoupper($resultarray[$i]) == 'CATEGORY_DESC')
				$string = str_replace('{'.$resultarray[$i].'}', $categorydesc, $string);
			if(Tools::strtoupper($resultarray[$i]) == 'SHOP_NAME')
				$string = str_replace('{'.$resultarray[$i].'}', $shopname, $string);
		}
		return strip_tags($string);
	}
	
	
	
	public function isValidate($expstr)
	{
//		$expstr = iconv("UTF-8", "UTF-8", $expstr);
		############################is match {}#######################################
		$expstr = str_split ($expstr);
		$temp = array();
		for($i = 0; $i < count($expstr); $i++)
		{
			$ch = $expstr[$i]; //TODO:Возможно проблемы с кодировкой из-за того что обращаются по номеру к символу в строке. Нужно переписать на нормальное разбитие строки.
			switch ($ch)
			{
				case '{' :
					array_push($temp, '{');
					break;
				case '}' :
					if(empty($temp) || array_pop($temp) != '{')
						return false;
			}
		}
		return empty($temp);// == true ? true : false;
	}
	
	/**
	 * 
	 * @param type $pagename
	 * @return boolean
	 */
	public function isPageInTable($pagename)
	{
		$isIN = true;
		$sql = 'SELECT * FROM '._DB_PREFIX_.'meta where page="'.$pagename.'"';
		$items = Db::getInstance()->executeS($sql);
		if(count($items) > 0)
			$isIN = true;
		else
			$isIN = false;
		return $isIN;
	}
	
	public function InsertPageTable($pagename, $shopid)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'meta (page) values ("'.$pagename.'")';
		Db::getInstance()->execute($sql);
		$sqlselec = 'SELECT * FROM '._DB_PREFIX_.'meta where page="'.$pagename.'"';
		$row = Db::getInstance()->getRow($sqlselec);
		for($i = 1; $i < 6; $i ++)
		{
			$insertsql = 'INSERT INTO '._DB_PREFIX_.'meta_lang(id_shop,id_lang,title,id_meta,url_rewrite) values ('.$shopid.','.$i.', "'.$row['page'].'" ,'.$row['id_meta'].' , "'.$row['page'].' ")';
			Db::getInstance()->execute($insertsql);
		}
	}
	
	/**
	 * 
	 * @param type $info
	 * @return type
	 */
	public static function replaceNewLine($info)
	{
		$patterns = array();
		$patterns[0] = '/<br>/';
		$patterns[1] = '/<br\/>/';
		$patterns[2] = '/<br \/>/';
		$patterns[3] = '/<li>/';
		$patterns[4] = '/<li\/>/';
		$patterns[5] = '/<li \/>/';
		$patterns[6] = '/<ul>/';
		$patterns[7] = '/<ul\/>/';
		$patterns[8] = '/<ul \/>/';
		$replacements = array();
		$replacements[0] = ' - ';
		$replacements[1] = ' - ';
		$replacements[2] = ' - ';
		$replacements[3] = ' - ';
		$replacements[4] = ' - ';
		$replacements[5] = ' - ';
		$replacements[6] = ' - ';
		$replacements[7] = ' - ';
		$replacements[8] = ' - ';
		return preg_replace($patterns, $replacements, $info);
	}
	
	public static function htlmDecode($htmlEntity)
	{
		$htmlentities = array(
			'À'=>'&Agrave;', 
			'à'=>'&agrave;', 
			'Á'=>'&Aacute;', 
			'á'=>'&aacute;', 
			'Â'=>'&Acirc;', 
			'â'=>'&acirc;', 
			'Ã'=>'&Atilde;', 
			'ã'=>'&atilde;', 
			'Ä'=>'&Auml;', 
			'ä'=>'&auml;', 
			'Å'=>'&Aring;', 
			'å'=>'&aring;', 
			'Æ'=>'&AElig;', 
			'æ'=>'&aelig;', 
			'Ç'=>'&Ccedil;', 
			'ç'=>'&ccedil;', 
			'Ð'=>'&ETH;', 
			'ð'=>'&eth;', 
			'È'=>'&Egrave;', 
			'è'=>'&egrave;', 
			'É'=>'&Eacute;', 
			'é'=>'&eacute;', 
			'Ê'=>'&Ecirc;', 
			'ê'=>'&ecirc;', 
			'Ë'=>'&Euml;', 
			'ë'=>'&euml;', 
			'Ì'=>'&Igrave;', 
			'ì'=>'&igrave;', 
			'Í'=>'&Iacute;', 
			'í'=>'&iacute;', 
			'Î'=>'&Icirc;', 
			'î'=>'&icirc;', 
			'Ï'=>'&Iuml;', 
			'ï'=>'&iuml;', 
			'Ñ'=>'&Ntilde;', 
			'ñ'=>'&ntilde;', 
			'Ò'=>'&Ograve;', 
			'ò'=>'&ograve;', 
			'Ó'=>'&Oacute;', 
			'ó'=>'&oacute;', 
			'Ô'=>'&Ocirc;', 
			'ô'=>'&ocirc;', 
			'Õ'=>'&Otilde;', 
			'õ'=>'&otilde;', 
			'Ö'=>'&Ouml;', 
			'ö'=>'&ouml;', 
			'Ø'=>'&Oslash;',
			'ø'=>'&oslash;', 
			'Œ'=>'&OElig;', 
			'œ'=>'&oelig;', 
			'ß'=>'&szlig;', 
			'Þ'=>'&THORN;', 
			'þ'=>'&thorn;', 
			'Ù'=>'&Ugrave;', 
			'ù'=>'&ugrave;', 
			'Ú'=>'&Uacute;', 
			'ú'=>'&uacute;', 
			'Û'=>'&Ucirc;', 
			'û'=>'&ucirc;', 
			'Ü'=>'&Uuml;', 
			'ü'=>'&uuml;', 
			'Ý'=>'&Yacute;',
			'ý'=>'&yacute;', 
			'Ÿ'=>'&Yuml;', 
			'ÿ'=>'&yuml;'
		);
		
		return str_replace(array_values($htmlentities), array_keys($htmlentities), $htmlEntity);
	}
	
	public static function truncate($text, $length = 100, $options = array()) {
		$defaults = array(
			'ellipsis' => '...', 'exact' => true, 'html' => false
		);
		
		$options = array_merge($defaults, $options);
		extract($options);

		if (!function_exists('mb_strlen')) {
			class_exists('Multibyte');
		}

		if ($html) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen(strip_tags($ellipsis));
			$openTags = array();
			$truncate = '';

			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];

				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}

					$truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			}
			$truncate = mb_substr($text, 0, $length - mb_strlen($ellipsis));
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if ($html) {
				$truncateCheck = mb_substr($truncate, 0, $spacepos);
				$lastOpenTag = mb_strrpos($truncateCheck, '<');
				$lastCloseTag = mb_strrpos($truncateCheck, '>');
				if ($lastOpenTag > $lastCloseTag) {
					preg_match_all('/<[\w]+[^>]*>/s', $truncate, $lastTagMatches);
					$lastTag = array_pop($lastTagMatches[0]);
					$spacepos = mb_strrpos($truncate, $lastTag) + mb_strlen($lastTag);
				}
				$bits = mb_substr($truncate, $spacepos);
				preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
				if (!empty($droppedTags)) {
					if (!empty($openTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					} else {
						foreach ($droppedTags as $closingTag) {
							$openTags[] = $closingTag[1];
						}
					}
				}
			}
			$truncate = mb_substr($truncate, 0, $spacepos);
		}
		$truncate .= $ellipsis;

		if ($html) {
			foreach ($openTags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}
	public function cleanText($string){
		$string = addslashes($string);
		$string = htmlspecialchars($string);
		return $string;
	}
}