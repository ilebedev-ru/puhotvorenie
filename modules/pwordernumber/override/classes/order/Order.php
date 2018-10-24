<?php

class Order extends OrderCore
{
    public static function generateReference()
    {
        switch((int)Configuration::get('PWORDERNUMBER_OPTION1')){
            case 1:
                $last_id = Db::getInstance()->getValue('
                  SELECT MAX(id_order)
                  FROM '._DB_PREFIX_.'orders');
                return str_pad((int)$last_id + 1, 7, '0000000', STR_PAD_LEFT);
                break;
            case 2:
                return Tools::passwdGen(9, 'NUMERIC');
                break;
            default: /*Отключено */
                return strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
                break;
        }

    }
}
