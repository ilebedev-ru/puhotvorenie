<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.2015
 * Time: 19:40
 */
class Cart extends CartCore
{
    public function getPackageList($flush = false)
    {
        return parent::getPackageList(true); //Всегда сбрасываешь Кэш
    }
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        return parent::getDeliveryOptionList($default_country, true);
    }
}