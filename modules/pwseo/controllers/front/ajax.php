<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.07.2015
 * Time: 23:32
 */

require_once dirname(__FILE__).'/../../classes/MetaTagsEditorPrestashop.php';
class pwseoAjaxModuleFrontController extends ModuleFrontController {

    private $editor;

    public function initContent()
    {
        $this->editor = new MetaTagsEditorPrestashop();
        $this->editor->entity = Tools::getValue('entity');
        $this->editor->id = Tools::getValue('id');
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
                case 'toogleStatus':
                    die($this->editor->toogleStatus());
                    break;
                case 'editField':
                    die($this->editor->editField(Tools::getValue('field'), Tools::getValue('value')));
                case 'editFields':
                    die($this->editor->editFields(Tools::getValue('fields_arr')));
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