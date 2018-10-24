<?php
/**
* Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
*
* @category  Front Office Features
* @package   Yandex Payment Solution
* @author    Yandex.Money <cms@yamoney.ru>
* @copyright © 2015 NBCO Yandex.Money LLC
* @license   https://money.yandex.ru/doc.xml?id=527052
*/

class CartRule extends CartRuleCore
{

	public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true){
		print_r(parent::getContextualValue($use_tax, $context, $filter, $package, $use_cache));
		exit;
	}
}