<?php
include_once('PWModuleFrontController.php');
class PwdeveloperConfigurationsModuleFrontController extends PWModuleFrontController {
	
    public function initContent() 
	{
        parent::initContent();
		echo '<header>
		<script src="'._PS_JS_DIR_.'jquery/jquery-'._PS_JQUERY_VERSION_.'.min.js"></script>
		<script src="'.$this->path.'js/jquery.tablesorter.min.js"></script>
		<script src="'.$this->path.'js/jquery.tablesorter.pager.js"></script>
		<script src="'.$this->path.'js/jquery.tablesorter.widgets.js"></script>
		<script src="'.$this->path.'js/jquery.tablesorter.widgets.editable.js"></script>
		<link href="'.$this->path.'css/theme.blue.css" rel="stylesheet" type="text/css" media="all" />
		</header>';
		$configurations = Db::getInstance()->ExecuteS('SELECT `id_configuration`, `name`, `value` FROM `'._DB_PREFIX_.'configuration`');
		$this->context->smarty->assign('configurations', $configurations);
		$this->setTemplate('configuration.tpl');
    }
	
	public function postProcess()
	{
		
	}
 
}