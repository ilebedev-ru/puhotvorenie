<?php

if (!defined('_PS_VERSION_'))
	exit;

class HomeWeek extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'homeweek';
		$this->tab = 'front_office_features';
		$this->version = '0.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();
		
		$this->displayName = $this->l('Товары недели');
		$this->description = $this->l('');
		
		$this->defaultNumberProducts = 8;
	}

	function install()
	{
		return parent::install() && $this->registerHook('home');
	}
	
	function uninstall()
	{
		return Configuration::deleteByName('HOME_FEATURED_NBR') && parent::uninstall();
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitHomeFeatured'))
		{
			$nbr = (int)Tools::getValue('nbr');
			if ($nbr <= 0)
				$errors[] = $this->l('Invalid number of products');
			else
				Configuration::updateValue('HOME_FEATURED_NBR', (int)$nbr);
			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<p>'.$this->l('In order to add products to your homepage, just add them to the "home" category.').'</p><br />
				<label>'.$this->l('Number of products displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.Tools::safeOutput(Tools::getValue('nbr', (int)Configuration::get('HOME_FEATURED_NBR'))).'" />
					<p class="clear">'.sprintf($this->l('The number of products displayed on homepage (default: "%s").'), (int)$this->defaultNumberProducts).'</p>
					
				</div>
				<center><input type="submit" name="submitHomeFeatured" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	function hookHome($params)
	{
		global $smarty;

		$category = new Category(32, (int)Configuration::get('PS_LANG_DEFAULT'));
		$nb = (int)Configuration::get('HOME_FEATURED_NBR');

		$smarty->assign(array(
		'week_products' => $category->getProducts((int)$params['cookie']->id_lang, 1, 3),
		'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
		'weekSize' => Image::getSize('week')));

		return $this->display(__FILE__, 'homeweek.tpl');
	}
}
