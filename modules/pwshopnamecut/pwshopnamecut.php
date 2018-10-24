<?php
/**
* 2013-2016 PrestaWeb
*
*  @author http://prestaweb.ru/ <contact@prestaweb.ru>
*  @copyright  2013-2016 PrestaShop SA
*/
if (!defined('_PS_VERSION_')){
    exit;
}

class pwShopnamecut extends Module
{

    public function __construct()
    {
        $this->name = 'pwshopnamecut';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->description = $this->l('Hiding the name of the site in a meta tag title');
        $this->displayName = $this->l('Hiding the name of the site in a meta tag title');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('header');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookDisplayHeader()
    {
        $title = $this->context->smarty->getVariable('meta_title')->value;

        $match = '/(\s*-?[^-]*'.Configuration::get('PS_SHOP_NAME').')/';

        $tmp_name = preg_replace($match, '', $title);

        if (!empty($tmp_name)) {
            $this->context->smarty->assign('meta_title', $tmp_name);
        }
    }
}
