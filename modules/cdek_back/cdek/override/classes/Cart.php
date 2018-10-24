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

class Cart extends CartCore
{
	public $id_carrier_current = null;
	public function getPackageShippingCost($id_carrier = null, $use_tax = true,
											Country $default_country = null, $product_list = null, $id_zone = null)
	{
		$this->id_carrier_current = $id_carrier;
		return parent::getPackageShippingCost($id_carrier, $use_tax, $default_country, $product_list, $id_zone);
	}

	protected static $total_weight_calc = array();
	public function getTotalWeight($products = null)
	{
		if (Module::isEnabled('cdek'))
		{
			if (!class_exists('CarrierTariff'))
				$module = Module::getInstanceByName('cdek');
			unset($module);
			$carrier = new Carrier($this->id_carrier_current);
			if (Validate::isLoadedObject($carrier))
			{
				$id_tariff = (int)CarrierTariff::getTariffIdByCarrierReference($carrier->id_reference);
				if ($id_tariff)
				{
					if (!is_null($products))
					{
						$total_weight = 0;
						$total_calc_weight = 0;
						foreach ($products as $product)
						{
							if (!isset($product['weight_attribute']) || is_null($product['weight_attribute']))
								$total_weight += $product['weight'] * $product['cart_quantity'];
							else
								$total_weight += $product['weight_attribute'] * $product['cart_quantity'];
							$total_calc_weight += (($product['width'] * $product['height'] * $product['depth']) / 5000) * $product['cart_quantity'];
						}
						return max($total_weight, $total_calc_weight);
					}

					if (!isset(self::$total_weight_calc[$this->id]))
					{
						if (Combination::isFeatureActive())
						{
							$weight_product_with_attribute = Db::getInstance()->getValue('
									SELECT GREATEST(SUM((((p.`width` * p.`height` * p.`depth`) / 5000) * cp.`quantity`)),
									 SUM((p.`weight` + pa.`weight`) * cp.`quantity`)) as nb
									FROM `'._DB_PREFIX_.'cart_product` cp
									LEFT JOIN `'._DB_PREFIX_.'product` p ON (cp.`id_product` = p.`id_product`)
									LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (cp.`id_product_attribute` = pa.`id_product_attribute`)
									WHERE (cp.`id_product_attribute` IS NOT NULL AND cp.`id_product_attribute` != 0)
									AND cp.`id_cart` = '.(int)$this->id);
						}
						else
							$weight_product_with_attribute = 0;

						$weight_product_without_attribute = Db::getInstance()->getValue('
								SELECT GREATEST(SUM((((p.`width` * p.`height` * p.`depth`) / 5000) * cp.`quantity`)),
								 SUM(p.`weight` * cp.`quantity`)) as nb
								FROM `'._DB_PREFIX_.'cart_product` cp
								LEFT JOIN `'._DB_PREFIX_.'product` p ON (cp.`id_product` = p.`id_product`)
								WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
								AND cp.`id_cart` = '.(int)$this->id);

						self::$total_weight_calc[$this->id] = round((float)$weight_product_with_attribute + (float)$weight_product_without_attribute, 6);
					}

					return self::$total_weight_calc[$this->id];
				}
			}
		}

		return parent::getTotalWeight($products);
	}
}