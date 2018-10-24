<?php
include_once(_PS_MODULE_DIR_.'/pwdeveloper/lib/PWTools.php');
include_once('PWModuleFrontController.php');
class PwdeveloperDefaultModuleFrontController extends PWModuleFrontController {
   
    public $errors = array();
   
    public function init()
    {
        $cookie = PWTools::getAdminCookie();
        if(!$cookie->id_employee){
			return Tools::display404Error();
		}
        parent::init();
    }
   
    public function initContent() 
    {
        parent::initContent();
        $controllers = $this->module->pwcontrollers;
        $this->context->smarty->assign('controllers', $controllers);
        $this->setTemplate('default.tpl');
    }
}