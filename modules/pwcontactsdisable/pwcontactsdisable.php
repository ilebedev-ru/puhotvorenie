<?php

class pwcontactsdisable extends Module {
    public function __construct() {
        $this->name = 'pwcontactsdisable';
        $this->tab = 'other';
        $this->version = 0.2;
        $this->author = 'PrestaWeb.ru';

        parent::__construct();

        $this->displayName = $this->l("Отключение страницы контактов");
        $this->description = $this->l("Отключение страницы контактов");
		
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }

    public function install() {
        return parent::install() && $this->registerHook('actionDispatcher');
    }

    // hook runs just after controller has been instantiated
    public function hookActionDispatcher($params) {
        if ($params['controller_type'] === 1 && $params['controller_class'] === 'ContactController') {
            Tools::redirect('pagenotfound'); // redirect contact page to 404 page
        }
    }
}
?>