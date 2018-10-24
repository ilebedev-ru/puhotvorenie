<?php
if (!defined('_PS_VERSION_'))
    exit;

class opt extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Оптовые покупки");
        $this->description = $this->l("Оптовые покупки");
        //start_controller
        $this->controllers = array('opt');
        //end_controller
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if ( !parent::install() 
            
        ) return false;

        return true;
    }

    
    

    

    



}


