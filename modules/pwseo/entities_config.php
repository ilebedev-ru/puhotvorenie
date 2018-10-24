<?php
$result = Array(
	'product' => Array(
		'table'=>'product_lang',
		'class' => 'Product',
		'fields' => Array(
			'title' => Array('alias_for' => 'meta_title'),
			'meta_title' => Array('name' => 'Заголовок страницы', 'lang' => true, 'type' => 'meta'),
			'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
			'meta_keywords' => Array('name' => 'Мета Мета-тег keywords', 'lang' => true, 'type' => 'meta'),
			'name' => Array('name' => 'Название товара', 'lang' => true, 'type' => 'meta'),
			'description' => Array('name' => 'Описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea')
		)
	),
    'smartblog-details-module-front' => Array(
        'table'=>'smart_blog_post_lang',
        'id' => 'post',
        'class' => 'SmartBlogPost',
        'require' => _PS_MODULE_DIR_.'smartblog/classes/SmartBlogPost.php',
        'fields' => Array(
            'meta_title' => Array('name' => 'Заголовок страницы', 'lang' => true, 'type' => 'meta'),
            'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
            'meta_keyword' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta'), //meta_keyword вместо meta_keywords
            'title' => Array('name' => 'Название статьи', 'lang' => true, 'type' => 'meta')
        )
    ),
	'category' => Array(
        'category' => Array(
            'table' => 'category_lang',
            'class' => 'Category',
            'fields' => Array(
                'meta_title' => Array('name' => 'Заголовок страницы', 'lang' => true, 'type' => 'meta'),
                'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
                'meta_keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta'),
                'name' => Array('name' => 'Имя', 'lang' => true, 'type' => 'meta'),
                'description' => Array('name' => 'Описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea')
            )
        ),
    ),
	'cmspage' => Array(
	   'table' => 'cms_lang',
       'class'=> 'CMS',
       'id' => 'cms',
       'fields' => Array(
           'title' => Array('alias_for' => 'meta_title'),
           'meta_title' => Array('name' => 'Заголовок страницы и название', 'lang' => true, 'type' => 'meta'),
           'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
           'meta_keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta'),
           'content' => Array('name' => 'Описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea')
       )
	),
	'manufacturer' => Array(
	    'table' => 'manufacturer',
        'class'=> 'Manufacturer',
        'id' => 'manufacturer',
        'fields' => Array(
            'title' => Array('alias_for' => 'meta_title'),
            'meta_title' => Array('name' => 'Заголовок страницы', 'lang' => true, 'type' => 'meta'),
            'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
            'meta_keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta'),
            'name' => Array('name' => 'Имя', 'lang' => false, 'type' => 'meta'),
            'description' => Array('name' => 'Описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea'),
            'short_description' => Array('name' => 'Короткое описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea')
        )
	),
	'supplier' => Array(
	    'table' => 'supplier',
        'class'=> 'Supplier',
        'id' => 'supplier',
        'fields' => Array(
            'title' => Array('alias_for' => 'meta_title'),
            'meta_title' => Array('name' => 'Заголовок страницы', 'lang' => true, 'type' => 'meta'),
            'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
            'meta_keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta'),
            'name' => Array('name' => 'Имя', 'lang' => false, 'type' => 'meta'),
            'description' => Array('name' => 'Описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea'),
        )
	),
	'cmscategory' => Array(
		'table' => 'cms_category_lang',
		'id' => 'cms_category',
		'class' => 'CMSCategory',
		'fields' => Array(
			'title' => Array('alias_for' => 'meta_title'),
			'meta_title' => Array('name' => 'Заголовок страницы и название', 'lang' => true, 'type' => 'meta'),
			'meta_description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta', 'size'=> 'textarea'),
			'meta_keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta'),
			'name' => Array('name' => 'Имя', 'lang' => true, 'type' => 'meta'),
            'description' => Array('name' => 'Описание', 'lang' => true, 'type' => 'description', 'size'=> 'textarea')
		)
	),
	'other' => Array(
		'table' => 'meta_lang',
		'id' => 'meta',
		'class' => 'Meta',
		'fields' => Array(
			'meta_keywords' => array('alias_for' => 'keywords'),
			'meta_title' => array('alias_for' => 'title'),
			'meta_description' => array('alias_for' => 'description'),
			'title' => Array('name' => 'Заголовок страницы и название', 'lang' => true, 'type' => 'meta'),
			'description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta'),
			'keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta')
		)
	),
    'index' => array(
        'meta' => array(
            'table' => 'meta_lang',
            'id' => 'meta',
            'class' => 'Meta',
            'fields' => Array(
                'meta_keywords' => array('alias_for' => 'keywords'),
                'meta_title' => array('alias_for' => 'title'),
                'meta_description' => array('alias_for' => 'description'),
                'title' => Array('name' => 'Заголовок страницы и название', 'lang' => true, 'type' => 'meta'),
                'description' => Array('name' => 'Мета-тег descriprion', 'lang' => true, 'type' => 'meta'),
                'keywords' => Array('name' => 'Мета-тег keywords', 'lang' => true, 'type' => 'meta')
            )
        ),
    ),
);
if(Module::isInstalled('pwcatseo') && version_compare(Module::getInstanceByName('pwcatseo')->version, 0.5, '>=')){
    $pwseo = Catseo::getInstanceByCategory(Tools::getValue('id_category'));
    if (empty($pwseo->id) && Tools::getValue('id_category')) {
        $pwseo->id_category = Tools::getValue('id_category');
        $pwseo->add();
    }
    $result['category']['pwcatseo'] = array(
        'table' => 'catseo_lang',
		'id' => 'category',
        'class' => 'Catseo',
        'default_id' => $pwseo->id,
		'fields' => array(
			'title' => array('name' => 'h1', 'lang' => true, 'type' => 'meta'),
			'text' => array('name' => 'Еще один текст', 'lang' => true, 'type' => 'description', 'size' => 'textarea'),
		)
    );
}
if(Module::isInstalled('editorial')){
    Module::getInstanceByName('editorial'); //не убирай
    $editorial = EditorialClass::getByIdShop(Context::getContext()->shop->id);
    if (!Validate::isLoadedObject($editorial)) {
        $editorial->id_shop = Context::getContext()->shop->id;
        $editorial->add();
    }
    $result['index']['editorial'] = Array(
        'table' => 'editorial_lang',
		'id' => 'editorial',
        'class' => 'EditorialClass',
        'default_id' => $editorial->id,
		'fields' => array(
			'body_title' => array('name' => 'Заголовок', 'lang' => true, 'type' => 'meta'),
			'body_subheading' => array('name' => 'Подзаголовок', 'lang' => true, 'type' => 'meta'),
			'body_paragraph' => array('name' => 'Описание' ,'lang' => true, 'type' => 'description', 'size' => 'textarea'),
		)
    );
}
return $result;