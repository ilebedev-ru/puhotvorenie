<?php

if (!defined('_PS_VERSION_'))
    exit;
//start_class
require_once dirname(__FILE__).'/../../classes/blocktestClass.php';
//end_class
class blocktestPageModuleFrontController extends ModuleFrontController
{
    public $module;


    public function initContent()
    {
        //start_class
        $this->context->smarty->assign(Array(
            'list' => blocktestClass::getList()
        ));
        //end_class
        parent::initContent();
        $this->setTemplate('page.tpl');
    }

    public function postProcess()
    {
        /**
         * Для POST запросов
         */
    }


}
