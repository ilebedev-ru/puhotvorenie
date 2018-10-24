<?php



/*

	changelog:

	01.05 - УБрал сохранение пользователя до проверки полей  и починил баг с не отображением формы в корзине

	27.05 - Сделал присвоение страны по умолчанию + добавление объекта страны в контекст

    19.06 - сделал общую форму для хука и для отдельной страницы. Небольшой рефакторинг кода. Корректировка дизайна.

    21.06 - Добавлено мультиверсионность

    21.07 - поправлены баги для версии 1.4 при покупке в 1 клик

*/



/* Для установки купить в 1 клик нужно установить хуки Footer и productAction */

require_once _PS_MODULE_DIR_.'pwexpressorder/classes/PWExpressOrderClass.php';

class PWExpressOrder extends PaymentModule

{



    public function __construct()

    {

        $this->name = 'pwexpressorder';

        $this->tab = 'Admin';

        $this->version = '1.0';



        parent::__construct();



        $this->page = basename(__FILE__, '.php');

        $this->displayName = $this->l('Простое и быстрое оформление заказа');

        $this->description = $this->l('Оформление быстрого заказа');

        $this->author = 'PrestaWeb.ru';



        $this->PWExpressOrderClass = new PWExpressOrderClass();

        $this->controllers =  Array('display');

    }



    public function install()

    {

        if (!parent::install() OR !$this->registerHook('paymentReturn') OR !$this->registerHook('header'))

            return false;

        if (!$this->registerHook('shoppingCart') || Configuration::updateValue('eo_displayEmail', 0) == false ||

            Configuration::updateValue('eo_displayAgreement', 0) == false

        ) return false;



        /* Устанавливаем дефолтные значения(какие поля показывать) */

        $conf = Array();

        $conf['eo_fname_show'] = 1;

        $conf['eo_email_show'] = 1;

        $conf['eo_phone_show'] = 1;

        $conf['eo_phone_required'] = 1;

        $conf['eo_other_show'] = 1;

        $conf['eo_payment_return'] = "Ваш заказ оформлен. В течение рабочего дня наш менеджер свяжется с Вами. Спасибо!";

        $conf['eo_other_show'] = 1;

        $conf['eo_address_required'] = 1;

        Configuration::updateValue('EO_CONFIG', serialize($conf));



        if(version_compare(_PS_VERSION_, '1.5', '>=')){

            $this->setZone();

            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'required_field` WHERE 1 '); //Удаляем дополнительные обязательные поля, чтобы не было конфликтов

        }

        return true;

    }



    public function setZone(){

        $carriers = Carrier::getCarriers(intval($this->context->cookie->id_lang), true);
        foreach($carriers as $carrier){

            if(!Carrier::checkCarrierZone($carrier['id_carrier'], PWExpressOrderClass::ID_DEFAULT_ZONE)){

                Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'carrier_zone` (id_carrier, id_zone) VALUES ('.$carrier['id_carrier'].', '.PWExpressOrderClass::ID_DEFAULT_ZONE.')');

                $delivery = new Delivery();

                $delivery->id_zone = PWExpressOrderClass::ID_DEFAULT_ZONE;

                $delivery->id_carrier = $carrier['id_carrier'];

                $delivery->id_range_price = $delivery->id_range_weight = 1;

                $delivery->price = 0;

                $delivery->id_shop=1;

                $delivery->id_shop_group = 1;

                $delivery->save();

            }

        }

    }



    public function uninstall()

    {

        return parent::uninstall();

    }





    public function getContent()

    {

        $output = '<h2>' . $this->displayName . '</h2>';

        if (Tools::isSubmit('submitBlockExpressOrder')) {

            $conf = array();

            foreach ($_POST as $k => $v) {

                if (substr($k, 0, 3) == 'eo_') {

                    if ($k == 'eo_payment_return') $conf[$k] = strip_tags($v);

                    else $conf[$k] = intval($v);

                }

            }

            Configuration::updateValue('EO_CONFIG', serialize($conf));

            $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('Подтверждение') . '" />' . $this->l('Настройки сохранены') . '</div>';

        }

        return $output . $this->displayForm();

    }



    public function displayForm()

    {

        $conf = unserialize(Configuration::get('EO_CONFIG'));

        $output = '

		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">

		<fieldset><legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />Настройки модуля</legend>

		<table>

		   <tr>

			  <td>Выбор способа доставки</td>

			  <td><input type="checkbox" name="eo_carriers" value="1" ' . (!empty($conf['eo_carriers']) ? 'checked="checked"' : '') . '/></td>

			</tr>

			<tr>

			  <td>Выбор оплаты</td>

			  <td><input type="checkbox" name="eo_payments" value="1" ' . (!empty($conf['eo_payments']) ? 'checked="checked"' : '') . '/></td>

			</tr>

			<tr>

			  <td>Текст после оформления заказа</td>

			  <td><textarea cols="70" rows="5" name="eo_payment_return">' . $conf['eo_payment_return'] . '</textarea><br /><i>Работает только без выбора оплат</i></td>

			</tr>

		</table>

		</fieldset>

			<fieldset><legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />Настройки обязательных полей</legend>

				<table>

				   <tr>

				   	  <th>' . $this->l('Наименование поля') . '</th>

				   	  <th>' . $this->l('Показывать') . '</th>

				   	  <th>' . $this->l('Обязательно') . '</th>

				   </tr>

				   <tr>

				      <td>' . $this->l('Company') . '</td>

				      <td><input type="checkbox" name="eo_company_show" value="1" ' . (!empty($conf['eo_company_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_company_required" value="1" ' . (!empty($conf['eo_company_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('First Name') . '</td>

				      <td><input type="checkbox" name="eo_fname_show" value="1" ' . (!empty($conf['eo_fname_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_fname_required" value="1" ' . (!empty($conf['eo_fname_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Last Name') . '</td>

				      <td><input type="checkbox" name="eo_lname_show" value="1" ' . (!empty($conf['eo_lname_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_lname_required" value="1" ' . (!empty($conf['eo_lname_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Password') . '</td>

				      <td><input type="checkbox" name="eo_password_show" value="1" ' . (!empty($conf['eo_password_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_password_required" value="1" ' . (!empty($conf['eo_password_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Email') . '</td>

				      <td><input type="checkbox" name="eo_email_show" value="1" ' . (!empty($conf['eo_email_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_email_required" value="1" ' . (!empty($conf['eo_email_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Address') . '</td>

				      <td><input type="checkbox" name="eo_address_show" value="1" ' . (!empty($conf['eo_address_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_address_required" value="1" ' . (!empty($conf['eo_address_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Address (2)') . '</td>

				      <td><input type="checkbox" name="eo_address2_show" value="1" ' . (!empty($conf['eo_address2_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_address2_required" value="1" ' . (!empty($conf['eo_address2_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('ZIP') . '</td>

				      <td><input type="checkbox" name="eo_zip_show" value="1" ' . (!empty($conf['eo_zip_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_zip_required" value="1" ' . (!empty($conf['eo_zip_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('City') . '</td>

				      <td><input type="checkbox" name="eo_city_show" value="1" ' . (!empty($conf['eo_city_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_city_required" value="1" ' . (!empty($conf['eo_city_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Country') . '</td>

				      <td><input type="checkbox" name="eo_country_show" value="1" ' . (!empty($conf['eo_country_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_country_required" value="1" ' . (!empty($conf['eo_country_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('State') . '</td>

				      <td><input type="checkbox" name="eo_state_show" value="1" ' . (!empty($conf['eo_state_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_state_required" value="1" ' . (!empty($conf['eo_state_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Additional information') . '</td>

				      <td><input type="checkbox" name="eo_other_show" value="1" ' . (!empty($conf['eo_other_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_other_required" value="1" ' . (!empty($conf['eo_other_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Home phone') . '</td>

				      <td><input type="checkbox" name="eo_phone_show" value="1" ' . (!empty($conf['eo_phone_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_phone_required" value="1" ' . (!empty($conf['eo_phone_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				   <tr>

				      <td>' . $this->l('Mobile phone') . '</td>

				      <td><input type="checkbox" name="eo_mobilephone_show" value="1" ' . (!empty($conf['eo_mobilephone_show']) ? 'checked="checked"' : '') . '/></td>

					  <td><input type="checkbox" name="eo_mobilephone_required" value="1" ' . (!empty($conf['eo_mobilephone_required']) ? 'checked="checked"' : '') . '/></td>

				   </tr>

				</table>

				<center><input type="submit" name="submitBlockExpressOrder" value="' . $this->l('Save') . '" class="button" /></center>

			</fieldset>

		</form>';

        return $output;

    }



    public function hookFooter($params)

    {

		global $smarty;

        $conf = unserialize(Configuration::get('EO_CONFIG'));

        $smarty->assign('conf', $conf);

		$smarty->assign(Array(

			'pwexpressorder_uri' => $this->getFormUrl()

		));

        return $this->display(__FILE__, 'footer.tpl');

    }



	public function getFormUrl(){

		 return (version_compare(_PS_VERSION_, '1.5', '>=') ? $this->context->link->getModuleLink('pwexpressorder', 'display') : __PS_BASE_URI__.'modules/pwexpressorder/make.php');

	}



    public function hookHeader($params)

    {

        if (version_compare(_PS_VERSION_, '1.5', '<')) {

            Tools::addCSS(($this->_path) . 'css/pwexpressorder.css');

            Tools::addJS(($this->_path) . 'js/pwexpressorder.js');

        } else {

            $this->context->controller->addCSS(($this->_path) . 'css/pwexpressorder.css', 'all');

            $this->context->controller->addJS(($this->_path) . 'js/pwexpressorder.js', 'all');

        }

    }



    public function hookProductActions($params)

    {

        return '<a class="exclusive buy_fast" rel="nofollow" href="#" title="Купить в 1 клик">Купить в 1 клик</a>';

    }



    function hookshoppingCart($params)

    {

        return $this->hookshoppingCartExtra($params);

    }



    public function hookshoppingCartExtra($params)

    {

        global $smarty;

        $smarty->assign('fast', true);

        if(version_compare(_PS_VERSION_, '1.5', '>=')) {

            $this->context->cart->id_address_delivery = $this->context->cart->id_address_invoice = 0;

            $this->context->cart->update();;

        }

        $this->PWExpressOrderClass->prepareData();

        return $this->display(__FILE__, 'form.tpl');

    }



    public function hookPayment($params)

    {

        return;

    }



    public function hookPaymentReturn($params)

    {

        if (!$this->active)

            return;

        $conf = unserialize(Configuration::get('EO_CONFIG'));

        if ($conf['eo_payment_return']) return '<div class="success alert alert-success">' . $conf['eo_payment_return'] . '</div>';

        return;

    }

}



?>