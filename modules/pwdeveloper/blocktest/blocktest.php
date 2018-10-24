<?php
if (!defined('_PS_VERSION_'))
    exit;
//start_class
require_once dirname(__FILE__).'/classes/blocktestClass.php';
//end_class
class blocktest extends Module
{
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.1;
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("%name%");
        $this->description = $this->l("%description%");
        //start_controller
        $this->controllers = array('page');
        //end_controller
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        if ( !parent::install() %hook%
            //start_class
            OR !$this->installDB($this->name)
            //end_class
        ) return false;

        return true;
    }

    //start_class
    public function uninstall()
    {
        return (parent::uninstall() && $this->unistallDB($this->name));
    }

    public function installDB($name){
        $query = Array();
        $query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$name.'`;';
        $query[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$name.'` (
		  `id_'.$name.'` int(10) NOT NULL AUTO_INCREMENT,
		  `name` text,
		  `date_add` datetime DEFAULT "0000-00-00 00:00:00",
		  `date_upd` datetime DEFAULT "0000-00-00 00:00:00",
		  PRIMARY KEY (`id_'.$name.'`)
		) DEFAULT CHARSET=utf8;';

        foreach ($query as $q)
            if (!Db::getInstance()->Execute($q))
                return false;

        $classname = $name.'Class';
        $item = new $classname();
        $item->name = "Example 1";
        $item->add();
        $item = new $classname();
        $item->name = "Example 2";
        $item->add();

        return true;
    }

    public function unistallDB($name){
        $query = Array();
        $query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$name.'`;';
        foreach ($query as $q)
            if (!Db::getInstance()->Execute($q))
                return false;

        return true;
    }
    //end_class

    //start_helper
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Настройки'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Случайная настройка 1'),
                        'name' => '%nameup%_OPTION1',
                        'hint' => $this->l('Select which category is displayed in the block. The current category is the one the visitor is currently browsing.'),
                        'values' => array(
                            array(
                                'id' => 'home',
                                'value' => 0,
                                'label' => $this->l('Вариант 1')
                            ),
                            array(
                                'id' => 'current',
                                'value' => 1,
                                'label' => $this->l('Вариант 2')
                            ),
                            array(
                                'id' => 'parent',
                                'value' => 2,
                                'label' => $this->l('Вариант 3')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Настройка 2'),
                        'name' => '%nameup%_OPTION2',
                        'desc' => $this->l('Подсказка'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Настройка 3'),
                        'name' => '%nameup%_OPTION3',
                        'desc' => $this->l('Подсказка'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit%nameup%';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            '%nameup%_OPTION1' => Tools::getValue('%nameup%_OPTION1', Configuration::get('%nameup%_OPTION1')),
            '%nameup%_OPTION2' => Tools::getValue('%nameup%_OPTION2', Configuration::get('%nameup%_OPTION2')),
            '%nameup%_OPTION3' => Tools::getValue('%nameup%_OPTION3', Configuration::get('%nameup%_OPTION3')),
        );
    }
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit%nameup%'))
        {
            $maxDepth = (int)(Tools::getValue('%nameup%_OPTION1'));
            if ($maxDepth < 0)
                $output .= $this->displayError($this->l('Опция не прошла проверку, убирите её из кода если не нужна'));
            else{
                Configuration::updateValue('%nameup%_OPTION1', Tools::getValue('%nameup%_OPTION1'));
                Configuration::updateValue('%nameup%_OPTION2', Tools::getValue('%nameup%_OPTION2'));
                Configuration::updateValue('%nameup%_OPTION3', Tools::getValue('%nameup%_OPTION3'));
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf=6');
            }
        }
        return $output.$this->renderForm();
    }
    //end_helper

    //start_photo
    public function getPhoto($name = 'photo1'){
        global $errors;
        if (isset($_FILES[$name]) && isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
        {
            $ps_image_regeneration_method = (int)Configuration::get('PS_IMAGE_GENERATION_METHOD');
            Configuration::updateValue('PS_IMAGE_GENERATION_METHOD', 1);
            $ext= explode('.', $_FILES[$name]['name']);
            $ext = end($ext);
            if ((bool)file_exists(dirname(__FILE__).'/'.$name.'.jpg')) unlink(dirname(__FILE__).'/'.$name.'.jpg');
            if ($error = checkImage($_FILES[$name], $this->maxImageSize))
                $errors .= $error;
            elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES[$name]['tmp_name'], $tmpName))
                return false;
            elseif (!imageResize($tmpName, dirname(__FILE__).'/'.$name.'.jpg', NULL, NULL, $ext))
                $errors .= $this->displayError($this->l('An error occurred during the image upload.'));

            if (isset($tmpName)) unlink($tmpName);
            Configuration::updateValue('PS_IMAGE_GENERATION_METHOD', (int)$ps_image_regeneration_method);

            if(!$errors) return true;
        }
        return 0;
    }
    //end_photo

    //start_adminproducthook
    public function hookActionAdminProductsControllerSaveAfter($params){
        $product = $params['return'];
        if ($product->id AND Tools::getValue('blocktestData')) {
            $product->save();
        }
    }

    public function hookdisplayAdminProductsExtra($params){
        $product = new Product(Tools::getValue('id_product'));

        return $this->display(__FILE__, 'adminproducthook.tpl');
    }
    //end_adminproducthook


%functions%
}


