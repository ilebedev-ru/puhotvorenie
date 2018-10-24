<?php

class AdvancedModuleFrontController extends ModuleFrontController {
	
    protected $templateFinder = null;
    /*1.7 support*/
    public function getTemplateFinder()
    {
        if (!$this->templateFinder) {
            $this->context->smarty->addTemplateDir(_PS_MODULE_DIR_.$this->module->name.'/views/templates/front/');
            $this->templateFinder = new TemplateFinder($this->context->smarty->getTemplateDir(), '.tpl');
            smartyRegisterFunction($this->context->smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
            $this->context->smarty->assign(array(
                'tpl_dir' => _PS_MODULE_DIR_.$this->module->name.'/views/templates/retrocomp/',
                'use_taxes' => (bool)Configuration::get('PS_TAX'),
                'link' => $this->context->link,
            ));
        }
        return $this->templateFinder;
    }
 
}