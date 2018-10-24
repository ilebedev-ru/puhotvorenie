<?php
include_once('PWModuleFrontController.php');
class PwdeveloperAddhookModuleFrontController extends PWModuleFrontController {
	
	public $errors = array();
	
    public function initContent() 
	{
        parent::initContent();
		echo '<header>
		<script src="'._PS_JS_DIR_.'jquery/jquery-'._PS_JQUERY_VERSION_.'.min.js"></script>
		</header>';
		$hooks = Hook::getHooks();
		$modules = Module::getModulesInstalled();
		$this->context->smarty->assign(array(
			'hooks' => $hooks,
			'modules' => $modules,
			'errors' => $this->errors,
		));
		$this->setTemplate('addhook.tpl');
    }
	
	public function postProcess()
	{
		if(Tools::isSubmit('addHook')){
			$module = Module::getInstanceByName(Tools::getValue('mod'));
			if(Validate::isLoadedObject($module)){
				$hook = Tools::getValue('hook');
				if(Tools::getValue('hooktext')){
					$hook = Tools::getValue('hooktext');
				}
				// if(!Hook::get($hook)){ //не будем зацикливатья на существует не существует. если че все равно регаем
					// $this->errors[] = 'Такого хука не существует';
					// return true;
				// }
				if($module->registerHook($hook)){
					$this->errors[] = 'Хук привязан';
				}
			}
		}
	}
 
}