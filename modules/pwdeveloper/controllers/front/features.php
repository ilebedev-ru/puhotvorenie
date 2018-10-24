<?php
include_once('PWModuleFrontController.php');
class PWdeveloperFeaturesModuleFrontController extends PWModuleFrontController
{

    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('features.tpl');
    }

    public function makeValue($id_feature, $value)
    {
        global $count;
        $fv = new FeatureValue();
        $value = Tools::ucfirst($value);
        $fv->value = PWTools::createMultiLangField($value);
        $fv->id_feature = $id_feature;
        if ($fv->add()) {
            $count['fv']++;
            return true;
        }
        return false;
    }

    public function makeF($name)
    {
        global $count;
        $f = new Feature();
        $name = Tools::ucfirst($name);
        $f->name = PWTools::createMultiLangField($name);
        if ($f->add()) {
            $count['f']++;
            return $f->id;
        }
        return false;
    }

    public function postProcess()
    {
        global $count;
        $count = Array('f' => 0, 'fv' => 0);

        if (Tools::isSubmit('submitList')) {
            $featurelist = Tools::getValue('list');
            $arr = explode("\n", $featurelist);
            $i = 0;
            foreach ($arr as $row) {
                $row = trim($row);
                $row = explode(":", $row);
                $feature_name = trim($row[0]);
                $value_name = trim($row[1]);
                $id_feature = Db::getInstance()->getValue('SELECT id_feature FROM `' . _DB_PREFIX_ . 'feature_lang` WHERE name LIKE "' . $feature_name . '"');
                if ($id_feature) {
                    $id_feature_value = Db::getInstance()->getValue('SELECT id_feature_value FROM `' . _DB_PREFIX_ . 'feature_value_lang` WHERE `value` LIKE "' . $value_name . '"');
                    if ($id_feature_value) continue;
                    else $this->makeValue($id_feature, $value_name);
                } else {
                    $id_feature = $this->makeF($feature_name);
                    if ($id_feature) {
                        $id_feature_value = Db::getInstance()->getValue('SELECT id_feature_value FROM `' . _DB_PREFIX_ . 'feature_value_lang` WHERE `value` LIKE "' . $value_name . '"');
                        if ($id_feature_value) continue;
                        else  $this->makeValue($id_feature, $value_name);
                    }
                }
            }
            echo '<p class="success alert alert-success">Добавлено: <b>' . $count['f'] . '</b> свойств и <b>' . $count['fv'] . ' значений</b></p>';
        }
    }
}
?>