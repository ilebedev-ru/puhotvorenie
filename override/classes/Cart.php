<?php
class Cart extends CartCore
{
    /*
    * module: pwoneclick
    * date: 2016-09-06 15:13:52
    * version: 0.1
    */
    public function getPackageList($flush = false)
    {
        return parent::getPackageList(true); //Всегда сбрасываешь Кэш
    }
    /*
    * module: pwoneclick
    * date: 2016-09-06 15:13:52
    * version: 0.1
    */
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        return parent::getDeliveryOptionList($default_country, true);
    }
    public function getSummaryDetails($id_lang = null, $refresh = false)
    {
        $context = Context::getContext();
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }
        $delivery = new Address((int)$this->id_address_delivery);
        $invoice = new Address((int)$this->id_address_invoice);
        $formatted_addresses = array(
            'delivery' => AddressFormat::getFormattedLayoutData($delivery),
            'invoice' => AddressFormat::getFormattedLayoutData($invoice)
        );
        $base_total_tax_inc = $this->getOrderTotal(true);
        $base_total_tax_exc = $this->getOrderTotal(false);
        $total_tax = $base_total_tax_inc - $base_total_tax_exc;
        if ($total_tax < 0) {
            $total_tax = 0;
        }
        $currency = new Currency($this->id_currency);
        $products = $this->getProducts($refresh);
        foreach ($products as $key => &$product) {
            $product['price_without_quantity_discount'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                6,
                null,
                false,
                false
            );
            if ($product['reduction_type'] == 'amount') {
                $reduction = (!Product::getTaxCalculationMethod() ? (float)$product['price_wt'] : (float)$product['price']) - (float)$product['price_without_quantity_discount'];
                $product['reduction_formatted'] = Tools::displayPrice($reduction);
            }
        }
        $gift_products = array();
        $cart_rules = $this->getCartRules();
        $total_shipping = $this->getTotalShippingCost();
        $total_shipping_tax_exc = $this->getTotalShippingCost(null, false);
        $total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $total_discounts = $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $total_discounts_tax_exc = $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
        foreach ($cart_rules as &$cart_rule) {
            if ($cart_rule['free_shipping'] && (empty($cart_rule['code']) || preg_match('/^'.CartRule::BO_ORDER_CODE_PREFIX.'[0-9]+/', $cart_rule['code']))) {
                $cart_rule['value_real'] -= $total_shipping;
                $cart_rule['value_tax_exc'] -= $total_shipping_tax_exc;
                $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                if ($total_discounts > $cart_rule['value_real']) {
                    $total_discounts -= $total_shipping;
                }
                if ($total_discounts_tax_exc > $cart_rule['value_tax_exc']) {
                    $total_discounts_tax_exc -= $total_shipping_tax_exc;
                }
                $total_shipping = 0;
                $total_shipping_tax_exc = 0;
            }
            if ($cart_rule['gift_product']) {
                foreach ($products as $key => &$product) {
                    if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                        $total_products_wt = Tools::ps_round($total_products_wt - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $total_products = Tools::ps_round($total_products - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $total_discounts = Tools::ps_round($total_discounts - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $total_discounts_tax_exc = Tools::ps_round($total_discounts_tax_exc - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $product['total'] = Tools::ps_round($product['total'] - $product['price'], (int)$currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        $product['cart_quantity']--;
                        if (!$product['cart_quantity']) {
                            unset($products[$key]);
                        }
                        $gift_product = $product;
                        $gift_product['cart_quantity'] = 1;
                        $gift_product['price'] = 0;
                        $gift_product['price_wt'] = 0;
                        $gift_product['total_wt'] = 0;
                        $gift_product['total'] = 0;
                        $gift_product['gift'] = true;
                        $gift_products[] = $gift_product;
                        break; // One gift product per cart rule
                    }
                }
            }
        }
        foreach ($cart_rules as $key => &$cart_rule) {
            if (((float)$cart_rule['value_real'] == 0 && (int)$cart_rule['free_shipping'] == 0)) {
                unset($cart_rules[$key]);
            }
        }
        $summary = array(
            'delivery' => $delivery,
            'delivery_state' => State::getNameById($delivery->id_state),
            'invoice' => $invoice,
            'invoice_state' => State::getNameById($invoice->id_state),
            'formattedAddresses' => $formatted_addresses,
            'products' => array_values($products),
            'gift_products' => $gift_products,
            'discounts' => array_values($cart_rules),
            'is_virtual_cart' => (int)$this->isVirtualCart(),
            'total_discounts' => $total_discounts,
            'total_discounts_tax_exc' => $total_discounts_tax_exc,
            'total_wrapping' => $this->getOrderTotal(true, Cart::ONLY_WRAPPING),
            'total_wrapping_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_WRAPPING),
            'total_shipping' => $total_shipping,
            'total_shipping_tax_exc' => $total_shipping_tax_exc,
            'total_products_wt' => $total_products_wt,
            'total_products' => $total_products,
            'total_price' => $base_total_tax_inc,
            'total_tax' => $total_tax,
            'total_price_without_tax' => $base_total_tax_exc,
            'is_multi_address_delivery' => $this->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1),
            'free_ship' =>!$total_shipping && !count($this->getDeliveryAddressesWithoutCarriers(true, $errors)),
            'carrier' => new Carrier($this->id_carrier, $id_lang),
        );
        $hook = Hook::exec('actionCartSummary', $summary, null, true);
        if (is_array($hook)) {
            $summary = array_merge($summary, array_shift($hook));
        }
        return $summary;
    }
    public function isAttachCartRule($id_cart_rule)
    {
        $cart_rules = $this->getCartRules();
        foreach ($cart_rules as $key => &$cart_rule) {
            if (((float)$cart_rule['value_real'] == 0 && (int)$cart_rule['free_shipping'] == 0)) {
                unset($cart_rules[$key]);
            }
            if ($id_cart_rule == $cart_rule['id_cart_rule'])
                return true;
        }
        return false;
    }
    public function getOrderTotal($with_taxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = true)
    {
        $address_factory    = Adapter_ServiceLocator::get('Adapter_AddressFactory');
        $price_calculator    = Adapter_ServiceLocator::get('Adapter_ProductPriceCalculator');
        $configuration        = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        $ps_tax_address_type = $configuration->get('PS_TAX_ADDRESS_TYPE');
        $ps_use_ecotax = $configuration->get('PS_USE_ECOTAX');
        $ps_round_type = $configuration->get('PS_ROUND_TYPE');
        $ps_ecotax_tax_rules_group_id = $configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID');
        $compute_precision = $configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        if (!$this->id) {
            return 0;
        }
        $type = (int)$type;
        $array_type = array(
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;
        if (!in_array($type, $array_type)) {
            die(Tools::displayError());
        }
        $with_shipping = in_array($type, array(Cart::BOTH, Cart::ONLY_SHIPPING));
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
            if (is_null($products) && is_null($id_carrier)) {
                $shipping_fees = $this->getTotalShippingCost(null, (bool)$with_taxes);
            } else {
                $shipping_fees = $this->getPackageShippingCost((int)$id_carrier, (bool)$with_taxes, null, $products);
            }
        } else {
            $shipping_fees = 0;
        }
        if ($type == Cart::ONLY_SHIPPING) {
            return $shipping_fees;
        }
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }
        $param_product = true;
        if (is_null($products)) {
            $param_product = false;
            $products = $this->getProducts();
        }
        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }
        $order_total = 0;
        if (Tax::excludeTaxeOption()) {
            $with_taxes = false;
        }
        $products_total = array();
        $ecotax_total = 0;
        foreach ($products as $product) {
            if ($virtual_context->shop->id != $product['id_shop']) {
                $virtual_context->shop = new Shop((int)$product['id_shop']);
            }
            if ($ps_tax_address_type == 'id_address_invoice') {
                $id_address = (int)$this->id_address_invoice;
            } else {
                $id_address = (int)$product['id_address_delivery'];
            } // Get delivery address of the product from the cart
            if (!$address_factory->addressExists($id_address)) {
                $id_address = null;
            }
            $null = null;
            $price = $price_calculator->getProductPrice(
                (int)$product['id_product'],
                $with_taxes,
                (int)$product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['cart_quantity'],
                false,
                (int)$this->id_customer ? (int)$this->id_customer : null,
                (int)$this->id,
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context
            );
            $address = $address_factory->findOrCreate($id_address, true);
            if ($with_taxes) {
                $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $virtual_context);
                $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
            } else {
                $id_tax_rules_group = 0;
            }
            if (in_array($ps_round_type, array(Order::ROUND_ITEM, Order::ROUND_LINE))) {
                if (!isset($products_total[$id_tax_rules_group])) {
                    $products_total[$id_tax_rules_group] = 0;
                }
            } elseif (!isset($products_total[$id_tax_rules_group.'_'.$id_address])) {
                $products_total[$id_tax_rules_group.'_'.$id_address] = 0;
            }
            switch ($ps_round_type) {
                case Order::ROUND_TOTAL:
                    $products_total[$id_tax_rules_group.'_'.$id_address] += $price * (int)$product['cart_quantity'];
                    break;
                case Order::ROUND_LINE:
                    $product_price = $price * $product['cart_quantity'];
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision);
                    break;
                case Order::ROUND_ITEM:
                default:
                    $product_price = $price;
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision) * (int)$product['cart_quantity'];
                    break;
            }
        }
        foreach ($products_total as $key => $price) {
            $order_total += $price;
        }
        $order_total_products = $order_total;
        if ($type == Cart::ONLY_DISCOUNTS) {
            $order_total = 0;
        }
        $wrapping_fees = 0;
        $include_gift_wrapping = (!$configuration->get('PS_ATCP_SHIPWRAP') || $type !== Cart::ONLY_PRODUCTS);
        if ($this->gift && $include_gift_wrapping) {
            $wrapping_fees = Tools::convertPrice(Tools::ps_round($this->getGiftWrappingPrice($with_taxes), $compute_precision), Currency::getCurrencyInstance((int)$this->id_currency));
        }
        if ($type == Cart::ONLY_WRAPPING) {
            return $wrapping_fees;
        }
        $order_total_discount = 0;
        $order_shipping_discount = 0;
        if (!in_array($type, array(Cart::ONLY_SHIPPING, Cart::ONLY_PRODUCTS)) && CartRule::isFeatureActive()) {
            if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
                $cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_ALL);
            } else {
                $cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_REDUCTION);
                foreach ($this->getCartRules(CartRule::FILTER_ACTION_GIFT) as $tmp_cart_rule) {
                    $flag = false;
                    foreach ($cart_rules as $cart_rule) {
                        if ($tmp_cart_rule['id_cart_rule'] == $cart_rule['id_cart_rule']) {
                            $flag = true;
                        }
                    }
                    if (!$flag) {
                        $cart_rules[] = $tmp_cart_rule;
                    }
                }
            }
            $id_address_delivery = 0;
            if (isset($products[0])) {
                $id_address_delivery = (is_null($products) ? $this->id_address_delivery : $products[0]['id_address_delivery']);
            }
            $package = array('id_carrier' => $id_carrier, 'id_address' => $id_address_delivery, 'products' => $products);
            if ($this->isAttachCartRule(6) || (Tools::getValue('controller') == 'order' && $this->isAttachCartRule(5))) {
                $this->removeCartRule(5);
                foreach ($cart_rules as $i => $cart_rule) {
                    if ($cart_rule['id_cart_rule'] == 5) {
                        unset($cart_rules[$i]);
                    }
                }
            }
            $flag = false;
            foreach ($cart_rules as $cart_rule) {
                if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cart_rule['obj']->free_shipping && !$flag) {
                    $order_shipping_discount = (float)Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_SHIPPING, ($param_product ? $package : null), $use_cache), $compute_precision);
                    $flag = true;
                }
                if ((int)$cart_rule['obj']->gift_product) {
                    $in_order = false;
                    if (is_null($products)) {
                        $in_order = true;
                    } else {
                        foreach ($products as $product) {
                            if ($cart_rule['obj']->gift_product == $product['id_product'] && $cart_rule['obj']->gift_product_attribute == $product['id_product_attribute']) {
                                $in_order = true;
                            }
                        }
                    }
                    if ($in_order) {
                        $order_total_discount += $cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_GIFT, $package, $use_cache);
                    }
                }
                if ($cart_rule['obj']->reduction_percent > 0 || $cart_rule['obj']->reduction_amount > 0) {
                    $order_total_discount += Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_REDUCTION, $package, $use_cache), $compute_precision);
                }
            }
            $order_total_discount = min(Tools::ps_round($order_total_discount, 2), (float)$order_total_products) + (float)$order_shipping_discount;
            $order_total -= $order_total_discount;
        }
        if ($type == Cart::BOTH) {
            $order_total += $shipping_fees + $wrapping_fees;
        }
        if ($order_total < 0 && $type != Cart::ONLY_DISCOUNTS) {
            return 0;
        }
        if ($type == Cart::ONLY_DISCOUNTS) {
            return $order_total_discount;
        }
        return Tools::ps_round((float)$order_total, $compute_precision);
    }
    public function autosetProductAddressForcibly()
    {
        $id_address_delivery = 0;
        if ((int)$this->id_address_delivery > 0) {
            $id_address_delivery = (int)$this->id_address_delivery;
        } else {
            $id_address_delivery = (int)Address::getFirstCustomerAddressId(Context::getContext()->customer->id);
        }
        if (!$id_address_delivery) {
            return;
        }
        $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
			SET `id_address_delivery` = '.(int)$id_address_delivery.'
			WHERE `id_cart` = '.(int)$this->id.'
				AND `id_shop` = '.(int)$this->id_shop;
        Db::getInstance()->execute($sql);
        $sql = 'UPDATE `'._DB_PREFIX_.'customization`
			SET `id_address_delivery` = '.(int)$id_address_delivery.'
			WHERE `id_cart` = '.(int)$this->id;
        Db::getInstance()->execute($sql);
    }
	/*
    * module: cdek
    * date: 2018-10-20 20:02:57
    * version: 1.1.6
    */
    public $id_carrier_current = null;
	/*
    * module: cdek
    * date: 2018-10-20 20:02:57
    * version: 1.1.6
    */
    public function getPackageShippingCost($id_carrier = null, $use_tax = true,
											Country $default_country = null, $product_list = null, $id_zone = null)
	{
		$this->id_carrier_current = $id_carrier;
		return parent::getPackageShippingCost($id_carrier, $use_tax, $default_country, $product_list, $id_zone);
	}
	/*
    * module: cdek
    * date: 2018-10-20 20:02:57
    * version: 1.1.6
    */
    protected static $total_weight_calc = array();
	/*
    * module: cdek
    * date: 2018-10-20 20:02:57
    * version: 1.1.6
    */
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