<?php


class PWExpressOrderClass
{
    var $conf;
    var $clientData;

    const ID_DEFAULT_ZONE = 7;

    public static $address_fields = Array(
        'lastname',
        'firstname',
        'address1',
        'address2',
        'city',
        'other',
        'phone',
        'phone_mobile'
    );

    public function getLastAddress($id_customer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'address`
		WHERE `id_customer` = ' . (int)($id_customer) . '
		AND `deleted` = 0 ORDER BY `id_address` DESC');
    }



    public static function getSimilarAddress(Address $address, $id_customer){
        $sql = 'SELECT id_address FROM `'._DB_PREFIX_.'address` WHERE 1';
        foreach(self::$address_fields as $field){
            $sql.=' AND '.$field.' LIKE "'.$address->$field.'" ';
        }
        $sql.=' AND id_customer = '.(int)$id_customer.' ORDER BY id_address DESC';
        $result = Db::getInstance()->getValue($sql);
        return $result;
    }

    public function prepareData()
    {
        global $cookie, $smarty, $cart;

        $conf = unserialize(Configuration::get('EO_CONFIG'));
        $smarty->assign('conf', $conf);
        $errors = Array();

        $back = Tools::getValue('back');
        if (!empty($back)) $smarty->assign('back', Tools::safeOutput($back));

        if (isset($conf['eo_carriers'])) {
            $carriers = Carrier::getCarriers(intval($cookie->id_lang), true, false, self::ID_DEFAULT_ZONE); //Получаем перевозчиков доступных для перевозки

            if (version_compare(_PS_VERSION_, '1.5', '>='))
                foreach ($carriers as &$carrier) {
                    $carrier['price'] = (int)Context::getContext()->cart->getPackageShippingCost($carrier['id_carrier']);
                    $carrier['logo'] = file_exists(_PS_IMG_DIR_.'s/'.$carrier['id_carrier'].'.jpg') ? _PS_IMG_.'s/'.$carrier['id_carrier'].'.jpg' : null;
                }
            if($cart->id_carrier) $smarty->assign('id_carrier', $cart->id_carrier);
            $smarty->assign('carriers', $carriers);
        }

        //$smarty->assign('days', $this->getDays());
        if ($cookie->id_customer) {
            $customer = new Customer(intval($cookie->id_customer));
            $address = $this->getLastAddress($customer->id);
            if ($customer) {
                $smarty->assign('address', $address);
                $smarty->assign('customer', $customer);
            }
        }
        /* Определение ошибок корзины */
        $orderTotal = $cart->getOrderTotal();
        $orderTotalDefaultCurrency = Tools::convertPrice($cart->getOrderTotal(true, 1), Currency::getCurrency(intval(Configuration::get('PS_CURRENCY_DEFAULT'))));
        $minimalPurchase = floatval(Configuration::get('PS_PURCHASE_MINIMUM'));
        $hideForm = false;
        if ($orderTotalDefaultCurrency < $minimalPurchase) {
            $errors[] = Tools::displayError('Общая сумма заказа должна быть не менее ').' '.Tools::displayPrice($minimalPurchase, Currency::getCurrency(intval($cart->id_currency)));
            $hideForm = true;
        } elseif ($orderTotal == 0) {
            $errors[] = Tools::displayError('Ваша корзина пуста');
            $hideForm = true;
        }
        $smarty->assign('hideForm', $hideForm);
        $pwexpressorderModule = new PWExpressOrder();
        $pwexpressorder_uri = $pwexpressorderModule->getFormUrl();
        $smarty->assign(
            Array(
                'pwexpressorder_uri' => $pwexpressorder_uri
            )
        );
        $this->conf = $conf;
        return $this->conf;
    }

    public function validate(){
        global $smarty;
        $errors = Array();

        if(version_compare(_PS_VERSION_, '1.5', '>=')){
            $context = Context::getContext();
            $smarty = $context->smarty;
        }
        if (@$this->conf['eo_email_show'] && isset($this->conf['eo_email_required']) && !Validate::isEmail($_POST['email'])) {
            $errors[] = Tools::displayError('Укажите E-mail');
        } else {
            if ($_POST['email']) {
                $this->clientData['email'] = $_POST['email'];
                $smarty->assign('email_type_by_user', 1);
            } else {
                $this->clientData['email'] = 'guest' . uniqid() . '@' . 'prestaweb.ru';
            }
        }

        if (@$this->conf['eo_company_show'] && $this->conf['eo_company_required'] && empty($_POST['company'])) {
            $errors[] = Tools::displayError('Укажите компанию');
        }

        if (@$this->conf['eo_fname_show'] && isset($this->conf['eo_fname_required']) && empty($_POST['firstname'])) {
            $errors[] = Tools::displayError('Укажите свое имя.');
        } else {
            $this->clientData['firstname'] = (@$_POST['firstname'] ? @$_POST['firstname'] : 'Клиент ');
        }

        if (@$this->conf['eo_lname_show'] && $this->conf['eo_lname_required'] && empty($_POST['lastname'])) {
            $errors[] = Tools::displayError('Укажите свою фамилию.');
        } else {
            $this->clientData['lastname'] = (isset($_POST['lastname']) && $_POST['lastname'] ? $_POST['lastname'] : ' ');
        }

        /*if(!$_POST['lastname2']){
            $errors[] = Tools::displayError('Укажите своё отчество.');
        }else{
            $addrLastName2 = mysql_escape_string(strip_tags($_POST['lastname2']));
        }*/

        if (@$this->conf['eo_password_show'] && $this->conf['eo_password_required'] && empty($_POST['password'])) {
            $errors[] = Tools::displayError('Укажите пароль.');
        } else {
            $this->clientData['passwd'] = (!empty($_POST['password']) ? $_POST['password'] : substr(uniqid(rand() . true), 0, 6)); //создаем случайный пароль
        }

        $_POST['passwd'] = $this->clientData['passwd'];
        $_POST['confirm_passwd'] = $this->clientData['passwd'];

        if (@$this->conf['eo_country_show'] && @$this->conf['eo_country_required'] && (!$_POST['id_country'] || !is_numeric($_POST['id_country']))) {
            $errors[] = Tools::displayError('Выберите страну.');
        } else {
            $_POST['id_country'] = (!empty($_POST['id_country']) ? intval($_POST['id_country']) : Configuration::get('PS_COUNTRY_DEFAULT'));
        }
        if (@$this->conf['eo_state_show'] && $this->conf['eo_state_required'] && (!$_POST['id_state'] || !is_numeric($_POST['id_state']))) {
            $errors[] = Tools::displayError('Выберите регион.');
        } else {
            $_POST['id_state'] = (!empty($_POST['id_state']) ? intval($_POST['id_state']) : 0);
        }
        if (@$this->conf['eo_address_show'] && @$this->conf['eo_address_required'] && !$_POST['address1']) {
            $errors[] = Tools::displayError('Укажите адрес.');
        } else {
            $_POST['address1'] = (!empty($_POST['address1']) ? $_POST['address1'] : 'Адрес не указан');
        }
        if (@$this->conf['eo_address2_show'] && $this->conf['eo_address2_required'] && !$_POST['address2']) {
            $errors[] = Tools::displayError('Укажите доп. адрес.');
        }
        if (@$this->conf['eo_city_show'] && $this->conf['eo_city_required'] && !$_POST['city']) {
            $errors[] = Tools::displayError('Укажите город');
        } else {
            $_POST['city'] = (!empty($_POST['city']) ? $_POST['city'] : 'Город не указан');
        }
        if (@$this->conf['eo_zip_show'] && $this->conf['eo_zip_required'] && !$_POST['zip']) {
            $errors[] = Tools::displayError('Укажите почтовый индекс');
        }
        if (isset($_POST['zip']) && strlen($_POST['zip']) > 1 && !is_numeric($_POST['zip'])) {
            $errors[] = Tools::displayError('Почтовый индекс указан не верно, он должен состоять только из цифр');
        }
        if (@$this->conf['eo_other_show'] && isset($this->conf['eo_other_required']) && !$_POST['other']) {
            $errors[] = Tools::displayError('Укажите дополнительную информацию');
        }
        if (@$this->conf['eo_phone_required'] && strlen($_POST['phone']) < 3) {
            $errors[] = Tools::displayError('Укажите телефон');
        }
        if (@$this->conf['eo_mobilephone_show'] && $this->conf['eo_mobilephone_required'] && !$_POST['phone_mobile']) {
            $errors[] = Tools::displayError('Укажите мобильный телефон');
        }
        return $errors;
    }

    /**
     * @return bool
     */
    public function cleanCartAddress()
    {
        $context = Context::getContext();
        return Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'cart_product` SET id_address_delivery = 0 WHERE id_cart =  ' . $context->cart->id);
    }

}