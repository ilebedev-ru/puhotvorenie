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

class CDEKOrderInfo extends ObjectModel
{
    /**
     * @var string
     */
    public $tariff;

    /**
     * @var string
     */
    public $delivery_point;

    /**
     * @var string
     */
    public $delivery_date = '0000-00-00';

    /**
     * @var string
     */
    public $delivery_time_begin = '0:00';

    /**
     * @var string
     */
    public $delivery_time_end = '23:59';

    /**
     * @var int
     */
    public $id_cart;
    /**
     * @var string
     */
    public $street;
    /**
     * @var string
     */
    public $house;
    /**
     * @var string
     */
    public $flat;
    /**
     * @var string
     */
    public $pvz_key;
    /**
     * @var string
     */
    public $error_create_order;
    /**
     * @var float
     */
    public $weight;
    /**
     * @var string
     */
    public $history_response;
    /**
     * @var string
     */
    public $history_last_update;

    public static $definition = array(
        'table' => 'cdek_order_info',
        'primary' => 'id_cdek_order_info',
        'fields' => array(
            'tariff' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'delivery_point' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'id_cart' => array(
                'type' => self::TYPE_INT,
                'validate' => ValidateTypeSK::IS_INT
            ),
            'delivery_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => ValidateTypeSK::IS_DATE_FORMAT
            ),
            'delivery_time_begin' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'delivery_time_end' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'street' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'house' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'flat' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'pvz_key' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'error_create_order' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'weight' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => ValidateTypeSK::IS_FLOAT
            ),
            'history_response' => array(
                'type' => self::TYPE_HTML,
                'validate' => ValidateTypeSK::IS_ANYTHING
            ),
            'history_last_update' => array(
                'type' => self::TYPE_DATE,
                'validate' => ValidateTypeSK::IS_DATE_FORMAT
            )
        )
    );

    /**
     * @param $id_cart
     * @return CDEKOrderInfo
     */
    public static function getInstanceByCart($id_cart)
    {
        $id = (int)Db::getInstance()->getValue(
            'SELECT `id_cdek_order_info`
            FROM '._DB_PREFIX_.'cdek_order_info WHERE `id_cart` = '.(int)$id_cart
        );
        $object = new self($id);
        $object->id_cart = $id_cart;
        return $object;
    }

    public function formatWeight()
    {
        $this->weight /= 1000;
        return $this;
    }

    public function getHistoryResponse()
    {
        if (!$this->history_response) {
            return false;
        }
        $response = Tools::jsonDecode($this->history_response, true);
        if (!$response) {
            return false;
        }
        return $response;
    }

    public function setHistoryResponse($response)
    {
        $this->history_response = Tools::jsonEncode($response);
        $this->history_last_update = date('Y-m-d H:i:s');
    }
}