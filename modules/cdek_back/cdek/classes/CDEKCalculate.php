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

class CDEKCalculate
{
	/**
	 * @var string
	 */
	protected $api_path = 'http://api.cdek.ru/calculator/calculate_price_by_json.php';
	protected $version_api = '1.0';
	/**
	 * @var string
	 */
	protected $date_execute;
	/**
	 * @var Cart
	 */
	protected $cart;

	public function __construct(Cart $cart)
	{
		$this->date_execute = date('Y-m-d');
		$this->cart = $cart;
	}

	protected function sendRequest($data)
	{
		$data_string = Tools::jsonEncode($data);
		$CDEKCacheId=md5($data_string);

		$context=Context::getContext();
		if(isset($context->cookie->CDEKCache)){$CDEKCache=Tools::jsonDecode($context->cookie->CDEKCache,true);}
		else{$CDEKCache=Array();}
		
		if(isset($CDEKCache[$CDEKCacheId]))
		{
			return $CDEKCache[$CDEKCacheId];
		}
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->api_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json')
		);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$result = curl_exec($ch);
		curl_close($ch);

		$CDEKCache[$CDEKCacheId]=Tools::jsonDecode($result, true);
		$context->cookie->CDEKCache=Tools::jsonEncode($CDEKCache);

        return $CDEKCache[$CDEKCacheId];
	}

	public function calculate()
	{
		$data = array();
		if (!isset($this->cart->id_carrier_current))
			throw new Exception('Class Cart in module cdek no override!');
		$carrier = new Carrier($this->cart->id_carrier_current);
		if (!Validate::isLoadedObject($carrier))
			throw  new Exception('Can not load carrier!');

		isset($this->version_api) ? $data['version'] = $this->version_api : '';
		$id_tariff = CarrierTariff::getTariffIdByCarrierReference($carrier->id_reference);
		if (!$id_tariff)
			throw new Exception('Tariff not found!');

		$data['dateExecute'] = $this->date_execute;
		$data['authLogin'] = CDEKConf::getAccount();
		$data['secure'] = CDEKConf::getSecure($this->date_execute);
		$data['tariffId'] = $id_tariff;
		/**
		 * @var $address Address
		 */
		$address = $this->cart->getAddressCollection();
		$address = current($address);
		$data['receiverCityPostCode'] = $address->postcode;
		$data['senderCityPostCode'] = CDEKConf::getPostcode();
        if (ConfSK::getConf('calculate_from_index')) {
            $data['receiverCityPostCode'] = $address->postcode;
            $data['senderCityPostCode'] = CDEKConf::getPostcode();
        } else {
            $receiverCity = CDEKCity::getCityByName($address->city);
            $senderCity = CDEKCity::getCityByName(CDEKConf::getPostcode());

            $data['receiverCityId'] = $receiverCity->id_cdek;
            $data['senderCityId'] = $senderCity->id_cdek;
        }


		$tariff = CDEKConf::getTariffInfo($id_tariff);
		if (!$tariff)
			throw new Exception('Tariff info not found!');
		$data['modeId'] = $tariff['mode'];

		$data['goods'] = array();
		foreach ($this->cart->getProducts() as $product)
		{
			$product['length'] = &$product['depth'];
			$product['width'] = self::getProductDimensions($product, 'width');
			$product['height'] = self::getProductDimensions($product, 'height');
			$product['length'] = self::getProductDimensions($product, 'length');
			$product['weight'] = self::getProductDimensions(
			    $product,
                'weight'
            ) * (float)ConfSK::getConf('weight_unit');
            $product['weight'] = ceil($product['weight']);

			$width = $product['width'] * $product['cart_quantity'];
			$height = $product['height'];
			$length = $product['depth'];
			$volume = ($width * $height * $length) * 0.000001;
			$weight = (($product['width'] * $product['height'] * $product['depth']) / 5000) * $product['cart_quantity'];

			$data['goods'][] = array(
				'width' => $width,
				'height' => $height,
				'length' => $length,
				'volume' => $volume,
				'weight' => max(
					$weight,
					($product['weight'] * $product['cart_quantity'])
				) * (float)ConfSK::getConf('weight_unit')
			);
		}

		if (!extension_loaded('curl'))
			throw new Exception('No connection with CURL');

		$errors = array();
		$result = null;
		$response = $this->sendRequest($data);
		if (isset($response['result']) && !empty($response['result']))
			$result = $response['result'];
		else
		{
			if (is_array($response['error']))
				$errors = $response['error'];
			else
				$errors[] = $response['error'];
		}

		if (is_array($errors) && count($errors))
			array_unshift($errors, var_export($data, true));

		if ($result['price'] <= 500){
            $result['price'] = 380;

            if ($result['tariffId'] == 136)
                $result['price'] = 0;
		}

		return array(
			'has_error' => (count($errors) ? true : false),
			'result' => $result,
			'errors' => $errors,
			'overprice' => $result['price'] > 500 && $result['tariffId'] == 137 ? true : false
		);
	}

	public static function getProductDimensions($product, $property)
	{
		$value = (float)$product[$property];
		$settings = FormatConfCategories::getSettings();

		if ($value <= 0) {
			if (array_key_exists($product['id_category_default'], $settings)
			&& (float)$settings[$product['id_category_default']][$property] > 0) {
                $value = (float)$settings[$product['id_category_default']][$property];
            } else {
                $value = (float)ConfSK::getConf($property);
            }
		}

		return $value;
	}
}