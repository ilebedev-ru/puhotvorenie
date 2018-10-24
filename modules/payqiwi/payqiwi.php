<?php
if (!defined('_PS_VERSION_'))
	exit;

class payqiwi extends PaymentModule
{	
	public function __construct()
	{
		$this->name = 'payqiwi';
		$this->tab = 'payments_gateways';
		$this->version = '0.1';
		$this->author = 'Evil1';
		$this->need_instance = 1;
		
		$this->currencies = false;

		parent::__construct();

		$this->displayName = $this->l('Оплата Qiwi');
		$this->description = $this->l('Выдает реквизиты после оформления');

	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}
	public function getContent()
	{
		if (Tools::isSubmit('submit'))
		{
			Configuration::updateValue('PAYQIWI', Tools::getValue('PAYQIWI'));
		}
		
		return '
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Настройки модуля оплаты').'</legend>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Текст оформления заказа:').'</label>
				<div class="margin-form">
					<textarea name="PAYQIWI" cols="70" rows="10">'.Tools::getValue('PAYQIWI', Configuration::get('PAYQIWI')).'</textarea>
				</div>
				<div class="clear">&nbsp;</div>
				<center><input type="submit" name="submit" value="'.$this->l('Обновить настройки').'" class="button" /></center>
			</fieldset>
		</form>';	
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return ;

		global $smarty;


		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}
	
	public function hookPaymentReturn($params)
	{
		global $smarty, $cookie;
		if (!$this->active)
			return ;
		
		$smarty->assign('paycontent', Configuration::get('PAYQIWI'));
		return $this->display(__FILE__, 'confirmation.tpl');
	}
}
