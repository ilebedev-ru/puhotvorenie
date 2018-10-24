<?php

class Catseo extends ObjectModel
{
    public $id_pwcatseo;
    public $id_category;
    
    public $text;
    public $title;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'pwcatseo',
        'primary' => 'id_pwcatseo',
        'multilang' => true,
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isInt'),
            'title'       => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'text'        => array('type' => self::TYPE_HTML,   'lang' => true, 'validate' => 'isCleanHtml'),
        ),
    );
    
    public static function getInstanceByCategory($id_category, $id_lang = null)
    {
        $id_pwcatseo = Db::getInstance()->getValue('SELECT `id_pwcatseo` FROM '._DB_PREFIX_.'pwcatseo WHERE id_category='.(int)$id_category);
        return new self($id_pwcatseo, $id_lang);
    }

}