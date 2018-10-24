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

class CDEKConf
{
	/**
	 * @return string
	 * @see account for integration get in CDEK account
	 */
	public static function getAccount()
	{
		return ConfSK::getConf('account');
	}

	/**
	 * @return string
	 * @see secret code, it can be obtained from  CDEK
	 */
	public static function getSecurePassword()
	{
		return ConfSK::getConf('secure_password');
	}

	public static function getDate($date = '0000-00-00T00:00:00')
	{
		$date = str_replace('T', '*', $date);
		return str_replace('*', 'T', date($date));
	}

	public static function getSecure($date = '0000-00-00T00:00:00')
	{
		return md5(self::getDate($date).'&'.self::getSecurePassword());
	}

	public static function getPostcode()
	{
		return ConfSK::getConf('postcode');
	}

	public static function getDeleteOrderOrderState()
	{
		return ConfSK::getConf('delete_order_order_state');
	}

	const MODE_DOOR_DOOR = 1;
	const MODE_DOOR_STOCK = 2;
	const MODE_STOCK_DOOR = 3;
	const MODE_STOCK_STOCK = 4;

	/**
	 * @return array
	 */
	public static function getTariffs()
	{
		$t = TransModSK::getInstance();
		return array(
			array(
				'id' => '1',
				'name' => $t->l('Light Express door-to-door', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '3',
				'name' => $t->l('Super Express 18', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '5',
				'name' => $t->l('Economy Express warehouse-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '10',
				'name' => $t->l('Express warehouse light-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '11',
				'name' => $t->l('Express warehouse light-door', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_STOCK_DOOR
			),
			array(
				'id' => '12',
				'name' => $t->l('Express warehouse door light', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_DOOR_STOCK
			),
			array(
				'id' => '15',
				'name' => $t->l('Express warehouse storage heavyweights', __FILE__),
				'range' => array(
					'min' => 30,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '16',
				'name' => $t->l('Express heavyweights warehouse-door', __FILE__),
				'range' => array(
					'min' => 30,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_DOOR
			),
			array(
				'id' => '17',
				'name' => $t->l('Express warehouse door heavyweights', __FILE__),
				'range' => array(
					'min' => 30,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_STOCK
			),
			array(
				'id' => '18',
				'name' => $t->l('heavyweights Express door-to-door', __FILE__),
				'range' => array(
					'min' => 30,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '57',
				'name' => $t->l('Super Express to 9', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 5
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '58',
				'name' => $t->l('Super Express 10', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 5
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '59',
				'name' => $t->l('Super Express 12', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 5
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '60',
				'name' => $t->l('Super Express 14', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 5
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '61',
				'name' => $t->l('Super Express 16', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '62',
				'name' => $t->l('Bulk Express warehouse-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '63',
				'name' => $t->l('super-rapid Bulk storage warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '136',
				'name' => $t->l('Making a warehouse-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '137',
				'name' => $t->l('Making a warehouse-door', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_STOCK_DOOR
			),
			array(
				'id' => '138',
				'name' => $t->l('Making a door-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_DOOR_STOCK
			),
			array(
				'id' => '139',
				'name' => $t->l('Making a door-to-door', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 30
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '233',
				'name' => $t->l('Cost-door parcel depot', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 50
				),
				'mode' => self::MODE_STOCK_DOOR
			),
			array(
				'id' => '234',
				'name' => $t->l('Cost-premise storage warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 50
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '291',
				'name' => $t->l('CDEK Express warehouse-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '293',
				'name' => $t->l('CDEK Express door-to-door', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '294',
				'name' => $t->l('CDEK Express warehouse-door', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_DOOR
			),
			array(
				'id' => '295',
				'name' => $t->l('CDEK Express door-warehouse', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '243',
				'name' => $t->l('China Express', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_STOCK
			),
			array(
				'id' => '245',
				'name' => $t->l('China Express', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_DOOR
			),
			array(
				'id' => '246',
				'name' => $t->l('China Express (warehouse door)', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_STOCK_DOOR
			),
			array(
				'id' => '247',
				'name' => $t->l('China Express (door-warehouse)', __FILE__),
				'range' => array(
					'min' => 0,
					'max' => 100500
				),
				'mode' => self::MODE_DOOR_STOCK
			)
		);
	}

	public static function getTariffInfo($id_tariff)
	{
		$tariffs = self::getTariffs();
		foreach ($tariffs as $tariff)
			if ((int)$tariff['id'] == $id_tariff)
				return $tariff;
		return false;
	}
}