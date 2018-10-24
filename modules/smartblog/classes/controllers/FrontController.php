<?php

class smartblogModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($id_category = Tools::getvalue('id_category') && Tools::getvalue('id_category') != Null) {
            $this->context->smarty->assign(smartblog::getMeta(Tools::getvalue('id_category')));
        }
        elseif ($id_post = Tools::getvalue('id_post') && Tools::getvalue('id_post') != Null) {
            $meta = SmartBlogPost::GetPostMetaByPost(Tools::getvalue('id_post'));
            $this->context->smarty->assign($meta);
        }
        else{
            $this->context->smarty->assign(smartblog::getMeta());
        }
        if (Configuration::get('smartshowcolumn') == 0) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
            ));
        } elseif (Configuration::get('smartshowcolumn') == 1) {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft')
            ));
        } elseif (Configuration::get('smartshowcolumn') == 2) {

            $this->context->smarty->assign(array(
                'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
            ));
        } elseif (Configuration::get('smartshowcolumn') == 3) {
            $this->context->smarty->assign(array());
        } else {
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => Hook::exec('displaySmartBlogLeft'),
                'HOOK_RIGHT_COLUMN' => Hook::exec('displaySmartBlogRight')
            ));
        }
    }
}