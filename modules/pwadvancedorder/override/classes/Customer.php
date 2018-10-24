<?php

class Customer extends CustomerCore
{

    /** @var string Middlename */
    public $middlename;

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
}