<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class PWBlockHTMLObject extends ObjectModel
{
    public $id_pwblockhtml;
    public $name;
    public $hooks;
    public $html;
    public $html_editor;
    public $need_css;
    public $css;
    public $css_editor;
    public $need_js;
    public $js;
    public $js_editor;
    public $order;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'     => 'pwblockhtml',
        'primary'   => 'id_pwblockhtml',
        'multilang' => false,
        'multishop' => false,
        'fields'    => array
        (
            'name'        => array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => true),
            'hooks'       => array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => true),
            'html'        => array('type' => self::TYPE_NOTHING,                           'required' => false),
            'html_editor' => array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false),
            'need_css'    => array('type' => self::TYPE_INT,     'validate' => 'isInt',    'required' => false),
            'css'         => array('type' => self::TYPE_NOTHING,                           'required' => false),
            'css_editor'  => array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false),
            'need_js'     => array('type' => self::TYPE_INT,     'validate' => 'isInt',    'required' => false),
            'js'          => array('type' => self::TYPE_NOTHING,                           'required' => false),
            'js_editor'   => array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false),
            'order'       => array('type' => self::TYPE_INT,     'validate' => 'isInt',    'required' => false),
            'active'      => array('type' => self::TYPE_INT,     'validate' => 'isInt',    'required' => false),
            'date_add'    => array('type' => self::TYPE_DATE,    'validate' => 'isDate',   'required' => false),
            'date_upd'    => array('type' => self::TYPE_DATE,    'validate' => 'isDate',   'required' => false),
        ),
    );
    
        
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getAllHTMLBlocks($id_shop = null)
    {
        if(empty($id_shop)){
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table'].' t 
                LEFT JOIN '._DB_PREFIX_.self::$definition['table'].'_shop ts on(t.'.self::$definition['primary'].' = ts.'.self::$definition['primary'].')
                WHERE id_shop = '.$id_shop;
        if ( !($blocks = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql)) ) return array();

        foreach ($blocks as $key => $row)
            $blocks[$key]['hooks'] = Tools::jsonDecode($row['hooks']);

        uasort($blocks, function($a, $b) {
            $sort_key = 'order';
            if ( $a[$sort_key] == $b[$sort_key] ) return 0;
            return ( $a[$sort_key] > $b[$sort_key] ) ? 1 : -1;
        });

        return $blocks;
    }

    public static function getActiveHTMLBlocksByHook($hook_name)
    {
        $hook_id     = Hook::getIdByName($hook_name);
        $html_blocks = PWBlockHTMLObject::getAllHTMLBlocks(Context::getContext()->shop->id);
        $active_blocks = array();
        foreach ($html_blocks as $html_block){
            $hooks = array_flip($html_block['hooks']);
            if ( in_array($hook_id, $html_block['hooks']) AND $html_block['active'] )
                $active_blocks[] = $html_block;
            elseif(in_array(strtolower($hook_name), $html_block['hooks']) AND $html_block['active'] )
                $active_blocks[] = $html_block;
        }
        return $active_blocks;
    }

    public static function getBlockHTML($id_pwblockhtml)
    {
        if ( !$id_pwblockhtml ) return array();

        $block_html = new PWBlockHTMLObject($id_pwblockhtml);
        return $block_html->getFields();
    }
    
    /*возвращает все используемые хуки*/
    /*extends - id блока, ля которого не надо возвращать хуки*/
    public static function getExistingHooks($extends = false)
    {
        $existing_hooks = Db::getInstance()->executeS('SELECT `hooks` FROM `'._DB_PREFIX_.self::$definition['table'].'` '.
                ($extends?'WHERE id_pwblockhtml != '.$extends:''));
        $ex_hooks = array();
        foreach($existing_hooks as $hook){
            $h = Tools::jsonDecode($hook['hooks'], true);
            $ex_hooks = array_merge($ex_hooks, $h);
        }
        return $ex_hooks;
    }
}
