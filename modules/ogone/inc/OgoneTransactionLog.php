<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class OgoneTransactionLog extends ObjectModel
{

    /**
     * Cart id. Is used as ORDERID in transaction processing
     * @var int
     */
    public $id_cart;

    /**
     * Order id
     * @var int Id order
     */
    public $id_order;

    /**
     * Customer
     * @var int
     */
    public $id_customer;

    /**
     * @var string Ogone PAYID
     */
    public $payid;

    /**
     * Ogone response status code (numeric)
     * @var int
     */
    public $status;

    /**
     * Json-encoded raw Ogone response
     * @var string
     */
    public $response;

    public $date_add;

    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ogone_tl',
        'primary' => 'id_ogone_tl',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'payid' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'response' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Returns decoded response as array or empty array on error
     */
    public function getResponseDecoded()
    {
        $decoded = self::decodeResponse($this->response);
        return (is_array($decoded)) ? $decoded : array();
    }

    /**
     * Encodes response to format which can be stocked in database
     * @param mixed $response
     * @return string Encoded response
     */
    public static function encodeResponse($response)
    {
        return Tools::jsonEncode($response);
    }

    /**
     * Decodes response
     * @param string $response
     * @return array Decoded response
     */
    public static function decodeResponse($response)
    {
        return Tools::jsonDecode($response, true);
    }

    public static function getAllByCartId($id_cart)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE id_cart=' . (int) $id_cart . ' ORDER BY date_add DESC, id_ogone_tl DESC';
        return Db::getInstance()->executeS($query);
    }

    public static function getAllByOrderId($id_order)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE id_order=' . (int) $id_order . ' ORDER BY date_add DESC, id_ogone_tl DESC';
        return Db::getInstance()->executeS($query);
    }

    public static function getAllByPayId($payid)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE paid=' . pSql($payid) . ' ORDER BY date_add DESC, id_ogone_tl DESC';
        return Db::getInstance()->executeS($query);
    }

    public static function getLastByCartId($id_cart)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE id_cart=' . (int) $id_cart . ' ORDER BY date_add DESC, id_ogone_tl DESC';
        return Db::getInstance()->getRow($query);
    }

    public static function getLastByOrderId($id_order)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE id_order=' . (int) $id_order . ' ORDER BY date_add DESC, id_ogone_tl DESC';
        return Db::getInstance()->getRow($query);
    }

    public static function getLastByPayId($payid)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE paid=' . pSql($payid) . ' ORDER BY date_add DESC, id_ogone_tl DESC';
        return Db::getInstance()->getRow($query);
    }
}
