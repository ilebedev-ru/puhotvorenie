<?php
class News extends ObjectModel
{
	public $name;
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $description_short;
	public $content;
	public $link_rewrite;
    public $status = true;
    public $date_add;
    public $date_upd;
    public $id_employee;
	
    public $id_category_default;
	public $product_list;

 	protected $fieldsRequiredLang = array(
 	    'name', 
 	    'link_rewrite'
 	);
 	
	protected $fieldsSizeLang = array(
	    'meta_description'  => 255, 
	    'meta_keywords'     => 255, 
		'name'        => 128, 
	    'meta_title'        => 255, 
	    'link_rewrite'      => 128, 
	    'description_short' => 65536, 
	    'content'           => 65536
	);
	
	protected $fieldsValidate = array(
	    'id_employee'   => 'isInt', 
		'product_list'  => 'isString', 
	    'status'        => 'isBool'
	);
	
	protected $fieldsValidateLang = array(
		'name'        => 'isGenericName', 
	    'meta_description'  => 'isGenericName', 
	    'meta_keywords'     => 'isGenericName', 
	    'meta_title'        => 'isGenericName', 
	    'link_rewrite'      => 'isLinkRewrite', 
	    'description_short' => 'isCleanHTML', 
	    'content'           => 'isCleanHTML'
	);

	protected $table = 'news';
	protected $identifier = 'id_entry';
	
	public function getFields() { 
	    global $cookie;
	    parent::validateFields();
		$fields['id_employee'] = intval($cookie->id_employee);
		$fields['status'] = intval($this->status);
		$fields['product_list'] = pSQL($this->product_list);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		
		if(Module::isInstalled('newscategoriesmod')) {
		    $fields['id_category_default'] = intval($this->id_category_default);
		}
		
		return $fields; 
	}
	
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();

		$fieldsArray = array('name', 'meta_title', 'meta_description', 'meta_keywords', 'link_rewrite');
		$fields = array();
		$languages = Language::getLanguages();
		$defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
			$fields[$language['id_lang']][$this->identifier] = intval($this->id);
			$fields[$language['id_lang']]['description_short'] = (isset($this->description_short[$language['id_lang']])) ? Tools::htmlentitiesDecodeUTF8(pSQL($this->description_short[$language['id_lang']], true)) : '';
			$fields[$language['id_lang']]['content'] = (isset($this->content[$language['id_lang']])) ? Tools::htmlentitiesDecodeUTF8(pSQL($this->content[$language['id_lang']], true)) : '';
			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']]);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
				else
					$fields[$language['id_lang']][$field] = '';
			}
		}
		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false) { 
	    if(parent::add($autodate, true)) {
	        if(Module::isInstalled('newscategoriesmod')) {
                $_POST['id_category'][] = $_POST['id_category_default'];
                $_POST['id_category'] = array_unique($_POST['id_category']);
                foreach($_POST['id_category'] as $category_id) {
                    $sql = '
                        INSERT INTO `' . _DB_PREFIX_ . 'newstocategories` VALUES (
                        ' . intval($category_id) . ',
                        ' . $this->id . ')';
                    
                    if(!Db::getInstance()->Execute($sql)) {
                        return false;
                    }
                }
	        }
	        return true;
	    }
	    return false;
	}
	
	public function delete()
	{
	    if(parent::delete()) {
	        $sql = '
	        DELETE FROM `ps_news` 
	        WHERE `id_entry` = ' . $this->id;
	        if(Db::getInstance()->Execute($sql)) {
	            return true;
	        }
	    }
	    return false;
	}
	public function toggleStatus()
	{
	 	if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table)) {
	 		die(Tools::displayError());
	    } elseif (!key_exists('status', $this)) {
	 		die(Tools::displayError());
	 	}
		return Db::getInstance()->Execute('
		UPDATE `'.pSQL(_DB_PREFIX_.$this->table).'`
		SET `status` = !`status`
		WHERE `'.pSQL($this->identifier).'` = '.intval($this->id));
	}
	
	public function update($nullValues = false)
	{
	 	if(parent::update($nullValues)) {
	        if(Module::isInstalled('newscategoriesmod')) {
                $sql = '
                DELETE FROM `' . _DB_PREFIX_ . 'newstocategories` 
                WHERE `id_entry` = ' . intval($this->id);
                
                if(!Db::getInstance()->Execute($sql)) {
                    return false;
                }

                $_POST['id_category'][] = $_POST['id_category_default'];
                $_POST['id_category'] = array_unique($_POST['id_category']);
                
                foreach($_POST['id_category'] as $category_id) {
                    $sql = '
                    INSERT INTO `' . _DB_PREFIX_ . 'newstocategories` VALUES (
                    ' . intval($category_id) . ',
                    ' . $this->id . ')';

                    if(!Db::getInstance()->Execute($sql)) {
                        return false;
                    }
                }
	        }
	        
	        return true;
	    }
	    return false;
	}
	
    public function getEntryPreview($id_lang, $entry_id, $category_id = null)
    {
        if(!Validate::isUnsignedInt($entry_id) 
            OR !Validate::isUnsignedInt($id_lang) 
            OR ($category_id AND !Validate::isUnsignedInt($category_id))) {
            return false;
        }
        
        if($category_id) {
            $sql = '
            SELECT nw. * , nwl. * , ncl.`meta_title` AS  `category_name` , ncl.`link_rewrite` AS  `category_link` 
            FROM  `' . _DB_PREFIX_ . 'newstocategories` ntc
            LEFT JOIN  `' . _DB_PREFIX_ . 'news` nw ON ( nw.`id_entry` = ntc.`id_entry` ) 
            LEFT JOIN  `' . _DB_PREFIX_ . 'news_lang` nwl ON ( nw.`id_entry` = nwl.`id_entry` ) 
            LEFT JOIN  `' . _DB_PREFIX_ . 'newscategories` nc ON ( nc.`id_category` = ntc.`id_category` ) 
            LEFT JOIN  `' . _DB_PREFIX_ . 'newscategories_lang` ncl ON ( nc.`id_category` = ncl.`id_category` ) 
            WHERE ntc.`id_category` = ' . intval($category_id) . '
            AND nc.`status` = 1
            AND nw.`status` = 1
            AND nw.`id_entry` = ' . intval($entry_id) . '
            AND nwl.`id_lang` = ' . intval($id_lang) . '
            AND ncl.`id_lang` = ' . intval($id_lang);
        } else {
            $sql = '
            SELECT 
            nw.*, nwl.* 
            FROM `' . _DB_PREFIX_ . 'news` nw
            LEFT JOIN `' . _DB_PREFIX_ . 'news_lang` nwl 
            ON (nw.`id_entry` = nwl.`id_entry`) 
            WHERE nw.`id_entry` = ' . intval($entry_id) . ' 
            AND nw.`status` = 1
            AND nwl.`id_lang` = ' . intval($id_lang);
        }

        return Db::getInstance()->getRow($sql);
    }

	
	public function getEntryById($category_id = null)
    {
        global $cookie;
        if(!$this->id OR ($category_id AND !Validate::isUnsignedId($category_id))) {
            return false;
        }
        
        if($category_id) {
            $sql = '
            SELECT nw. * , nwl. * , ncl.`meta_title` AS  `category_name` , ncl.`link_rewrite` AS  `category_link` 
            FROM  `' . _DB_PREFIX_ . 'newstocategories` ntc
            LEFT JOIN  `' . _DB_PREFIX_ . 'news` nw ON ( nw.`id_entry` = ntc.`id_entry` ) 
            LEFT JOIN  `' . _DB_PREFIX_ . 'news_lang` nwl ON ( nw.`id_entry` = nwl.`id_entry` ) 
            LEFT JOIN  `' . _DB_PREFIX_ . 'newscategories` nc ON ( nc.`id_category` = ntc.`id_category` ) 
            LEFT JOIN  `' . _DB_PREFIX_ . 'newscategories_lang` ncl ON ( nc.`id_category` = ncl.`id_category` ) 
            WHERE ntc.`id_category` = ' . intval($category_id) . '
            AND nc.`status` = 1
            AND nw.`status` = 1
            AND nw.`id_entry` = ' . $this->id . '
            AND nwl.`id_lang` = ' . intval($cookie->id_lang) . '
            AND ncl.`id_lang` = ' . intval($cookie->id_lang);
        } else {
            $sql = '
            SELECT 
            nw.*, nwl.* 
            FROM `' . _DB_PREFIX_ . 'news` nw
            LEFT JOIN `' . _DB_PREFIX_ . 'news_lang` nwl 
            ON (nw.`id_entry` = nwl.`id_entry`) 
            WHERE nw.`id_entry` = ' . $this->id . ' 
            AND nw.`status` = 1
            AND nwl.`id_lang` = ' . intval($cookie->id_lang);
        }

        return Db::getInstance()->getRow($sql);
    }
    
    static public function getLink($params)
	{
		global $cookie;
	    $link = false;
		$page = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT ml.url_rewrite FROM `'._DB_PREFIX_.'meta` m, `'._DB_PREFIX_.'meta_lang` ml WHERE m.page = "news" AND m.id_meta = ml.id_meta AND id_lang='.$cookie->id_lang);
	    $template = array('info', 'entry', 'category');
		$template[0] = ($page && $page != "page-not-found" ? $page : 'info');
	    $tmpLink = "/";
	    $rewrite = intval(Configuration::get('PS_REWRITING_SETTINGS')) == 1 ? true : false;
	    if(isset($params['category'])) {
	        if(!isset($params['entry'])) {
	            $link = $tmpLink . ($rewrite ? $template[0] . '/' . $template[2] . '/' . $params['category']['id'] . '-' . $params['category']['rewrite'] . '.html' : 'modules/newscore/news.php?category_id=' . $params['category']['id']);
	        } else {
	            $link = $tmpLink . ($rewrite ? $template[0]. "/". $template[1] . '/' . $params['entry']['id'] . '-' . $params['entry']['rewrite'] . '.html' : 'modules/newscore/news.php?category_id=' . $params['category']['id'] . '&id_entry=' . $params['entry']['id']);
	        }
	    } elseif(isset($params['entry'])) {
	        $link = $tmpLink . ($rewrite ? $template[0] . '/' . $template[1] . '/' . $params['entry']['id'] . '-' . $params['entry']['rewrite'] . '.html' : 'modules/newscore/news.php?id_entry=' . $params['entry']['id']);
	    }
	    return $link;
	}
    
    public function getNews($p = false, $n = false)
    {
        global $cookie;
        $rewrite = intval(Configuration::get('PS_REWRITING_SETTINGS'));
        $tmpLink = _PS_BASE_URL_ . __PS_BASE_URI__;
		if(Module::isInstalled('newscategoriesmod')){
        $sql = '
        SELECT 
        nw.*, 
        nwl.`link_rewrite`, 
        nwl.`meta_title`, 
		nwl.`name`, 
        nwl.`description_short`,
		nwcl.`meta_title` as categoryName
        FROM `' . _DB_PREFIX_ . 'news` nw
        LEFT JOIN `' . _DB_PREFIX_ . 'news_lang` nwl ON (nw.`id_entry` = nwl.`id_entry`) 
		LEFT JOIN `' . _DB_PREFIX_ . 'newscategories_lang` nwcl  ON (nwcl.`id_category` = nw.`id_category_default`) 
        WHERE nwl.`id_lang` = ' . intval($cookie->id_lang) . ' 
        AND nwl.`meta_title` != ""
        AND nw.`status` = 1
        ORDER BY nw.`date_add` DESC';
		}else{
			$sql = '
			SELECT 
			nw.*, 
			nwl.`link_rewrite`, 
			nwl.`meta_title`, 
			nwl.`name`, 
			nwl.`description_short`
			FROM `' . _DB_PREFIX_ . 'news` nw
			LEFT JOIN `' . _DB_PREFIX_ . 'news_lang` nwl ON (nw.`id_entry` = nwl.`id_entry`) 
			WHERE nwl.`id_lang` = ' . intval($cookie->id_lang) . ' 
			AND nwl.`meta_title` != ""
			AND nw.`status` = 1
			ORDER BY nw.`date_add` DESC';
		}

        if($p AND $n) {
            $sql .= ' LIMIT ' . ((intval($p) - 1) * intval($n)) . ',' . intval($n);
        } elseif ($p) {
            $sql .= ' LIMIT ' . (intval($p));
        }

        $result = Db::getInstance()->ExecuteS($sql);
        $links = array();

        if ($result)
        {
            foreach ($result as $row) 
            {
                $row['link'] = self::getLink(array('entry' => array('id' => $row['id_entry'], 'rewrite' => $row['link_rewrite'])));
				if(is_file(_PS_IMG_DIR_."preview/".$row['id_entry'].".jpg"))  $row['image'] = "/img/preview/".$row['id_entry'].".jpg";
                $links[] = $row;
            }
            return $links;
        }

        return false;
    }
	
}

?>
