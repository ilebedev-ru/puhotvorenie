<?php

class massupdatediscount extends Module
{
	/* @var boolean error */
	protected $error = false;
	public $product_limit = 500;
	
	
	function __construct()
	{
	 	$this->name = 'massupdatediscount';
	 	$this->tab = 'Products';
	 	$this->version = '1.0';

	 	parent::__construct();

	 	/* The parent construct is required for translations */
		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Массовое обновление скидок');
        $this->description = $this->l('');
		$this->confirmUninstall = $this->l('');
	}
	
	function install($try_again=true)
	{
	 	//if (!$this->addMassUpdateHook() || !parent::install() || !$this->registerHook('shippingCalculate') || !$this->registerHook('shippingCalculateDays')){
	 	if (!parent::install()){
 			return false;
	 	}
	 	
	 	return true;
	}
	
	function uninstall()
	{
	 	if (parent::uninstall() == false)
	 		return false;
	 	
	 		
	 	return true;
	}
	
	
	
	
	function updateProducts()
	{
		global $cookie;
		$product_settings = $_POST['mup'];
		
		$skidka = (int)$_POST['skidka'];
		$rewrite = ((int)$_POST['rewrite'] ? 1 : 0);
		
		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'product` WHERE active = 1');
		
		foreach($products as $product){
			$sql = 'INSERT INTO `'._DB_PREFIX_.'specific_price` (id_product, from_quantity, reduction_type, reduction)
					VALUES ('.$product['id_product'].', 1, "percentage", "'.($skidka/100).'")';
			$product_skidka = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE id_product = '.$product['id_product']);
			if($product_skidka){
				if($rewrite){
					Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_specific_price` = '.$product_skidka['id_specific_price']);
					Db::getInstance()->Execute($sql);
				}
			}else{
				Db::getInstance()->Execute($sql);
			}
		}
		
	 	return true;
	}
	
	
	
	function getContent()
    {
		global $cookie;
     	$this->_html = '<h2>'.$this->displayName.'</h2>';

     	/* Update the settings */
     	if (isset($_POST['mu_doupdate']))
     	{
     	 	if (!$this->updateProducts())
     	 		$this->_html .= $this->displayError($this->l('Произошла ошибка'));
     	 	else
     	 		$this->_html .= $this->displayConfirmation($this->l('Товары успешно обновлены!'));
     	}
		
    
	 	
     	ob_start();
     	
     	?>
		<div style="clear:both"></div>
		<form method="POST">
     	<input type="hidden" name="mu_doupdate" value="go">
		Переписать старые скидки? <input type="checkbox" value="1" name="rewrite" /><br />
     	<input type="text" name="skidka" value="">% 
		<input type="submit" class="button" name="go" value="Добавить скидку" onclick="this.value='Обновляется...'; this.disabled=true;" />
     	<?
     	
     	
     	$this->_html .= ob_get_clean();
     	
		     	$this->_html .= ' 
				
		     	
			</form>';
			
     	

        return $this->_html;
    }
	
	
}
?>
