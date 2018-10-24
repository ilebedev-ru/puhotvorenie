<?php

class AdminPwCatSeoController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table          = 'pwcatseo';
        $this->className      = 'Catseo';
        $this->module         = 'pwcatseo';
        $this->lang           = true;
        $this->bootstrap      = true;
        $this->context        = Context::getContext();

        parent::__construct();
        
        // $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->show_toolbar = true;
        $this->token = Tools::getAdminTokenLite('AdminPwCatSeo');
        
        $this->_select .= "cat.name";
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'category_lang` AS cat ON (cat.`id_category` = a.`id_category` AND cat.id_lang = '.$this->context->language->id.') ';

        $this->fields_list = array(
            'id_pwcatseo' => array(
                'title' => $this->l('ID'),
                'type'  => 'text',
            ),
            'name' => array(
                'title' => $this->l('Категория'),
                'type'  => 'text',
            ),
            'title' => array(
                'title' => $this->l('Заголовок'),
                'type'  => 'text',
            ),
            'text' => array(
                'title' => $this->l('Описание'),
                'type' => 'text',
            ),
        );
    }
}