<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
*
*  Module based on favoriteproducts v1.3.0
*
*/

class PWFavoriteProducts extends Module
{
	public function __construct()
	{
		$this->name = 'pwfavoriteproducts';
		$this->tab = 'front_office_features';
		$this->version = '1.4.3';
		$this->author = 'PrestaWeb.ru';
		$this->need_instance = 0;

		$this->controllers = array('account');

		parent::__construct();

		$this->displayName = $this->l('Favorite Products');
		$this->description = $this->l('Display a page featuring the customer\'s favorite products.');
		$this->ps_versions_compliancy = array('min' => '1.5.6.1', 'max' => _PS_VERSION_);
	}

	public function install()
	{
			if (!parent::install()
				|| !$this->registerHook('displayMyAccountBlock')
				|| !$this->registerHook('displayCustomerAccount')
				|| !$this->registerHook('displayLeftColumnProduct')
				|| !$this->registerHook('extraLeft')
				|| !$this->registerHook('displayHeader')
				|| !$this->registerHook('displayProductButtons')
				|| !$this->registerHook('actionAuthentication')
				|| !$this->registerHook('productActions')
				|| !$this->registerHook('displayProductListFunctionalButtons')
				|| !$this->registerHook('actionCustomerAccountAdd'))
					return false;

			if (!Db::getInstance()->execute('
				CREATE TABLE `'._DB_PREFIX_.'favorite_product` (
				`id_favorite_product` int(10) unsigned NOT NULL auto_increment,
				`id_product` int(10) unsigned NOT NULL,
				`id_customer` int(10) unsigned NOT NULL,
				`id_shop` int(10) unsigned NOT NULL,
				`date_add` datetime NOT NULL,
  				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_favorite_product`))
				ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
				return false;

			return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'favorite_product`'))
			return false;
		return true;
	}

	public function hookDisplayCustomerAccount($params)
	{
		$this->smarty->assign('in_footer', false);
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		$this->smarty->assign('in_footer', true);
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayLeftColumnProduct($params)
	{
		include_once(dirname(__FILE__).'/FavoriteProduct.php');
        $isFavorite = $this->isFavorite(Tools::getValue('id_product'), $this->context->customer);
		$this->smarty->assign(array(
			'isCustomerFavoriteProduct' => $isFavorite,
			'isLogged' => 1,
		));
		return $this->display(__FILE__, 'favoriteproducts-extra.tpl');
	}
    
    public function isFavorite($id_product, $customer = null)
    {
        if(!$customer){
            $customer = $this->context->customer;
        }
        if($customer->logged){
			$isFavorite = (FavoriteProduct::isCustomerFavoriteProduct($customer->id, Tools::getValue('id_product'))? 1 : 0);
		}else{
			$isFavorite = $this->isGuestFavoriteProduct(Tools::getValue('id_product'));
		}
        return $isFavorite;
    }

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'favoriteproducts.css', 'all');
		$this->context->controller->addJS($this->_path.'favoriteproducts.js');
		return $this->display(__FILE__, 'favoriteproducts-header.tpl');
	}

	
	private function isGuestFavoriteProduct($id_product)
	{
		$wishlist = $this->context->cookie->wishlist;
		if($wishlist){
			$wishlist = Tools::jsonDecode($this->context->cookie->wishlist, true);
			if(is_array($wishlist)){
				return($wishlist[$id_product]);
			}
		}
		return false;
	}

		
	public function hookdisplayProductButtons($params)
	{
		 return $this->hookDisplayLeftColumnProduct($params);
	}
	
	public function hookActionAuthentication($params)
	{
		include_once(dirname(__FILE__).'/FavoriteProduct.php');
		$wishlist = $this->context->cookie->wishlist;
		if($wishlist){
			$wishlist = Tools::jsonDecode($this->context->cookie->wishlist, true);
			if(is_array($wishlist)){
				foreach($wishlist as $id_product => $v){
					$product = new Product($id_product, true, $this->context->language->id);
					if (!Validate::isLoadedObject($product) || FavoriteProduct::isCustomerFavoriteProduct((int)$params['customer']->id, (int)$product->id))
						continue;
					$favorite_product = new FavoriteProduct();
					$favorite_product->id_product = $product->id;
					$favorite_product->id_customer = (int)$params['customer']->id;
					$favorite_product->id_shop = (int)Context::getContext()->shop->id;
					$favorite_product->add();
				}
				$this->context->cookie->wishlist = '';
				$this->context->cookie->write();
			}
		}
	}
	
	public function hookactionCustomerAccountAdd($params)
	{
		include_once(dirname(__FILE__).'/FavoriteProduct.php');
		$wishlist = $this->context->cookie->wishlist;
		if($wishlist){
			$wishlist = Tools::jsonDecode($this->context->cookie->wishlist, true);
			if(is_array($wishlist)){
				foreach($wishlist as $id_product => $v){
					$product = new Product($id_product, true, $this->context->language->id);
					if (!Validate::isLoadedObject($product) || FavoriteProduct::isCustomerFavoriteProduct((int)$params['newCustomer']->id, (int)$product->id))
						continue;
					$favorite_product = new FavoriteProduct();
					$favorite_product->id_product = $product->id;
					$favorite_product->id_customer = (int)$params['newCustomer']->id;
					$favorite_product->id_shop = (int)Context::getContext()->shop->id;
					$favorite_product->add();
				}
				$this->context->cookie->wishlist = '';
				$this->context->cookie->write();
			}
		}
	}
    
    public function hookDisplayProductListFunctionalButtons($params)
    {
        $isFavorite = $this->isFavorite($params['product']['id_product'], $this->context->customer);
        $this->smarty->assign(array(
            'isFavorite' => $isFavorite,
            'product' => $params['product']
        ));
		return $this->display(__FILE__, 'pwfavorite_button.tpl');
    }
	
	
}


