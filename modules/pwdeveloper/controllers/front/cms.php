<?php
include_once('PWModuleFrontController.php');
class PwdeveloperCMSModuleFrontController extends PWModuleFrontController {
	
    public function initContent() 
	{
        parent::initContent();
		$this->setTemplate('cms.tpl');
    }
	
	public function postProcess()
	{
		require_once(__DIR__.'/../../lib/PWTools.php');
		if(Tools::isSubmit('submitList')){
            $cmslist = Tools::getValue('cmslist');
            $arr = explode(",", $cmslist);
            if(count($arr)<2) $arr = explode("\n", $cmslist);
            $i = 0;
            $links = "<ul>";
            foreach($arr as $row){
                $row = trim($row);
                if(strlen($row)){
                    $cms = new CMS();
                    $cms->meta_title = PWTools::createMultiLangField($row);
                    $cms->link_rewrite = PWTools::createMultiLangField(Tools::link_rewrite($row));
                    $cms->content = PWTools::createMultiLangField("Редактировать в Админ-панели -> Настройки -> Страницы");
                    $cms->id_cms_category = 1;
                    $cms->active = 1;
                    if($cms->add()) $i++;
                    $links.= '<li><a href="'.$this->context->link->getCMSLink($cms->id, $cms->link_rewrite[$this->context->language->id]).'">'.$row.'</a>'."\n";
                }
            }
            echo '<p class="success">'.$i.' добавлено</p>';
            echo '<pre>'.htmlentities($links.'</ul>', ENT_COMPAT | ENT_HTML401, 'UTF-8').'</pre>';
        }
	}
 
}