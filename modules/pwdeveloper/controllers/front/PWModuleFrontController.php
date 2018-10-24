<?php
set_time_limit(360);
include_once(_PS_MODULE_DIR_.'/pwdeveloper/lib/PWTools.php');

class PWModuleFrontController extends ModuleFrontController {
	
	public $errors = array();
	public $path;
    protected $templateFinder = null;

    public function initContent() 
	{
		$cookie = PWTools::getAdminCookie();
        if(!$cookie->id_employee){
			return Tools::display404Error();
		}
		$this->display_header = false;
		$this->display_footer = false;
		$this->display_column_left = false;
		$this->display_column_right = false;
        parent::initContent();
		$this->path = _MODULE_DIR_.'pwdeveloper/';
    }
    
    /*1.7 support*/
    public function getTemplateFinder()
    {
        if (!$this->templateFinder) {
            $this->context->smarty->addTemplateDir(_PS_MODULE_DIR_.'pwdeveloper/views/templates/front/');
            $this->templateFinder = new TemplateFinder($this->context->smarty->getTemplateDir(), '.tpl');
        }
        return $this->templateFinder;
    }
 
}