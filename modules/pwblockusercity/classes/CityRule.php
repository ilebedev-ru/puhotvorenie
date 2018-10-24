<?
Class CityRule extends ObjectModel{
    var $id;
    var $id_city_rule;
    var $city;
    var $description;

    protected	$fieldsRequired = array('city');
    protected	$fieldsSize = array('city' => 32);
    protected	$fieldsValidate = array('city' => 'isGenericName', 'description' => 'isString');

    protected	$table = 'pwblockusercity';
    protected 	$identifier = 'id_city_rule';

    public function getFields()
    {
        parent::validateFields();
        return array(
            'id_city_rule' => pSQL($this->id_city_rule),
            'city' => pSQL($this->city),
            'description' => pSQL($this->description, true)
        );
    }

    public function getList(){
        return Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pwblockusercity` WHERE 1 ORDER BY `id_city_rule`');
    }

    public static function getForCity($city){
        $result = Db::getInstance()->getValue('SELECT description FROM `'._DB_PREFIX_.'pwblockusercity` WHERE `city` LIKE "'.pSQL($city).'"');
        if(!$result) $result = Configuration::get('PS_PWBLOCKUSERCITY_RULE');
        return '<h3 style="font-size:18px;margin-bottom: 10px">Доставка в '.$city.'</h3>'.$result;
    }



}