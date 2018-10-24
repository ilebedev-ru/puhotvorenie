<?php

class SmartBlogPost extends ObjectModel {

    public $id_smart_blog_post;
    public $id_author;
    public $id_category;
    public $position = 0;
    public $active = 1;
    public $available;
    public $created;
    public $modified;
    public $short_description;
    public $viewed;
    public $comment_status = 1;
    public $post_type;
    public $meta_title;
    public $meta;
    public $title;
    public $meta_keyword;
    public $meta_description;
    public $image;
    public $content;
    public $link_rewrite;
    public $is_featured;
    public static $definition = array(
        'table' => 'smart_blog_post',
        'primary' => 'id_smart_blog_post',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_author' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'available' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'modified' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
            'viewed' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'is_featured' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'comment_status' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'post_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'meta_title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true, 'required' => true),
            'meta_keyword' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'),
            'short_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString', 'required' => true),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => false)
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null) {
        Shop::addTableAssociation('smart_blog_post', array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getPost($id_post, $id_lang = null) {
        $result = array();
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 AND p.id_smart_blog_post = ' . $id_post;

        if (!$post = Db::getInstance()->executeS($sql))
            return false;
        $result['id_post'] = $post[0]['id_smart_blog_post'];
        $result['title'] = $post[0]['title'];
        $result['meta_title'] = $post[0]['meta_title'];
        $result['meta_description'] = $post[0]['meta_description'];
        $result['short_description'] = $post[0]['short_description'];
        $result['meta_keyword'] = $post[0]['meta_keyword'];
        if ((Module::isEnabled('smartshortcode') == 1) && (Module::isInstalled('smartshortcode') == 1)) {
            require_once(_PS_MODULE_DIR_ . 'smartshortcode/smartshortcode.php');
            $smartshortcode = new SmartShortCode();
            $result['content'] = $smartshortcode->parse($post[0]['content']);
        } else {

            $result['content'] = $post[0]['content'];
        }
        $result['active'] = $post[0]['active'];
        $result['created'] = $post[0]['created'];
        $result['comment_status'] = $post[0]['comment_status'];
        $result['viewed'] = $post[0]['viewed'];
        $result['is_featured'] = $post[0]['is_featured'];
        $result['post_type'] = $post[0]['post_type'];
        $result['id_category'] = $post[0]['id_category'];
        $employee = new Employee($post[0]['id_author']);
        $result['lastname'] = $employee->lastname;
        $result['firstname'] = $employee->firstname;
        if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $post[0]['id_smart_blog_post'] . '.jpg')) {
            $image = $post[0]['id_smart_blog_post'] . '.jpg';
            $result['post_img'] = $image;
        } else {
            $result['post_img'] = NULL;
        }
        return $result;
    }

    public static function getProductsByIDs($id_lang, $ids, $orderBy = 'position', $orderWay = 'ASC', $active = false) {
        $ids2oneArray = Array();
        $ids2oneString = '';
        if(count($ids) && is_array($ids))
            foreach ($ids AS $value)
                if($value) $ids2oneArray[] = $value['id_product'];
        if (count($ids2oneArray))
            $ids2oneString = implode(', ', $ids2oneArray);
        if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay))
            die(Tools::displayError());
        if ($orderBy == 'id_product' || $orderBy == 'price' || $orderBy == 'date_add')
            $orderByPrefix = 'p';
        elseif ($orderBy == 'name')
            $orderByPrefix = 'pl';
        elseif ($orderBy == 'position')
            $orderByPrefix = 'c';
        if(!trim($ids2oneString)) return Array();
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`,
		pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` manufacturer_name,
		tl.`name` tax_name, t.`rate`, cl.`name` category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
		INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY)) > 0 new
		FROM `' . _DB_PREFIX_ . 'category_product` cp
		LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = cp.`id_product`)
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)$id_lang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_state` = 0)
		LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = ' . (int)$id_lang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
		WHERE p.id_product IN (' . $ids2oneString . ') 
		'.($active?' AND p.active = 1':'').'
		GROUP BY p.id_product');

            if ($orderBy == 'price')
                Tools::orderbyPrice($rq, $orderWay);

            return Product::getProductsProperties((int)$id_lang, $rq);
        return Array();
    }

    public static function getAllPost($id_lang = null, $limit_start, $limit) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if ($limit_start == '')
            $limit_start = 0;
        if ($limit == '')
            $limit = 5;
        $result = array();
        $BlogCategory = '';

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 ORDER BY p.id_smart_blog_post DESC LIMIT ' . $limit_start . ',' . $limit;

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        $BlogCategory = new BlogCategory();
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_smart_blog_post'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['title'] = $post['title'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_name'] = $BlogCategory->getCatName($post['id_category']);
            $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
            $employee = new Employee($post['id_author']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg')) {
                $image = $post['id_smart_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['created'] = $post['created'];
            $i++;
        }
        return $result;
    }

    public static function getToltal($id_lang = null) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return count($posts);
    }

    public static function getToltalByCategory($id_lang = null, $id_category = null) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if ($id_category == null) {
            $id_category = 1;
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 AND p.id_category = ' . $id_category;
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return count($posts);
    }

    public static function addTags($id_lang = null, $id_post, $tag_list, $separator = ',') {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if (!Validate::isUnsignedId($id_lang))
            return false;

        if (!is_array($tag_list))
            $tag_list = array_filter(array_unique(array_map('trim', preg_split('#\\' . $separator . '#', $tag_list, null, PREG_SPLIT_NO_EMPTY))));

        $list = array();
        if (is_array($tag_list))
            foreach ($tag_list as $tag) {
                $id_tag = BlogTag::TagExists($tag, (int) $id_lang);
                if (!$id_tag) {
                    $tag_obj = new BlogTag(null, $tag, (int) $id_lang);
                    if (!Validate::isLoadedObject($tag_obj)) {
                        $tag_obj->name = $tag;
                        $tag_obj->id_lang = (int) $id_lang;
                        $tag_obj->add();
                    }
                    if (!in_array($tag_obj->id, $list))
                        $list[] = $tag_obj->id;
                }
                else {
                    if (!in_array($id_tag, $list))
                        $list[] = $id_tag;
                }
            }
        $data = '';
        foreach ($list as $tag)
            $data .= '(' . (int) $tag . ',' . (int) $id_post . '),';
        $data = rtrim($data, ',');

        return Db::getInstance()->execute('
		INSERT INTO `' . _DB_PREFIX_ . 'smart_blog_post_tag` (`id_tag`, `id_post`)
		VALUES ' . $data);
    }

    public function add($autodate = true, $null_values = false) {
        if (!parent::add($autodate, $null_values))
            return false;
        else if (isset($_POST['products'])) {
            $this->setProductsPostsRelations(Tools::getValue('products'));
            return $this->setProducts(Tools::getValue('products'));
        }
        return true;
    }

    public static function postViewed($id_post) {

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'smart_blog_post as p SET p.viewed = (p.viewed+1) where p.id_smart_blog_post = ' . $id_post;

        return Db::getInstance()->execute($sql);

        return true;
    }

    public function update($null_values = false) {
        // @hook actionObject*UpdateBefore
        Hook::exec('actionObjectUpdateBefore', array('object' => $this));
        Hook::exec('actionObject' . get_class($this) . 'UpdateBefore', array('object' => $this));

        $this->clearCache();

        // Automatically fill dates
        if (array_key_exists('date_upd', $this)) {
            $this->date_upd = date('Y-m-d H:i:s');
            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_upd'] = true;
            }
        }

        // Automatically fill dates
        if (array_key_exists('date_add', $this) && $this->date_add == null) {
            $this->date_add = date('Y-m-d H:i:s');
            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_add'] = true;
            }
        }

        $id_shop_list = Shop::getContextListShopID();
        if (count($this->id_shop_list) > 0) {
            $id_shop_list = $this->id_shop_list;
        }

        if (Shop::checkIdShopDefault($this->def['table']) && !$this->id_shop_default) {
            $this->id_shop_default = (in_array(Configuration::get('PS_SHOP_DEFAULT'), $id_shop_list) == true) ? Configuration::get('PS_SHOP_DEFAULT') : min($id_shop_list);
        }
        // Database update
        if (!$result = Db::getInstance()->update($this->def['table'], $this->getFields(), '`' . pSQL($this->def['primary']) . '` = ' . (int) $this->id, 0, $null_values)) {
            return false;
        }

        // Database insertion for multishop fields related to the object
        if (Shop::isTableAssociated($this->def['table'])) {
            $fields = $this->getFieldsShop();
            $fields[$this->def['primary']] = (int) $this->id;
            if (is_array($this->update_fields)) {
                $update_fields = $this->update_fields;
                $this->update_fields = null;
                $all_fields = $this->getFieldsShop();
                $all_fields[$this->def['primary']] = (int) $this->id;
                $this->update_fields = $update_fields;
            } else {
                $all_fields = $fields;
            }

            foreach ($id_shop_list as $id_shop) {
                $fields['id_shop'] = (int) $id_shop;
                $all_fields['id_shop'] = (int) $id_shop;
                $where = $this->def['primary'] . ' = ' . (int) $this->id . ' AND id_shop = ' . (int) $id_shop;

                // A little explanation of what we do here : we want to create multishop entry when update is called, but
                // only if we are in a shop context (if we are in all context, we just want to update entries that alread exists)
                $shop_exists = Db::getInstance()->getValue('SELECT ' . $this->def['primary'] . ' FROM ' . _DB_PREFIX_ . $this->def['table'] . '_shop WHERE ' . $where);
                if ($shop_exists) {
                    $result &= Db::getInstance()->update($this->def['table'] . '_shop', $fields, $where, 0, $null_values);
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $result &= Db::getInstance()->insert($this->def['table'] . '_shop', $all_fields, $null_values);
                }
            }
        }

        // Database update for multilingual fields related to the object
        if (isset($this->def['multilang']) && $this->def['multilang']) {
            $fields = $this->getFieldsLang();
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    foreach (array_keys($field) as $key) {
                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PrestaShopException('key ' . $key . ' is not a valid table or identifier');
                        }
                    }

                    // If this table is linked to multishop system, update / insert for all shops from context
                    if ($this->isLangMultishop()) {
                        $id_shop_list = Shop::getContextListShopID();
                        if (count($this->id_shop_list) > 0) {
                            $id_shop_list = $this->id_shop_list;
                        }
                        foreach ($id_shop_list as $id_shop) {
                            $field['id_shop'] = (int) $id_shop;
                            $where = pSQL($this->def['primary']) . ' = ' . (int) $this->id
                                    . ' AND id_lang = ' . (int) $field['id_lang']
                                    . ' AND id_shop = ' . (int) $id_shop;

                            if (Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . pSQL(_DB_PREFIX_ . $this->def['table']) . '_lang WHERE ' . $where)) {
                                $result &= Db::getInstance()->update($this->def['table'] . '_lang', $field, $where);
                            } else {
                                $result &= Db::getInstance()->insert($this->def['table'] . '_lang', $field);
                            }
                        }
                    }
                    // If this table is not linked to multishop system ...
                    else {
                        $where = pSQL($this->def['primary']) . ' = ' . (int) $this->id
                                . ' AND id_lang = ' . (int) $field['id_lang'];
                        if (Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . pSQL(_DB_PREFIX_ . $this->def['table']) . '_lang WHERE ' . $where)) {
                            $result &= Db::getInstance()->update($this->def['table'] . '_lang', $field, $where);
                        } else {
                            $result &= Db::getInstance()->insert($this->def['table'] . '_lang', $field, $null_values);
                        }
                    }
                }
            }
        }
		$this->setProductsPostsRelations(Tools::getValue('products'));
		$this->setCategoriesPostsRelations(Tools::getValue('categories'));
		//Так невозможно удалить все продуты / категории
        // if (Tools::getValue('products')) {
            // $this->setProductsPostsRelations(Tools::getValue('products'));
        // }
        // if (Tools::getValue('categories')) {
            // $this->setCategoriesPostsRelations(Tools::getValue('categories'));
        // }
        // @hook actionObject*UpdateAfter
        Hook::exec('actionObjectUpdateAfter', array('object' => $this));
        Hook::exec('actionObject' . get_class($this) . 'UpdateAfter', array('object' => $this));
        return $result;
    }

    public function setProducts($array) {
        $result = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'smart_blog_post_tag WHERE id_tag = ' . (int) $this->id);
        if (is_array($array)) {
            $array = array_map('intval', $array);
            $result &= ObjectModel::updateMultishopTable('smart_blog_post_tag', array('indexed' => 0), 'a.id_post IN (' . implode(',', $array) . ')');
            $ids = array();
            foreach ($array as $id_post)
                $ids[] = '(' . (int) $id_post . ',' . (int) $this->id . ')';

            if ($result) {
                $result &= Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'smart_blog_post_tag (id_post, id_tag) VALUES ' . implode(',', $ids));
                if (Configuration::get('PS_SEARCH_INDEXATION'))
                    $result &= Search::indexation(false);
            }
        }
        return $result;
    }

    /** PW * */
    public function setProductsPostsRelations($array) {

        $result = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'smart_blog_product_related WHERE id_post = ' . (int) $this->id);
        if (is_array($array)) {
            $array = array_map('intval', $array);
            /*  $result &= ObjectModel::updateMultishopTable('smart_blog_product_related', array('indexed' => 0), 'a.id_post IN (' . implode(',', $array) . ')'); */
            $ids = array();
            foreach ($array as $id_product) {
                if (intval($id_product) > 0) {
                    $ids[] = '(' . (int) $id_product . ',' . (int) $this->id . ')';
                }
            }
            $ids = array_unique($ids);
                if ($result) {
                    $result &= Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'smart_blog_product_related (id_product, id_post) VALUES ' . implode(',', $ids));
                    if (Configuration::get('PS_SEARCH_INDEXATION'))
                        $result &= Search::indexation(false);
                }
        }
        return $result;
    }
/*pwIljaAlt*/
    public function setCategoriesPostsRelations($array) {

        $result = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'smart_blog_category_of_product_related WHERE id_post = ' . (int) $this->id);
        if (is_array($array)) {
            $array = array_map('intval', $array);
            /*  $result &= ObjectModel::updateMultishopTable('smart_blog_product_related', array('indexed' => 0), 'a.id_post IN (' . implode(',', $array) . ')'); */
            $ids = array();
            foreach ($array as $id_category) {
                if (intval($id_category)) {
                    $ids[] = '(' . (int) $id_category . ',' . (int) $this->id . ')';
                }
            }
            $ids = array_unique($ids);
                if (count($ids)) {
                    $result &= Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'smart_blog_category_of_product_related (id_category, id_post) VALUES ' . implode(',', $ids));
                    if (Configuration::get('PS_SEARCH_INDEXATION'))
                        $result &= Search::indexation(false);
                }
        }

        return $result;
    }

    public static function deleteTagsForProduct($id_post) {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'smart_blog_post_tag` WHERE `id_post` = ' . (int) $id_post);
    }

    public static function getProductTags($id_post) {
        $id_lang = (int) Context::getContext()->language->id;
        if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT  t.`name`
		FROM ' . _DB_PREFIX_ . 'smart_blog_tag t
		LEFT JOIN ' . _DB_PREFIX_ . 'smart_blog_post_tag pt ON (pt.id_tag = t.id_tag AND t.id_lang = ' . $id_lang . ')
		WHERE pt.`id_post`=' . (int) $id_post))
            return false;
        return $tmp;
    }

    public static function getPostProducts($id_post) {
        if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT pr.id_product 
		FROM  ' . _DB_PREFIX_ . 'smart_blog_product_related pr 
		WHERE pr.`id_post`=' . (int) $id_post))
            return false;		
        return $tmp;
    }

    public static function getPostCategories($id_post) {
        if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT pr.id_category
		FROM  ' . _DB_PREFIX_ . 'smart_blog_category_of_product_related pr
		WHERE pr.`id_post`=' . (int) $id_post))
            return false;
        foreach($tmp  as $category)
            $result[] = $category["id_category"];
        return $result;
    }

    public static function getProductTagsBylang($id_post, $id_lang = null) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        $tags = '';
        if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT  t.`name`
                    FROM ' . _DB_PREFIX_ . 'smart_blog_tag t
                    LEFT JOIN ' . _DB_PREFIX_ . 'smart_blog_post_tag pt ON (pt.id_tag = t.id_tag AND t.id_lang = ' . $id_lang . ')
                    WHERE pt.`id_post`=' . (int) $id_post))
            return false;
        $i = 1;
        foreach ($tmp as $val) {
            if ($i >= count($tmp)) {
                $tags .= $val['name'];
            } else {
                $tags .= $val['name'] . ',';
            }
            $i++;
        }
        return $tags;
    }

    public static function getPopularPosts($id_lang = null) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if (Configuration::get('smartshowpopularpost') != '' && Configuration::get('smartshowpopularpost') != null) {
            $limit = Configuration::get('smartshowpopularpost');
        } else {
            $limit = 5;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT p.viewed ,p.created , p.id_smart_blog_post,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                    ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                    ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                    WHERE pl.id_lang=' . $id_lang . ' AND p.active = 1 ORDER BY p.viewed DESC LIMIT 0,' . $limit);

        return $result;
    }

    public static function getRelatedPosts($id_lang = null, $id_cat = null, $id_post = null) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if (Configuration::get('smartshowrelatedpost') != '' && Configuration::get('smartshowrelatedpost') != null) {
            $limit = Configuration::get('smartshowrelatedpost');
        } else {
            $limit = 5;
        }
        if ($id_cat == null) {
            $id_cat = 1;
        }
        if ($id_post == null) {
            $id_post = 1;
        }
        $sql = 'SELECT  p.id_smart_blog_post,p.created,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '  AND p.active = 1 AND p.id_category = ' . $id_cat . ' AND p.id_smart_blog_post != ' . $id_post . ' ORDER BY p.id_smart_blog_post DESC LIMIT 0,' . $limit;

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return $posts;
    }

    public static function getRecentPosts($id_lang = null) {
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if (Configuration::get('smartshowrecentpost') != '' && Configuration::get('smartshowrecentpost') != null) {
            $limit = Configuration::get('smartshowrecentpost');
        } else {
            $limit = 5;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT  p.id_smart_blog_post,p.created,pl.meta_title,pl.link_rewrite FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . '  AND p.active = 1 ORDER BY p.id_smart_blog_post DESC LIMIT 0,' . $limit);

        return $result;
    }

    public static function tagsPost($tags, $id_lang = null) {
        if ($id_lang == null)
            $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post=ps.id_smart_blog_post  AND  ps.id_shop = ' . (int) Context::getContext()->shop->id . ' INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_tag pt ON pl.id_smart_blog_post = pt.id_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_tag t ON pt.id_tag=t.id_tag 
                WHERE pl.id_lang=' . $id_lang . '  AND p.active = 1 	 		
                AND t.name="' . $tags . '"';

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;

        $BlogCategory = new BlogCategory();
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_smart_blog_post'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['title'] = $post['title'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_name'] = $BlogCategory->getCatName($post['id_category']);
            $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
            $employee = new Employee($post['id_author']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg')) {
                $image = $post['id_smart_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['created'] = $post['created'];
            $i++;
        }
        return $result;
    }

    public static function getArchiveResult($month = null, $year = null, $limit_start = 0, $limit = 5) {
        $BlogCategory = '';
        $result = array();
        $id_lang = (int) Context::getContext()->language->id;
        if ($month != '' and $month != NULL and $year != '' and $year != NULL) {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
            where s.active = 1 and MONTH(s.created) = ' . $month . ' AND YEAR(s.created) = ' . $year . ' ORDER BY s.id_smart_blog_post DESC';
        } elseif ($month == '' and $month == NULL and $year != '' and $year != NULL) {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
           where s.active = 1 AND YEAR(s.created) = ' . $year . ' ORDER BY s.id_smart_blog_post DESC';
        } elseif ($month != '' and $month != NULL and $year == '' and $year == NULL) {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
           where s.active = 1 AND   MONTH(s.created) = ' . $month . '  ORDER BY s.id_smart_blog_post DESC';
        } else {
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post s INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_lang sl ON s.id_smart_blog_post = sl.id_smart_blog_post
                 and sl.id_lang = ' . $id_lang . ' INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON ps.id_smart_blog_post = s.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
            where s.active = 1 ORDER BY s.id_smart_blog_post DESC';
        }
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;

        $BlogCategory = new BlogCategory();
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_smart_blog_post'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['title'] = $post['title'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_name'] = $BlogCategory->getCatName($post['id_category']);
            $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
            $employee = new Employee($post['id_author']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg')) {
                $image = $post['id_smart_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['created'] = $post['created'];
            $i++;
        }
        return $result;
    }

    public static function getArchiveD($month, $year) {

        $sql = 'SELECT DAY(p.created) as day FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                 where MONTH(p.created) = ' . $month . ' AND YEAR(p.created) = ' . $year . ' GROUP BY DAY(p.created)';

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;

        return $posts;
    }

    public static function getArchiveM($year) {

        $sql = 'SELECT MONTH(p.created) as month FROM ' . _DB_PREFIX_ . 'smart_blog_post p  INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                 where YEAR(p.created) = ' . $year . ' GROUP BY MONTH(p.created)';

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return $posts;
    }

    public static function getArchive() {
        $result = array();
        $sql = 'SELECT YEAR(p.created) as year FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON p.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . ' 
                GROUP BY YEAR(p.created)';

        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        $i = 0;
        foreach ($posts as $value) {
            $result[$i]['year'] = $value['year'];
            $result[$i]['month'] = SmartBlogPost::getArchiveM($value['year']);
            $months = SmartBlogPost::getArchiveM($value['year']);
            $j = 0;
            foreach ($months as $month) {
                $result[$i]['month'][$j]['day'] = SmartBlogPost::getArchiveD($month['month'], $value['year']);
                $j++;
            }
            $i++;
        }
        return $result;
    }

    public static function SmartBlogSearchPost($keyword = NULL, $id_lang = NULL, $limit_start = 0, $limit = 5) {
        if ($keyword == NULL)
            return false;
        if ($id_lang == NULL)
            $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post_lang pl, ' . _DB_PREFIX_ . 'smart_blog_post p 
                WHERE pl.id_lang=' . $id_lang . '  AND p.active = 1 
                AND pl.id_smart_blog_post=p.id_smart_blog_post AND
                (pl.meta_title LIKE \'%' . $keyword . '%\' OR
				pl.title LIKE \'%' . $keyword . '%\' OR
                 pl.meta_keyword LIKE \'%' . $keyword . '%\' OR
                 pl.meta_description LIKE \'%' . $keyword . '%\' OR
                 pl.content LIKE \'%' . $keyword . '%\') ORDER BY p.id_smart_blog_post DESC';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;

        $BlogCategory = new BlogCategory();
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_smart_blog_post'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['title'] = $post['title'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_name'] = $BlogCategory->getCatName($post['id_category']);
            $result[$i]['cat_link_rewrite'] = $BlogCategory->getCatLinkRewrite($post['id_category']);
            $employee = new Employee($post['id_author']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg')) {
                $image = $post['id_smart_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['created'] = $post['created'];
            $i++;
        }
        return $result;
    }

    public static function SmartBlogSearchPostCount($keyword = NULL, $id_lang = NULL) {
        if ($keyword == NULL)
            return false;
        if ($id_lang == NULL)
            $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post_lang pl, ' . _DB_PREFIX_ . 'smart_blog_post p 
                WHERE pl.id_lang=' . $id_lang . '
                AND pl.id_smart_blog_post=p.id_smart_blog_post AND p.active = 1 AND 
                (pl.meta_title LIKE \'%' . $keyword . '%\' OR
				pl.title LIKE \'%' . $keyword . '%\' OR
                 pl.meta_keyword LIKE \'%' . $keyword . '%\' OR
                 pl.meta_description LIKE \'%' . $keyword . '%\' OR
                 pl.content LIKE \'%' . $keyword . '%\') ORDER BY p.id_smart_blog_post DESC';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return count($posts);
    }

    public static function getBlogImage() {

        $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT id_smart_blog_post FROM ' . _DB_PREFIX_ . 'smart_blog_post';

        if (!$result = Db::getInstance()->executeS($sql))
            return false;
        return $result;
    }

    public static function GetPostSlugById($id_post, $id_lang = null) {
        if ($id_lang == null)
            $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 AND p.id_smart_blog_post = ' . $id_post;

        if (!$post = Db::getInstance()->executeS($sql))
            return false;

        return $post[0]['link_rewrite'];
    }

    public static function GetPostMetaByPost($id_post, $id_lang = null) {
        if ($id_lang == null)
            $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post 
                WHERE pl.id_lang=' . $id_lang . '
                AND p.active= 1 AND p.id_smart_blog_post = ' . $id_post;

        if (!$post = Db::getInstance()->executeS($sql))
            return false;


        if ($post[0]['meta_title'] == '' || $post[0]['meta_title'] == NULL) {
            $meta['meta_title'] = ($post[0]['title'] ? $post[0]['title'] : Configuration::get('smartblogmetatitle'));
        } else {
            $meta['meta_title'] = $post[0]['meta_title'];
        }

        if ($post[0]['meta_description'] == '' || $post[0]['meta_description'] == NULL) {
            $meta['meta_description'] = Configuration::get('smartblogmetadescrip');
        } else {
            $meta['meta_description'] = $post[0]['meta_description'];
        }

        if ($post[0]['meta_keyword'] == '' || $post[0]['meta_keyword'] == NULL) {
            $meta['meta_keywords'] = Configuration::get('smartblogmetakeyword');
        } else {
            $meta['meta_keywords'] = $post[0]['meta_keyword'];
        }
        return $meta;
    }

    public static function GetPostLatestHome($limit) {
        if ($limit == '' && $limit == null)
            $limit = 3;
        $id_lang = (int) Context::getContext()->language->id;
        $id_lang_defaut = Configuration::get('PS_LANG_DEFAULT');
        $result = array();
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang . ' 		
                AND p.active= 1 ORDER BY p.id_smart_blog_post DESC 
                LIMIT ' . $limit;
        $posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (empty($posts)) {
            $sql2 = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_post p INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_lang pl ON p.id_smart_blog_post=pl.id_smart_blog_post INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop ps ON pl.id_smart_blog_post = ps.id_smart_blog_post  AND ps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE pl.id_lang=' . $id_lang_defaut . ' 		
                AND p.active= 1 ORDER BY p.id_smart_blog_post DESC 
                LIMIT ' . $limit;
            $posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql2);
        }
        $i = 0;
        foreach ($posts as $post) {
            $result[$i]['id'] = $post['id_smart_blog_post'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['title'] = $post['title'];
            $result[$i]['meta_description'] = strip_tags($post['meta_description']);
            $result[$i]['short_description'] = strip_tags($post['short_description']);
            $result[$i]['content'] = strip_tags($post['content']);
            $result[$i]['category'] = $post['id_category'];
            $result[$i]['date_added'] = $post['created'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['is_featured'] = $post['is_featured'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/' . $post['id_smart_blog_post'] . '.jpg')) {
                $image = $post['id_smart_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $i++;
        }
        return $result;
    }

    public static function totranslit($string) {

        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );

        return strtr($string, $converter);
    }

    public static function str2url($str) {
        // переводим в транслит

        $str = self::totranslit($str);

        // в нижний регистр

        $str = strtolower($str);

        // заменям все ненужное нам на "-"

        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);

        // удаляем начальные и конечные '-'

        $str = trim($str, "-");

        return $str;
    }

}
