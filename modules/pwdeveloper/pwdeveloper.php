<?php
if (!defined('_PS_VERSION_'))
	exit;

include_once('lib/PWTools.php');
include_once('classes/Rep2.php');

class pwDeveloper extends Module
{
	
	private $html;
    private $modules;

    const fileModuleXML = "http://admin.leadget.ru/modules/info_automatic.xml";

    const REPOSITORY_LOGIN = 'admin';
    
    public $pwcontrollers = array(
        'configurations' => 'Configurations',
        'defines' => 'Defines',
        'products' => 'Создание товаров и копирование',
        'categories' => 'Категории',
        'cms' => 'Страницы CMS',
        'employee' => 'Сотрудники',
        'customer' => 'Покупатели',
        'manufacturers' => 'Производители',
        'features' => 'Характеристики',
        'generator' => 'Генератор',
        'addhook' => 'Привязать к хуку',
        'modules' => 'Генератор модулей',
        'cleaner' => 'Чистка товаров',
        'cart' => 'Копия корзины',
        'links' => 'Ссылки на страницы',
    );

    public function __construct()
	{
		$this->name = 'pwdeveloper';
		$this->tab = 'admin';
		$this->version = '1.1.2';
		$this->author = 'PrestaWeb.ru';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Помощь разработчику');
		$this->description = $this->l('Набор инструментов для разработки.');
		$this->modules = Array(
            'graphnvd3',
            'blockcurrencies',
            'blocklanguages',
            'gamification',
            'onboarding'
        );
        foreach(scandir(__DIR__.'/controllers/front') as $file){
           if(!in_array($file, array('.', '..', 'PWModuleFrontController.php', 'ajax.php', 'default.php')) && empty($this->pwcontrollers[str_replace('.php', '', $file)])){
               $this->pwcontrollers[str_replace('.php', '', $file)] = str_replace('.php', '', $file);
           }
        }
	}

	public function install()
	{
        $cookie = PWTools::getCustomerCookie($this->context);
        $cookie->pwDeveloper = true;
        $cookie->write();
		return parent::install() && $this->registerHook('header') && $this->registerHook('footer')
		&& $this->registerHook('displayBackOfficeHeader');
	}

	public function uninstall()
	{
		return parent::uninstall();
	}
	
	public function hookHeader($params)
	{
		if($this->context->cookie->pwDeveloper){
			$this->context->controller->addCSS(($this->_path).'css/pwdeveloper.css', 'all');
			$this->context->controller->addJS(($this->_path).'js/pwdeveloper.js');
			$this->context->controller->addjqueryPlugin('fancybox');
	 
			$this->smarty->assign(array(
				'pw_dev_url' => $this->context->link->getModuleLink('pwdeveloper', 'ajax', array())
			));
			return $this->display(__FILE__, 'header.tpl');
		}
	}
	
	public function getContent()
    {
        $id_lang = (int)Context::getContext()->language->id;
        $this->html = '';
		$this->postProcess();
		$this->smarty->assign(array(
			'pwDeveloperOn' => PWTools::getCustomerCookie($this->context)->pwDeveloper,
            'controllers' => $this->pwcontrollers,
            'uri' => Tools::getProtocol().Tools::getHttpHost() . '/module/' . $this->name . '/',
		));
        if($this->context->cookie->modulePassword){
            $xmlContent = $this->getModuleImportFile($this->context->cookie->modulePassword);
            try {
                $modulesXML = new SimpleXMLElement($xmlContent);
            } catch (Exception $e) {
                $this->html .= $this->displayError('XML не распарсено или не получено:'. $e->getMessage());
            }
            if(!isset($modulesXML[0]->module)) $this->html .= $this->displayError('Не удалось скачать файл');
            else{
                $modules = Array();
                foreach ($modulesXML[0]->module as $item) {
                    $modules[(string)$item->name] = $item;
                }
                $installedModules = Module::getModulesInstalled();
                foreach($installedModules as $installedModule){
                    if(isset($modules[$installedModule['name']])){
                        $modules[$installedModule['name']]['installed'] = true;
                        $modules[$installedModule['name']]['old'] = version_compare($installedModule['version'], (string)$modules[$installedModule['name']]->version, "<");
                    }
                }
                $this->context->smarty->assign(Array(
                    'modules' => $modules,
                ));

            }
        }
		return $this->html.$this->display(__FILE__, 'back.tpl'); 
    }

    public function getModuleImportFile($password){
        $context = stream_context_create(array (
            'http' => array (
                'header' => 'Authorization: Basic ' . base64_encode(self::REPOSITORY_LOGIN.":$password")
            )
        ));
        $data = file_get_contents(self::fileModuleXML, false, $context);
        //d($data);
        return $data;
    }

    public function installModule($module){
        $module = Module::getInstanceByName($module);
        if(is_object($module)){
            if($module->install()) return true;
        }
        return false;
    }
	
	public function postProcess()
	{
        if(Tools::isSubmit('submitPassword')){
            if(Tools::getValue('modulePassword')){
                $this->context->cookie->modulePassword = Tools::getValue('modulePassword');
                $this->context->cookie->write();
            }
        }
        if(Tools::isSubmit('submitModuleInstall')){
            $rep = new Rep2('http://' . self::REPOSITORY_LOGIN . ':' .$this->context->cookie->modulePassword.'@admin.leadget.ru/modules/rep/', _PS_ROOT_DIR_, $this->getModuleImportFile($this->context->cookie->modulePassword));
            foreach($_POST['modules'] as $module_name=>$value){
                if($rep->getModule($module_name))
                     if($this->installModule($module_name)) $this->html .= $this->displayConfirmation('Модуль '.$module_name.' установлен');
                     else $this->html .= $this->displayError('Модуль '.$module_name.' не установлен');
                else $this->html .= $this->displayError('Модуль '.$module_name.' не удалось скачать');
            }
        }
        if(Tools::isSubmit('submitModuleUpdate')){
            $module_update = Tools::getValue('module_update');
            /*Дописать обновление модулей*/
        }

        if(Tools::isSubmit('submitModuleLogOut')){
            $this->context->cookie->modulePassword = '';
            $this->context->cookie->write();
        }

		if (Tools::isSubmit('submitUnistallModule')){
			$disabled = array();
			$modules = Module::getModulesInstalled();
			foreach($modules as $mod)
			{
				$m = Module::getInstanceByName($mod['name']);
				if($m->tab == 'analytics_stats' && $m->uninstall()){
					$disabled[] = $m->name;
				}
			}
            foreach($this->modules as $module){
                if($module){
					$moduleObj = Module::getInstanceByName($module);
					if(Validate::isLoadedObject($moduleObj) && $moduleObj->uninstall()){
						$disabled[] = $moduleObj->name;
					}
                }
            }
            $this->html .= $this->displayConfirmation('Модулей успешно удалено: '.count($disabled).'<br />'.implode(', ',$disabled));
        }
		if(Tools::isSubmit('submitOnDeveloper')){
			$cookie = PWTools::getCustomerCookie($this->context);
			$cookie->pwDeveloper = true;
			$cookie->write();
			$this->html .= $this->displayConfirmation('Инструменты включены для вас');
		}
		if(Tools::isSubmit('submitOffDeveloper')){
			$cookie = PWTools::getCustomerCookie($this->context);
			$cookie->pwDeveloper = false;
			$cookie->write();
			$this->html .= $this->displayConfirmation('Инструменты выключены для вас');
		}
	}
	
	
	function hookFooter($params)
	{
        if($this->context->cookie->pwDeveloper) {
            $this->context->smarty->assign(array(
                'pwcontrollers' => $this->pwcontrollers,
            ));
            return $this->display(__FILE__, 'footer.tpl');
        }
	}
	
	public function hookDisplayBackOfficeHeader()
	{
		return '<script>$(document).ready(function(){$("#module_install").next(".alert").hide();});</script>';
	}

}
