<?php

class BlogCategory extends ObjectModel
{
    public $id_smart_blog_category;
    public $id_parent;
    public $position;
    public $desc_limit;
    public $active = 1;
    public $created;
    public $modified;
    public $meta_title;
    public $meta_keyword;
    public $meta_description;
    public $description;
    public $link_rewrite;

    public static $definition = array(
        'table' => 'smart_blog_category',
        'primary' => 'id_smart_blog_category',
        'multilang' => true,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'desc_limit' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'modified' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),

            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true),
            'meta_keyword' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true)
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('smart_blog_category', array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getRootCategory($id_lang = null)
    {
        if ($id_lang == NULL)
            $id_lang = (int)Context::getContext()->language->id;
        $root_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int)($id_lang) . ')
		INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and sbs.id_shop = ' . (int)Context::getContext()->shop->id . '   WHERE sbc.`active`= 1 AND sbc.id_parent = 0');
        return $root_category;
    }

    public static function getNameCategory($id)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->getRow($sql))
            return false;
        return $result;
    }

    public static function getCatName($id)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT pl.meta_title FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result[0]['meta_title'];
    }

    public static function getCatLinkRewrite($id)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $sql = 'SELECT pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_category_lang pl, ' . _DB_PREFIX_ . 'smart_blog_category p
                       WHERE pl.id_smart_blog_category=p.id_smart_blog_category AND p.id_smart_blog_category=' . $id . ' AND pl.id_lang = ' . $id_lang;
        if (!$result = Db::getInstance()->getRow($sql))
            return false;
        return $result['link_rewrite'];
    }

    public static function getCatImage()
    {
        $id_lang = (int)Context::getContext()->language->id;

        $sql = 'SELECT id_smart_blog_category FROM ' . _DB_PREFIX_ . 'smart_blog_category';

        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result;
    }

    public static function getCategory($active = 1, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = (int)Context::getContext()->language->id;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT * FROM `' . _DB_PREFIX_ . 'smart_blog_category` sbc INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_lang` sbcl ON(sbc.`id_smart_blog_category` = sbcl.`id_smart_blog_category` AND sbcl.`id_lang` = ' . (int)($id_lang) . ')
		INNER JOIN `' . _DB_PREFIX_ . 'smart_blog_category_shop` sbs ON sbs.id_smart_blog_category = sbc.id_smart_blog_category and sbs.id_shop = ' . (int)Context::getContext()->shop->id . ' WHERE sbc.`active`= 1');

        return $result;
    }

    public static function getCategoryNameByPost($id_post)
    {

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.id_category FROM `' . _DB_PREFIX_ . 'smart_blog_post` p where p.id_smart_blog_post =  ' . $id_post);

        return $result[0]['id_category'];
    }

    public static function getPostByCategory($id_smart_blog_category)
    {
        $sql = 'select count(id_smart_blog_post) as count from `' . _DB_PREFIX_ . 'smart_blog_post` where id_category = ' . $id_smart_blog_category;

        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result[0]['count'];
    }


}