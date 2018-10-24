<?php
class Order extends OrderCore
{
    /*
    * module: pwordernumber
    * date: 2016-10-24 12:42:26
    * version: 1.0.0
    */
    public static function generateReference()
    {
        switch((int)Configuration::get('PWORDERNUMBER_OPTION1')){
            case 1:
                $last_id = Db::getInstance()->getValue('
                  SELECT MAX(id_order)
                  FROM '._DB_PREFIX_.'orders');
                return str_pad((int)$last_id + 1, 9, '0000000', STR_PAD_LEFT);
                break;
            case 2:
                return Tools::passwdGen(9, 'NUMERIC');
                break;
            default: 
                return strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
                break;
        }
    }
}
