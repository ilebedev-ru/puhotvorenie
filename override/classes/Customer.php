<?php

class Customer extends CustomerCore
{

    /** @var string Middlename */
    public $middlename;

    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public $sn_service = array();

    public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            'lastname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'firstname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'middlename' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => false, 'size' => 32),
            'email' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 32),
            'last_passwd_gen' =>            array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_gender' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'birthday' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
            'newsletter' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'newsletter_date_add' =>        array('type' => self::TYPE_DATE,'copy_post' => false),
            'ip_registration_newsletter' =>    array('type' => self::TYPE_STRING, 'copy_post' => false),
            'optin' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'website' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'company' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'siret' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isSiret'),
            'ape' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
            'outstanding_allow_amount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'show_public_prices' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_risk' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'max_payment_days' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'active' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'deleted' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'note' =>                        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
            'is_guest' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_shop' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_default_group' =>            array('type' => self::TYPE_INT, 'copy_post' => false),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public function __construct($id = null)
    {
        $this->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
        parent::__construct($id);
        if ($this->middlename == '') {
            $this->middlename = ' ';
        }
    }


    public static function getCustomers($only_active = null)
    {
        $sql = 'SELECT `id_customer`, `email`, `firstname`, `lastname`, `middlename`
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
            ($only_active ? ' AND `active` = 1' : '').'
				ORDER BY `id_customer` ASC';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function transformToCustomer($id_lang, $password = null)
    {
        if (!$this->isGuest()) {
            return false;
        }
        if (empty($password)) {
            $password = Tools::passwdGen(8, 'RANDOM');
        }
        if (!Validate::isPasswd($password)) {
            return false;
        }

        $this->is_guest = 0;
        $this->passwd = Tools::encrypt($password);
        $this->cleanGroups();
        $this->addGroups(array(Configuration::get('PS_CUSTOMER_GROUP'))); // add default customer group
        if ($this->update()) {
            $vars = array(
                '{firstname}' => $this->firstname,
                '{lastname}' => $this->lastname,
                '{middlename}' => $this->middlename,
                '{email}' => $this->email,
                '{passwd}' => $password
            );

            Mail::Send(
                (int)$id_lang,
                'guest_to_customer',
                Mail::l('Your guest account has been transformed into a customer account', (int)$id_lang),
                $vars,
                $this->email,
                $this->firstname.' '.$this->lastname.' '.$this->middlename,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                false,
                (int)$this->id_shop
            );
            return true;
        }
        return false;
    }

    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function getCustomerSnService() {
        $sn_servl = Context::getContext()->fr_sn_servlist;
        if (isset($sn_servl)) {
            $sql_id = (isset($this->id)) ? $this->id : -1;
            $sn_serv_customer = Db::getInstance()->ExecuteS('
                SELECT id_sn_service, sn_id
                FROM '._DB_PREFIX_.'sn_customer
                WHERE id_customer='.(int)$sql_id
            );
            if (!empty($sn_servl)) {
                foreach ($sn_servl AS $key=>$sn_serv) {
                    $sn_id = '';
                    if ($sn_serv_customer) {
                        foreach ($sn_serv_customer AS $sn_serv_cust)
                            if ($sn_serv_cust['id_sn_service'] == $sn_serv['id_sn_service'])
                                $sn_id = $sn_serv_cust['sn_id'];
                    }
                    $this->sn_service[$key] = $sn_id;
                }
            }
        }
    }
    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function deleteCustomerSnAccount($sn) {

        $result = true;
        $where = '`id_customer` = '.(int)($this->id).' AND `id_sn_service` = '.(int)($sn);
        $result = Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.'sn_customer').'` WHERE '.$where);

        return $result;

    }

    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function addCustomerSnAccount($sn, $id_sn) {

        $result = true;
        $result = Db::getInstance()->AutoExecute(_DB_PREFIX_.'sn_customer', array('id_customer'=>(int)($this->id),'id_sn_service'=>(int)($sn),'sn_id'=>$id_sn), 'INSERT');

        return $result;

    }
    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function updateCustomerSnAccount($sn, $id_sn) {

        $result = true;
        $where = '`id_customer` = '.(int)($this->id).' AND `id_sn_service` = '.(int)($sn);
        $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'sn_customer WHERE '.$where);
        if ($result)
            $result = Db::getInstance()->AutoExecute(_DB_PREFIX_.'sn_customer', array('sn_id'=>$id_sn), 'UPDATE', $where);
        else
            $result = $this->addCustomerSnAccount ($sn, $id_sn);
        return $result;
    }
    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function getBySNId($sn_id, $sn_user_id) {
        $result = Db::getInstance()->getRow('
            SELECT c.* 
            FROM  `'._DB_PREFIX_.'customer` AS c
            LEFT JOIN  `'._DB_PREFIX_.'sn_customer` AS s
            USING (id_customer ) 
            WHERE c.`active` =1
            AND c.`deleted` =0
            AND c.`is_guest` =0
            AND s.`id_sn_service` ='.trim($sn_id).'
            AND s.`sn_id` ="'.trim($sn_user_id).'"'
        );
        if (!$result)
            return false;
        $this->id = $result['id_customer'];
        foreach ($result AS $key => $value)
            if (key_exists($key, $this))
                $this->{$key} = $value;
        return $this;
    }
    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function logout() {

        if (isset(Context::getContext()->cookie)) {
            $cookies = Context::getContext()->cookie;
            $cookies->unsetFamily('__snconnect_');
            $cookies->logout();
        }
        $this->logged = 0;

    }
    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function mylogout() {

        if (isset(Context::getContext()->cookie)) {
            $cookies = Context::getContext()->cookie;
            $cookies->unsetFamily('__snconnect_');
            $cookies->mylogout();
        }
        $this->logged = 0;
    }
    /*
    * module: frsnconnect
    * date: 2016-10-06 08:37:16
    * version: 0.16.1
    */
    public function delete() {

        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'sn_customer` WHERE `id_customer` = '.(int)($this->id));
        return parent::delete();

    }

}
