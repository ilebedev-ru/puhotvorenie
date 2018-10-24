<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class FormatConfCarriers extends FormatConfSK
{
	public static function prepareSettingCategories($post_data)
	{
		$input_array = CarrierTariff::getAllCarriers(array());
		$properties = array(
			'commission' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT),
			'free_shipping' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_BOOL),
            'free_shipping_weight_from' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT),
            'free_shipping_price_from' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT)
        );
		return self::prepareSetting($post_data, $input_array, 'id_reference', $properties);
	}

	public static function getSettings()
	{
		return self::prepareSettingCategories(ConfSK::getConf('carriers_settings', ConfSK::TYPE_ARRAY));
	}
}