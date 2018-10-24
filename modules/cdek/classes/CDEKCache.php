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

class CDEKCache extends ObjectModel
{
    /**
     * @var int
     */
    public $id_cart;
    /**
     * @var int
     */
    public $id_carrier;
    /**
     * @var string
     */
    public $key_products;
    /**
     * @var string
     */
    public $postcode;
    /**
     * @var string
     */
    public $data;

    public static $definition = array(
        'table' => 'cdek_cache',
        'primary' => 'id_cdek_cache',
        'fields' => array(
            'id_cart' => array(
                'type' => self::TYPE_INT,
                'validate' => ValidateTypeSK::IS_INT
            ),
            'id_carrier' => array(
                'type' => self::TYPE_INT,
                'validate' => ValidateTypeSK::IS_INT
            ),
            'key_products' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'postcode' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'data' => array(
                'type' => self::TYPE_HTML,
                'validate' => ValidateTypeSK::IS_ANYTHING
            )
        )
    );

    public static function getCache($id_cart, $id_carrier)
    {
        $data = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'cdek_cache`
            WHERE `id_cart` = ' . (int)$id_cart . ' AND `id_carrier` = ' . (int)$id_carrier
        );
        return (is_array($data) ? Tools::jsonDecode($data['data'], true) : false);
    }

    public static function isExistCache($id_cart, $id_carrier, $products, $postcode)
    {
        $key_products = self::getKeyProducts($products);
        $id = self::isExistsProductCache(
            $id_cart,
            $id_carrier,
            $key_products,
            $postcode
        );
        return ($id ? true : false);
    }

    public static function getKeyProducts($products)
    {
        $keys_products = array();
        foreach ($products as $product) {
            $keys_products[] = implode(
                '_',
                array(
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $product['cart_quantity']
                )
            );
        }
//        if ($_SERVER['HTTP_X_REAL_IP'] == '109.197.193.177') {
//            $keys_products[] = 'dev';
//        }
        return implode('|', $keys_products);
    }

    public static function isExistsProductCache($id_cart, $id_carrier, $key_products, $postcode)
    {
        $id = Db::getInstance()->getValue(
            'SELECT `id_cdek_cache` FROM `' . _DB_PREFIX_ . 'cdek_cache`
            WHERE
            `id_cart` = ' . (int)$id_cart . '
            AND `id_carrier` = ' . (int)$id_carrier . '
            AND `key_products` = "' . pSQL($key_products) . '"
            AND `postcode` = "'.pSQL($postcode).'"'
        );
        return $id;
    }

    public static function getIdCache($id_cart, $id_carrier)
    {
        $id = Db::getInstance()->getValue(
            'SELECT `id_cdek_cache` FROM `'._DB_PREFIX_.'cdek_cache`
            WHERE
            `id_cart` = '.(int)$id_cart.'
            AND `id_carrier` = '.(int)$id_carrier
        );
        return $id;
    }

    public static function getInstanceCache($id_cart, $id_carrier, $key_products, $postcode)
    {
        $id = self::getIdCache(
            $id_cart,
            $id_carrier,
            $key_products,
            $postcode
        );
        $object = new self($id);
        $object->id_cart = $id_cart;
        $object->id_carrier = $id_carrier;
        $object->key_products = $key_products;
        $object->postcode = $postcode;

        return $object;
    }

    public static function setCache($id_cart, $id_carrier, $products, $postcode, $data)
    {
        $key_products = self::getKeyProducts($products);
        $cache = self::getInstanceCache(
            $id_cart,
            $id_carrier,
            $key_products,
            $postcode
        );
        $cache->data = Tools::jsonEncode($data);
        return $cache->save();
    }

    public static function clearByCart($id_cart)
    {
        return Db::getInstance()->delete(
            'cdek_cache',
            'id_cart = '.(int)$id_cart
        );
    }

    public static function clearCacheAll()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'cdek_cache');
    }
}