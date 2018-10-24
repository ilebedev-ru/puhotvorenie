<?php
/**
 * @varsion 0.2 - Добавлена рандомизация характеристик
 */
include_once('PWModuleFrontController.php');

class PwdeveloperModulesModuleFrontController extends PWModuleFrontController
{

    public $errors;
    public $hooks;
    public $moduleContent;
    public $moduleCreate;
    public $hooksonlyforinstall;

    public function __construct()
    {
       parent::__construct();
        $this->hooks = Array(
            'displayLeftColumn', 'displayRightColumn',
            'displayHeader', 'displayFooter',
            'displayHome', 'displayTop'
        );
        $this->hooksonlyforinstall = Array(
         'displayadminproductsextra', 'actionadminproductscontrollersaveafter'
        );
    }


    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('modules.tpl');
        $this->context->smarty->assign(array(
            'errors' => $this->errors,
            'hooks' => $this->hooks
        ));
    }

    public function postProcess()
    {
        if (Tools::isSubmit('addModule')) {
            $this->moduleCreate = Tools::getValue('name');
            if (empty($this->moduleCreate)) {
                $errors[] = "Не указано имя";
            }
            $this->moduleCreate = Tools::link_rewrite(strtolower($this->moduleCreate));

            if (empty($_POST['hooks']) && empty($_POST['myhooks'])) echo '<div class="warn">Обратите внимание хуки не были указаны</div>';
            if (is_dir(_PS_MODULE_DIR_ . $this->moduleCreate)) $errors[] = "Такой модуль уже есть";

            if (empty($errors)) {
                $blocktestDir = dirname(__FILE__) . '/../../blocktest/';
                mkdir(_PS_MODULE_DIR_ . $this->moduleCreate);
                $dir = _PS_MODULE_DIR_ . $this->moduleCreate . '/';

                $this->copyBase($blocktestDir, $dir);

                if($this->makeClass($blocktestDir, $dir))
                    echo '<p class="success">Класс ' . $this->moduleCreate . ' обновлен установлен</p>';
                else echo '<p class="error">Класс не установился</p>';

                if (Tools::getValue('makeController')){
                    if($this->makeController($blocktestDir, $dir))
                        echo '<p class="success">Контроллер ' . $this->moduleCreate . ' обновлен установлен. <a target="_blank" href="/module/'.$this->moduleCreate.'/page">Пример страницы.</a></p>';
                    $tpl_path = $dir.'views/templates/hook/' . $this->moduleCreate . '.tpl';
                } else{
                    $this->moduleContent = preg_replace('|\/\/start_controller(.*)\/\/end_controller|Uis', '', $this->moduleContent);
                    $tpl_path = $dir . $this->moduleCreate . '.tpl';
                }
                copy($blocktestDir . 'blocktest.tpl', $tpl_path);

                if (Tools::getValue('tpl')) {
                    $tpl_content = file_get_contents($tpl_path);
                    file_put_contents($tpl_path, $tpl_content."\n".Tools::getValue('tpl'));
                }

                if (!Tools::getValue('photo')) $this->moduleContent = preg_replace('|\/\/start_photo(.*)\/\/end_photo|Uis', '', $this->moduleContent);
                if (!Tools::getValue('settings')) $this->moduleContent = preg_replace('|\/\/start_setting(.*)\/\/end_setting|Uis', '', $this->moduleContent);
                if (!Tools::getValue('helpers')) $this->moduleContent = preg_replace('|\/\/start_helper(.*)\/\/end_helper|Uis', '', $this->moduleContent);

                $this->makeFunctionAndHooks($blocktestDir, $dir);
                if (file_put_contents($dir . $this->moduleCreate . '.php', $this->moduleContent)) {
                    echo '<p class="success">Модуль ' . $this->moduleCreate . ' создан</p>';
                    $this->installModule();
                }
            } else p($errors);
        }
    }

    public function rCopy($source, $dest){
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @param $name
     * @param $blocktest_dir
     * @param $dir
     * @param $module_content
     * @return mixed
     */
    private function makeClass($blocktest_dir, $dir)
    {
        if (Tools::getValue('makeClass')) {
            mkdir(_PS_MODULE_DIR_ . $this->moduleCreate . '/classes');
//                    $this->rCopy($blocktest_dir.'classes', $dir.'classes');
            copy($blocktest_dir . 'classes/blocktestClass.php', $dir . 'classes/' . $this->moduleCreate . 'Class.php');
            copy($blocktest_dir . 'classes/index.php', $dir . 'classes/index.php');
            $moduleClass_content = file_get_contents($dir . 'classes/' . $this->moduleCreate . 'Class.php');
            $moduleClass_content = str_replace('blocktest', $this->moduleCreate, $moduleClass_content);
            if (file_put_contents($dir . 'classes/' . $this->moduleCreate . 'Class.php', $moduleClass_content)) {
                return true;
            }
            return false;
        } else {
            $this->moduleContent = preg_replace('|\/\/start_class(.*)\/\/end_class|Uis', '', $this->moduleContent);
            return true;
        }
        return true;
    }

    /**
     * @param $name
     * @param $blocktestDir
     * @param $dir
     */
    private function makeController($blocktestDir, $dir)
    {
        mkdir(_PS_MODULE_DIR_ . $this->moduleCreate . '/controllers');
        $this->rCopy($blocktestDir . 'controllers', $dir . 'controllers'); //копируем папку с контроллерами
        mkdir(_PS_MODULE_DIR_ . $this->moduleCreate . '/views');
        copy($blocktestDir . '/views/index.php', $dir . '/views/index.php');
        mkdir(_PS_MODULE_DIR_ . $this->moduleCreate . '/views/templates');
        mkdir(_PS_MODULE_DIR_ . $this->moduleCreate . '/views/templates/front');
        $this->rCopy($blocktestDir . 'views/templates/front', $dir . 'views/templates/front'); //копируем папку с контроллерами
        mkdir(_PS_MODULE_DIR_ . $this->moduleCreate . '/views/templates/hook');
        copy($blocktestDir . '/views/templates/hook/index.php', $dir . '/views/templates/hook/index.php');

        $moduleController_content = file_get_contents($dir . 'controllers/front/page.php');
        $moduleController_content = str_replace('blocktest', $this->moduleCreate, $moduleController_content);
        if (!Tools::getValue('makeClass')) $moduleController_content = preg_replace('|\/\/start_class(.*)\/\/end_class|Uis', '', $moduleController_content);
        if (file_put_contents($dir . 'controllers/front/page.php', $moduleController_content)) {
            return true;
        }
        return false;
    }

    /**
     * @param $dir
     * @param $name
     */
    private function makeFunctionAndHooks($blocktest_dir, $dir)
    {
        $hook_string = '';
        $functions = "";
        if (Tools::getValue('myhooks')) {
            $myhooks = explode(',', Tools::getValue('myhooks'));
            foreach ($myhooks as $myhook) {
                $myhook = trim($myhook);
                if (strlen($myhook)) $_POST['hooks'][$myhook] = 1;
            }
        }
        if (!Tools::getValue('adminProductHook')) $this->moduleContent = preg_replace('|\/\/start_adminproducthook(.*)\/\/end_adminproducthook|Uis', '', $this->moduleContent);
        else{
            $_POST['hooks']['displayAdminProductsExtra'] = 1;
            $_POST['hooks']['actionAdminProductsControllerSaveAfter'] = 1;
            $tpl_path = (Tools::getValue('makeController') ? $dir . '/views/templates/hook' : $dir).'/adminproducthook.tpl';
            copy($blocktest_dir . '/views/templates/hook/adminproducthook.tpl', $tpl_path);
        }
        if (Tools::getValue('hooks')) {
            $hook_string .= "\n\t\t\t" . 'OR !$this->registerHook(Array(' . "\n";
            foreach ($_POST['hooks'] as $key => $value) {
                $hook_string .= "\t\t\t\t" . '\'' . $key . '\',' . "\n";
                if(!in_array(strtolower($key), $this->hooksonlyforinstall)) {
                    $functions .= "\tpublic function hook" . $key . "(\$params){\n";
                    if (version_compare(_PS_VERSION_, '1.5.0', '<')) $functions .= "\n\tglobal \$smarty, \$cookie;";
                    switch (strtolower($key)) {
                        case "header":
                        case "displayheader":
                            if (version_compare(_PS_VERSION_, '1.5.0', '>=')) $functions .= "\n\t\t\$this->context->controller->addCSS(\$this->_path.\$this->name.'.css', 'all');\n\t\t\$this->context->controller->addJS(\$this->_path.\$this->name.'.js');";
                            else $functions .= "\tTools::addCSS(\$this->_path.\$this->name.'.css');\n\tTools::addJS(\$this->_path.\$this->name.'.js');";
                            file_put_contents($dir . $this->moduleCreate . '.css', '/*generated by pwdeveloper */'); //Создаем CSS файл
                            file_put_contents($dir . $this->moduleCreate . '.js', '/*generated by pwdeveloper */'); //Создаем JS файл
                            break;
                        default:
                            $functions .= (Tools::getValue('code') ? Tools::getValue('code') . "\n" : '') . "\t\treturn \$this->display(__FILE__, '" . $this->moduleCreate . ".tpl');";
                            break;
                    }
                    $functions .= "\n\t}\n\n";
                }
            }
            $hook_string .= "\t\t\t" . '))';
        }
        $this->moduleContent = str_replace('%hook%', $hook_string, $this->moduleContent);
        $this->moduleContent = str_replace('%functions%', $functions, $this->moduleContent);
    }

    /**
     * @param $blocktest_dir
     * @param $dir
     */
    private function copyBase($blocktest_dir, $dir)
    {
        $displayName = Tools::getValue('displayName');
        $displayDesc = Tools::getValue('displayDesc');
        copy($blocktest_dir . 'blocktest.php', $dir . $this->moduleCreate . '.php');
        //copy($blocktest_dir . 'admin.tpl', $dir . 'admin.tpl');
        copy($blocktest_dir . 'logo.gif', $dir . 'logo.gif');
        copy($blocktest_dir . 'logo.png', $dir . 'logo.png');

        $this->moduleContent = file_get_contents($dir . $this->moduleCreate . '.php');
        $this->moduleContent = str_replace('blocktest', $this->moduleCreate, $this->moduleContent);
        $this->moduleContent = str_replace('%name%', $displayName, $this->moduleContent);
        $this->moduleContent = str_replace('%nameup%', strtoupper($this->moduleCreate), $this->moduleContent);
        $this->moduleContent = str_replace('%description%', $displayDesc, $this->moduleContent);
    }

    private function installModule()
    {
        $module = Module::getInstanceByName($this->moduleCreate);
        if (Tools::getValue('installit')) {
            if ($module->install()) echo '<p class="success">Модуль ' . $this->moduleCreate . ' установлен</p>';
            else $this->errors[] = 'Ошибка при установке модуля ' . $this->moduleCreate . '';
        }
    }

}