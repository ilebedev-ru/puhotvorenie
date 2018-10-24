<?php
if (!defined('_PS_VERSION_'))
    exit;

class pwcontacts extends Module
{
    public function __construct()
    {
        $this->name = 'pwcontacts';
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = "Блок контактов";
        $this->description = "Контакты берутся из настроек магазина";
    }


    public function install()
    {
        return (parent::install() AND $this->registerHook('header') AND $this->registerHook('displayTop')
        AND $this->registerHook('displayHeaderLeft'));
    }

    public function hookheader($params)
    {
        $this->context->controller->addCSS($this->_path . $this->name . '.css', 'all');
    }
    public function hookdisplayHeaderLeft($params)
    {
        return $this->hookdisplayTop($params);
    }

    public function hookdisplayTop($params)
    {
        return $this->display(__FILE__, 'pwcontacts.tpl');
    }



}


