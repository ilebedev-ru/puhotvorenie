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

class CarrierTariff extends ObjectModel
{
	public $id_reference;
	public $id_tariff;

	public static $definition = array(
		'table' => 'carrier_tariff',
		'primary' => 'id_carrier_tariff',
		'fields' => array(
			'id_reference' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_tariff' =>     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt')
		)
	);

	public static function getTariffIdByCarrierReference($id_reference)
	{
		return (int)Db::getInstance()->getValue('SELECT id_tariff FROM '._DB_PREFIX_.'carrier_tariff
		 WHERE id_reference = '.(int)$id_reference);
	}

	public static function getAll()
	{
		return Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'carrier_tariff');
	}

	public static $cache_carriers = null;
	/**
	 * @param array $delay_carriers
	 * @return array
	 */
	public static function getAllCarriers($delay_carriers, $cache = true)
	{
		if (!is_null(self::$cache_carriers) && $cache)
			return self::$cache_carriers;

		$all = self::getAll();
		$carrier_tariffs = array();
		$carrier_name_tariffs = array();
		$carrier_reference_tariffs = array();
		foreach ($all as $item)
		{
			$carrier = Carrier::getCarrierByReference((int)$item['id_reference']);
			$carrier_tariffs[(int)$item['id_tariff']] = $carrier->id;
			$carrier_name_tariffs[(int)$item['id_tariff']] = $carrier->name;
			$carrier_reference_tariffs[(int)$item['id_tariff']] = $carrier->id_reference;
		}

		$tariffs = CDEKConf::getTariffs();
		$carriers = array();
		foreach ($tariffs as $tariff)
		{
			$carriers[$carrier_tariffs[$tariff['id']]] = array(
				'id_tariff' => $tariff['id'],
				'name' => (array_key_exists($tariff['id'], $carrier_name_tariffs)
					? $carrier_name_tariffs[$tariff['id']] : ''),
				'id_reference' => (array_key_exists($tariff['id'], $carrier_reference_tariffs)
					? $carrier_reference_tariffs[$tariff['id']] : ''),
				'mode' => $tariff['mode'],
				'delay' => (array_key_exists($carrier_tariffs[$tariff['id']], $delay_carriers) ?
					$delay_carriers[$carrier_tariffs[$tariff['id']]] : '')
			);
		}

		self::$cache_carriers = $carriers;
		return self::$cache_carriers;
	}

	public static function getTariffByCarrier($id_carrier)
	{
		$carriers = self::getAllCarriers(array());
		if (array_key_exists($id_carrier, $carriers))
			return $carriers[$id_carrier];
		return false;
	}
}