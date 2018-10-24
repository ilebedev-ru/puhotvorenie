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

class CDEKDocument
{

	protected $api_path;
	protected $root_name;
	protected $root_attrs = array();

	const API_URL = 'https://integration.cdek.ru/';

	/**
	 * @return array
	 */
	protected function getXMLRequest()
	{
		return array();
	}

	protected function createRequest()
	{
		$xml_request = Array2xml::createXML($this->root_name, array_merge(array(
			'@attributes' => $this->root_attrs
		), $this->getXMLRequest()));
		return array(
			'xml_request' => $xml_request->saveXML()
		);
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function sendRequest($data)
	{
		$user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322) -
		 See more at: http://parsing-and-i.blogspot.ru/2009/10/curl-post.html#sthash.u6swjOkU.dpuf';
		$data_string = http_build_query($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		$result = curl_exec($ch);
		curl_close($ch);
		return $this->parseResponse($result);
	}

	protected function parseResponse($response)
	{
		return XML2Array::createArray($response);
	}

	public function send()
	{
		$request = $this->createRequest();
		ErrorLoggerSK::getInstance()->add(var_export($request, true));
		$response = $this->sendRequest($request);
        $error_codes = array();
        if (get_called_class() == 'CDEKNewOrders') {
            if (ToolsModuleSK::checkItemArray('response', $response)) {
                if (ToolsModuleSK::checkItemArray('Order', $response['response'])) {
                    $order = $response['response']['Order'];
                    if (is_array($order) && count($order)) {
                        foreach ($order as $item) {
                            if (ToolsModuleSK::checkItemArray('@attributes', $item)) {
                                $attributes = $item['@attributes'];
                                if (is_array($attributes) && count($attributes)
                                && isset($attributes['ErrorCode'])) {
                                    $error_codes[] = sprintf(
                                        '[%s] %s (%s)',
                                        $attributes['ErrorCode'],
                                        $attributes['Msg'],
                                        $attributes['Number']
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        if (property_exists($this, 'order') && in_array(
                get_called_class(),
                array('CDEKNewOrders', 'CDEKDeleteOrders')
        )) {
            $order_info = CDEKOrderInfo::getInstanceByCart($this->order->id_cart);
            $order_info->error_create_order = implode(',', $error_codes);
            $order_info->save();
        }
		ErrorLoggerSK::getInstance()->add(var_export($response, true));
		return $response;
	}
}