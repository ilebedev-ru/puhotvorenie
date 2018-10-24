<?php

include_once(dirname(__FILE__) . '/../../classes/controllers/FrontController.php');

class smartblogCategoryModuleFrontController extends smartblogModuleFrontController
{
    public $ssl = true;
    public $smartblogCategory;

     protected function canonicalRedirection($canonical_url = '')
     {
         if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET' || Tools::getValue('live_edit')) {
             return;
         }

         $match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
         if (!preg_match('/^'.Tools::pRegexp(rawurldecode($canonical_url), '/').'([&?].*)?$/', $match_url)) {
             $params = array();
             $url_details = parse_url($canonical_url);

             if (!empty($url_details['query'])) {
                 parse_str($url_details['query'], $query);
                 foreach ($query as $key => $value) {
                     $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                 }
             }
             $excluded_key = array('isolang', 'id_lang', 'controller', 'fc', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier', 'id_cms', 'id_post', 'module', 'page');
             foreach ($_GET as $key => $value) {
                 if (!in_array($key, $excluded_key) && Validate::isUrl($key) && Validate::isUrl($value)) {
                     $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                 }
             }

             $str_params = http_build_query($params, '', '&');
             if (!empty($str_params)) {
                 $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url).'?'.$str_params;
             } else {
                 $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url);
             }

             // Don't send any cookie
             Context::getContext()->cookie->disallowWriting();

             if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__) {
                 die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$final_url.'">'.$final_url.'</a>');
             }

             $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
             header('HTTP/1.0 '.$redirect_type.' Moved');
             header('Cache-Control: no-cache');
             Tools::redirectLink($final_url);
         }
     }

    public function init()
    {
        parent::init();
        $id_category = Tools::getValue('id_category', 1); //если id_category не указан, то 1
        // if(empty($id_category))
            // return;
        
        $link_rewrite = BlogCategory::getCatLinkRewrite($id_category); //получаем link_rewrite
        $options = Array('id_category'=>$id_category, 'slug' => $link_rewrite);
        $controller = 'smartblog_category';
        if(Tools::getValue('page') > 1){
            $options['page'] = (int) Tools::getValue('page');
            $controller = 'smartblog_category_pagination'; //отдельный контроллер для пагинации
        }
        $url = null;
        if($id_category == 1 && (Tools::getValue('page', 1) == 1)){ //если это первая страница первой категории
            $url = smartblog::GetSmartBlogLink('smartblog');
        }elseif($id_category != 1){
            $url = smartblog::GetSmartBlogLink($controller,$options); //иначе
        }
        $this->canonicalRedirection($url);
    }

    public function initContent()
    {
        parent::initContent();
        $category_status = '';
        $totalpages = '';
        $cat_image = 'no';
        $categoryinfo = '';
        $title_category = '';
        $cat_link_rewrite = '';
        $blogcomment = new Blogcomment();
        $SmartBlogPost = new SmartBlogPost();
        $BlogCategory = new BlogCategory();
        $BlogPostCategory = new BlogPostCategory();
        $posts_per_page = Configuration::get('smartpostperpage');
        $limit_start = 0;
        $limit = $posts_per_page;
        if (!$id_category = Tools::getvalue('id_category')) {
            $total = $SmartBlogPost->getToltal($this->context->language->id);
        } else {
            $total = $SmartBlogPost->getToltalByCategory($this->context->language->id, $id_category);
            Hook::exec('actionsbcat', array('id_category' => Tools::getvalue('id_category')));
        }
        if ($total != '' || $total != 0)
            $totalpages = ceil($total / $posts_per_page);
        if ((boolean)Tools::getValue('page')) {
            $c = Tools::getValue('page');
            $limit_start = $posts_per_page * ($c - 1);
        }
        if (!$id_category = Tools::getvalue('id_category')) {
            $allNews = $SmartBlogPost->getAllPost($this->context->language->id, $limit_start, $limit);
        } else {
            if (file_exists(_PS_MODULE_DIR_ . 'smartblog/images/category/' . $id_category . '.jpg')) {
                $cat_image = $id_category;
            } else {
                $cat_image = 'no';
            }
            $categoryinfo = $BlogCategory->getNameCategory($id_category);
            $title_category = $categoryinfo['meta_title'];

            $category_status = $categoryinfo['active'];
            $cat_link_rewrite = $categoryinfo['link_rewrite'];
            if ($category_status == 1) {
                $allNews = $BlogPostCategory->getToltalByCategory($this->context->language->id, $id_category, $limit_start, $limit);
            } elseif ($category_status == 0) {
                $allNews = '';
            }
        }
        if(!$allNews && Tools::getValue('page')>1){
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
        }

        $i = 0;
        if (!empty($allNews)) {
            foreach ($allNews as $item) {
                $to[$i] = $blogcomment->getToltalComment($item['id_post']);
                $i++;
            }
            $j = 0;
            foreach ($to as $item) {
                if ($item == '') {
                    $allNews[$j]['totalcomment'] = 0;
                } else {
                    $allNews[$j]['totalcomment'] = $item;
                }
                $j++;
            }
        }

        $this->context->smarty->assign(array(
            'postcategory' => $allNews,
            'category_status' => $category_status,
            'title_category' => $title_category,
            'cat_link_rewrite' => $cat_link_rewrite,
            'id_category' => $id_category,
            'cat_image' => $cat_image,
            'categoryinfo' => $categoryinfo,
            'smartshowauthorstyle' => Configuration::get('smartshowauthorstyle'),
            'smartshowauthor' => Configuration::get('smartshowauthor'),
            'limit' => isset($limit) ? $limit : 0,
            'limit_start' => isset($limit_start) ? $limit_start : 0,
            'c' => isset($c) ? $c : 1,
            'total' => $total,
            'smartblogliststyle' => Configuration::get('smartblogliststyle'),
            'smartcustomcss' => Configuration::get('smartcustomcss'),
            'smartshownoimg' => Configuration::get('smartshownoimg'),
            'smartdisablecatimg' => Configuration::get('smartdisablecatimg'),
            'smartshowviewed' => Configuration::get('smartshowviewed'),
            'post_per_page' => $posts_per_page,
            'pagenums' => $totalpages - 1,
            'totalpages' => $totalpages
        ));

        $template_name = 'postcategory.tpl';

        $this->setTemplate($template_name);
    }
}