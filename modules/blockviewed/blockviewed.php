<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockViewed extends Module
{

	public function __construct()
	{
		$this->name = 'blockviewed';
		$this->tab = 'front_office_features';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Viewed products block');
		$this->description = $this->l('Adds a block displaying recently viewed products.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('header') && $this->registerHook('displayHome') && Configuration::updateValue('PRODUCTS_VIEWED_NBR', 2));

	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockViewed'))
		{
			if (!($productNbr = Tools::getValue('PRODUCTS_VIEWED_NBR')) || empty($productNbr))
				$output .= $this->displayError($this->l('You must fill in the \'Products displayed\' field.'));
			elseif ((int)($productNbr) == 0)
				$output .= $this->displayError($this->l('Invalid number.'));
			else
			{
				Configuration::updateValue('PRODUCTS_VIEWED_NBR', (int)$productNbr);
				$output .= $this->displayConfirmation($this->l('Settings updated.'));
			}
		}
		return $output.$this->renderForm();
	}

	public function hookDisplayHome($params)
	{
        $productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(array_reverse(explode(',', $params['cookie']->viewed)), 0, Configuration::get('PRODUCTS_VIEWED_NBR')) : array();
        if (count($productsViewed))
        {
            $defaultCover = Language::getIsoById($params['cookie']->id_lang);
            $default_lang = Language::getIdByIso($defaultCover);
            $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`,
			pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`,
			cl.`name` category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 new
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$default_lang.')
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$default_lang.')
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$default_lang.')
			WHERE p.id_product IN ('.implode(',', $productsViewed).') AND p.active=1
			GROUP BY p.`id_product`');
            if (!empty($products)) {
                $products = Product::getProductsProperties($default_lang, $products);
                $this->smarty->assign('products', $products);
                return $this->display(__FILE__, 'blockviewed.tpl');
            }
            return array();
        }
        return;
	}
	public function hookdisplayUnderHome($params)
	{
		return $this->hookDisplayHome($params);
	}
	public function hookHeader($params)
	{
		$id_product = (int)Tools::getValue('id_product');
		$productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(array_reverse(explode(',', $params['cookie']->viewed)), 0, Configuration::get('PRODUCTS_VIEWED_NBR')) : array();

		if ($id_product && !in_array($id_product, $productsViewed))
		{
			$product = new Product((int)$id_product);
			if ($product->checkAccess((int)$this->context->customer->id))
			{
				if (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed))
					$params['cookie']->viewed .= ','.(int)$id_product;
				else
					$params['cookie']->viewed = (int)$id_product;
			}
		}
		$this->context->controller->addCSS(($this->_path).'blockviewed.css', 'all');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Products to display'),
						'name' => 'PRODUCTS_VIEWED_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products displayed in this block.')
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockViewed';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'PRODUCTS_VIEWED_NBR' => Tools::getValue('PRODUCTS_VIEWED_NBR', Configuration::get('PRODUCTS_VIEWED_NBR')),
		);
	}
}
