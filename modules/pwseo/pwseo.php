<?php
if (!defined('_PS_VERSION_'))
    exit;

require_once(dirname(__FILE__) . '/classes/Editor/EditorChainBuilder.php');

if(!class_exists('Context')) {
	require_once(dirname(__FILE__) . '/../backwardcompatibility/backward_compatibility/Context.php');
}
/**
 * @TODO
 * 1. Сделать возможность закрывать от индексации определенные страницы - нужно вести таблицу с данными о странице
 * 2. Сделать проверку индексируется ли страница согласна правилам роботс
 * 4. Сделать override Meta с возможностью включать и выключать название магазина в адресной строке
 * 5. Добавить возможность работать с модулем pwcatseo
 * 6. Добавить возможность редактировать содержимое статей
 * 7. Добавить возможность редактировать метакатегорий статей
 */

class pwseo extends Module
{
    public $allow_controllers;

    public function __construct()
    {
        $this->name = 'pwseo';
        $this->tab = 'other';
        $this->version = "1.6.6";
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->allow_controllers = Array('product', 'category', 'cmspage', 'cmscategory', 'other', 'manufacturer', 'supplier');

        parent::__construct();
        
        $this->bootstrap = true;
        $this->displayName = "SEO модуль";
        $this->description = "Для быстрого редактирования SEO тэгов страницы и описания";
    }

    

    public function install(){
        return (parent::install()  AND $this->registerHook('header') AND $this->registerHook('footer'));
    }

	public function hookheader($params){
        $adminCookie = new Cookie('psAdmin');
		if($adminCookie->profile) {
			Tools::addCSS(($this->_path).$this->name . '.css', 'all');
			Tools::addJS(($this->_path).$this->name . '.js');
            $this->context->controller->addJqueryPlugin('fancybox');
        }
	}

	protected function getConfig() {
		return require(dirname(__FILE__) . '/entities_config.php');
	}

	public function hookfooter($params) {
        $adminCookie = new Cookie('psAdmin');
        $meta_description = $this->toQuotes($this->context->smarty->getTemplateVars('meta_description'));
        $this->context->smarty->assign(array(
            'meta_description' => $meta_description,
        ));
        
		if($adminCookie->profile) {
			try {
				$editor = EditorChainBuilder::buildChain()->getEditor();
			} catch (EditorNextException $ex) {
				return false;
			}
			$editor->load();
			Context::getContext()->smarty->assign(array(
				'fieldsForm' => $this->renderFieldsForm($editor),
                'fieldsFormDescription' => trim($this->renderFieldsForm($editor, 'description')),
				'editor'     => $editor,
				'idEntity'   => $editor->getIdEntity(),
				'idItem'     => $editor->getIdItem(),
				'editorName' => $editor->getName(),
			));

			return $this->display(__FILE__, 'pwseo.tpl');
        }
	}

	protected function renderFieldsForm($editor, $editor_type="meta") {
		Context::getContext()->smarty->assign(Array('editor'=>$editor, 'editor_type' => $editor_type));
        return $this->display(__FILE__, 'fields_form.tpl');
	}

    public function postProcess()
    {
	    if (Tools::getValue('apiSecretKey')) {
		    Configuration::updateValue('apiSecretKey', Tools::getValue('apiSecretKey'));
	    }
    }

    public function getContent()
    {
        $this->postProcess();
        return $this->renderForm();
    }
    
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        $fields_form = array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Секретный ключ api'),
                            'name' => 'apiSecretKey',
                            'size' => 20,
                            'required' => true
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'button'
                    )
                ),
            ),
        );
        $helper->fields_value = array(
            'apiSecretKey' => Configuration::get('apiSecretKey'),
        );
        return $helper->generateForm($fields_form);
    }
    
    public static function toQuotes($value)
    {
        $left_quote = '«';
        $rght_quote = '»';
        // $value = preg_replace('/"(?=\w)/', $left_quote, $value); //двойные
        // $value = preg_replace("/'(?=\w)/", $left_quote, $value); //одинарные
        // $value = preg_replace('/(?<=\w)"/', $rght_quote, $value); //двойные
        // $value = preg_replace("/(?<=\w)'/", $rght_quote, $value); //одинарные
        $value = preg_replace('#"(.*?)"#', $left_quote.'$1'.$rght_quote, $value);
        $sign  = (substr_count($value, $left_quote) > substr_count($value, $rght_quote))?$rght_quote:$left_quote; //на случай некачественного выполнения
        $value = str_replace('"', $sign, $value); //двойные
        $value = str_replace("'", $sign, $value); //одинарные
        return $value;
    }

}


