<?php
class PwdeveloperAjaxModuleFrontController extends ModuleFrontController {
 
    public function initContent() 
	{
        parent::initContent();
        $this->ajax = true; // enable ajax
    }

    public function displayAjax() 
	{
        if ($this->errors){
            die(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
		}
        else {
            switch (Tools::getValue('action')) {
				case 'SetField':
					die($this->setField());
					break;
                default:
                    exit;
            }
            exit;
        }
    }
	
	
	private function setField()
	{
		$identifier = Tools::getValue('identifier');
		$id = Tools::getValue('id');
		$value = Tools::getValue('value');
		$content = Tools::getValue('content');
		switch ($identifier) {
			case 'id_configuration' :
				return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'configuration` SET '.$value.'="'.$content.'" WHERE '.$identifier.'="'.$id.'"');
			default:
				return false;
		}
	}
	
 
}