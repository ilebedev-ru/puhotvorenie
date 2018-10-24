<?php

/*
 * 2010-10-19
 * Written by Troitskiy Evgeniy
 * E-mail: etroitskiy@gmai.com
 */


class ExpressOrder extends Module
{
  public function __construct()
  {
    $this->name = 'expressorder';
    $this->tab = 'Admin';
    $this->version = '0.3.5';

    parent::__construct();
	
	if (!parent::isInstalled('cashondelivery')) {
        $this->warning = $this->l('Первоначально следует установить модуль оплаты при получении.');
    }

    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('Быстрый заказ');
    $this->description = $this->l('Оформление заказа в 1 шаг');
  }

  public function install()
  {
    if (!parent::install())
      return false;
    if (!$this->registerHook('shoppingCart') || Configuration::updateValue('eo_displayEmail', 0) == false || 
    	Configuration::updateValue('eo_displayAgreement', 0) == false)
      return false;
    return true;
  }

  public function uninstall()
  {
    return parent::uninstall();
  }
  
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockExpressOrder'))
		{
			$conf = array();
			foreach($_POST as $k => $v) {
				if (substr($k, 0, 3) == 'eo_') {
					$conf[$k] = intval($v);
				}
			}
			Configuration::updateValue('EO_CONFIG', serialize($conf));
			$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Настройки сохранены').'</div>';
		}
		return $output.$this->displayForm();
	}
	
    public function displayForm()	{
    	$conf = unserialize(Configuration::get('EO_CONFIG'));
		$output = '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />Настройки модуля</legend>
		<table>
		   <tr>
			  <th>'.$this->l('Title field').'</th>
			  <th></th>
			  <th></th>
		   </tr>
		   <tr>
			  <td>Выбор способа доставки</td>
			  <td><input type="checkbox" name="eo_carriers" value="1" '.($conf['eo_carriers']?'checked="checked"':'').'/></td>
			  <td></td>
			</tr>
			<tr>
			  <td>Проверка заполнения полей, без перезагрузки страницы</td>
			  <td><input type="checkbox" name="eo_jscheck" value="1" '.($conf['eo_jscheck']?'checked="checked"':'').'/></td>
			  <td></td>
			</tr>
			<tr>
			  <td>Онлайн оплата</td>
			  <td><input type="checkbox" name="eo_payment" value="1" '.($conf['eo_payment']?'checked="checked"':'').'/></td>
			  <td></td>
			</tr>
		</table>
		</fieldset>
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />Настройки обязательных полей</legend>
				<table>
				   <tr>
				   	  <th>'.$this->l('Title field').'</th>
				   	  <th>'.$this->l('Show').'</th>
				   	  <th>'.$this->l('Required').'</th>
				   </tr>
				   <tr>
				      <td>'.$this->l('Company').'</td>
				      <td><input type="checkbox" name="eo_company_show" value="1" '.($conf['eo_company_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_company_required" value="1" '.($conf['eo_company_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('First Name').'</td>
				      <td><input type="checkbox" name="eo_fname_show" value="1" '.($conf['eo_fname_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_fname_required" value="1" '.($conf['eo_fname_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Last Name').'</td>
				      <td><input type="checkbox" name="eo_lname_show" value="1" '.($conf['eo_lname_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_lname_required" value="1" '.($conf['eo_lname_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Password').'</td>
				      <td><input type="checkbox" name="eo_password_show" value="1" '.($conf['eo_password_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_password_required" value="1" '.($conf['eo_password_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Email').'</td>
				      <td><input type="checkbox" name="eo_email_show" value="1" '.($conf['eo_email_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_email_required" value="1" '.($conf['eo_email_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Address').'</td>
				      <td><input type="checkbox" name="eo_address_show" value="1" '.($conf['eo_address_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_address_required" value="1" '.($conf['eo_address_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Address (2)').'</td>
				      <td><input type="checkbox" name="eo_address2_show" value="1" '.($conf['eo_address2_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_address2_required" value="1" '.($conf['eo_address2_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('ZIP').'</td>
				      <td><input type="checkbox" name="eo_zip_show" value="1" '.($conf['eo_zip_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_zip_required" value="1" '.($conf['eo_zip_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('City').'</td>
				      <td><input type="checkbox" name="eo_city_show" value="1" '.($conf['eo_city_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_city_required" value="1" '.($conf['eo_city_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Country').'</td>
				      <td><input type="checkbox" name="eo_country_show" value="1" '.($conf['eo_country_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_country_required" value="1" '.($conf['eo_country_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('State').'</td>
				      <td><input type="checkbox" name="eo_state_show" value="1" '.($conf['eo_state_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_state_required" value="1" '.($conf['eo_state_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Additional information').'</td>
				      <td><input type="checkbox" name="eo_other_show" value="1" '.($conf['eo_other_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_other_required" value="1" '.($conf['eo_other_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Home phone').'</td>
				      <td><input type="checkbox" name="eo_phone_show" value="1" '.($conf['eo_phone_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_phone_required" value="1" '.($conf['eo_phone_required']?'checked="checked"':'').'/></td>
				   </tr>
				   <tr>
				      <td>'.$this->l('Mobile phone').'</td>
				      <td><input type="checkbox" name="eo_mobilephone_show" value="1" '.($conf['eo_mobilephone_show']?'checked="checked"':'').'/></td>
					  <td><input type="checkbox" name="eo_mobilephone_required" value="1" '.($conf['eo_mobilephone_required']?'checked="checked"':'').'/></td>
				   </tr>
				</table>
				<center><input type="submit" name="submitBlockExpressOrder" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	
  function getAddresses2($id_customer)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'address`
		WHERE `id_customer` = '.(int)($id_customer).'
		AND `deleted` = 0');
	}
	
	function parsePayments(){
		$return = Module::hookExecPayment();

        # fix Moneybookers relative path to images
        $return = preg_replace('/src="modules\//', 'src="' . __PS_BASE_URI__ . 'modules/', $return);
        
        # OPCKT fix Paypal relative path to redirect script
        $return = preg_replace('/href="modules\//', 'href="' . __PS_BASE_URI__ . 'modules/', $return);
		
		$content = $return;
            $payment_methods = array();
            $i = 0;
            // regular payment modules
            preg_match_all('/<a.*?>.*?<img.*?src="(.*?)".*?\/?>(.*?)<\/a>/ms', $content, $matches1, PREG_SET_ORDER);
            // moneybookers
            preg_match_all('/<input .*?type="image".*?src="(.*?)".*?>.*?<span.*?>(.*?)<\/span>/ms', $content, $matches2, PREG_SET_ORDER);
            $matches = array_merge($matches1, $matches2);
            foreach ($matches as $match) {
                $payment_methods[$i]['img'] = preg_replace('/(\r)?\n/m', " ", trim($match[1]));
                $payment_methods[$i]['desc'] = preg_replace('/\s/m', " ", trim($match[2])); // fixed for Auriga payment
                $payment_methods[$i]['link'] = "opc_pid_$i";

                $i++;
            }
	}
	
	function hookshoppingCart($params){
	  global $cookie, $smarty;
		$back = Tools::getValue('back');
		$conf = unserialize(Configuration::get('EO_CONFIG'));
		$smarty->assign('conf', $conf);
		if (!empty($back)) $smarty->assign('back', Tools::safeOutput($back));
		if($conf['eo_carriers']){
			$carriers = Carrier::getCarriers(intval($cookie->id_lang),true);
			$smarty->assign('carriers', $carriers);
		}
		if($cookie->id_customer){
			$customer = new Customer(intval($cookie->id_customer));
			$adresses = $this->getAddresses2(intval($cookie->id_customer));
			if(count($adresses)){
				$address = $adresses[count($adresses)-1];
			}
			if($customer){
				$smarty->assign('address',$address);
				$smarty->assign('customer', $customer);
			}
		}

		$gift['enable'] = Configuration::get('PS_GIFT_WRAPPING');
		if ($gift['enable'] == 1) {
			$gift['price'] = Configuration::get('PS_GIFT_WRAPPING_PRICE');
			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
			$gift['currency'] = $currency->sign;
		}

		$smarty->assign('fast', true);
		$smarty->assign('gift', $gift);
		return $this->display(__FILE__, 'makeorder.tpl');
	  }
  
  function hookshoppingCartExtra($params){
		return $this->hookshoppingCart($params);
	}
}
?>
