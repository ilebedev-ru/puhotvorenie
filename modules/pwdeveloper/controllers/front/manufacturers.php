<?php
include_once('PWModuleFrontController.php');

class PwdeveloperManufacturersModuleFrontController extends PWModuleFrontController {

    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('manufacturers.tpl');
    }

    public function postProcess()
    {
        require_once(__DIR__.'/../../lib/PWTools.php');
        if(Tools::isSubmit('submitList')){
            $manufacturers = Tools::getValue('list');
            $arr = preg_split('/\\r\\n?|\\n/', $manufacturers);
            foreach($arr as $row){
                $row = trim($row);
                if(strlen($row)>1){
                    $manufacturer = new Manufacturer();
                    $manufacturer->name = $row;
                    $manufacturer->link_rewrite = PWTools::createMultiLangField(Tools::link_rewrite($row));
                    $manufacturer->active = 1;
                    $manufacturer->add();
                }
            }
            echo '<p class="success alert alert-success">Успешно добавлено</p>';
        }
    }

}