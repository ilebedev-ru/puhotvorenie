<?php
include_once('PWModuleFrontController.php');

class PwdeveloperEmployeeModuleFrontController extends PWModuleFrontController {
	
	public $errors = array();
	
    public function initContent() 
	{
        parent::initContent();
		echo '<header>
		<script src="'._PS_JS_DIR_.'jquery/jquery-'._PS_JQUERY_VERSION_.'.min.js"></script>
		</header>';
		$employees =Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'employee');
		$this->context->smarty->assign('employees', $employees);
		$this->setTemplate('employee.tpl');
    }
	
	public function postProcess()
	{
		if(Tools::isSubmit('pass')){
			$employee = new Employee(Tools::getValue('pass'));
			$this->context->smarty->assign('employee', $employee);
		}
		if(Tools::isSubmit('editEmployee')){
			$id_employee = Tools::getValue('id_employee');
			$employee = new Employee($id_employee);
			if(ValidateCore::isLoadedObject($employee)){
				$employee->email = Tools::getValue('email');
				if(Tools::getValue('password')){
					$employee->passwd = Tools::encrypt(Tools::getValue('password'));
				}
				if ($employee->validateFields(true)){
					$employee->save();
					$this->errors[] = 'Обновлено';
				}
			}
		}
		if(Tools::isSubmit('createEmployee')){
			$employee = new Employee();
			$employee->email = Tools::getValue('email');
			$employee->passwd = Tools::encrypt(Tools::getValue('password'));
			$employee->id_profile = _PS_ADMIN_PROFILE_;
			$employee->lastname = 'default';
			$employee->firstname = 'default';
			$employee->id_lang = ConfigurationCore::get('PS_LANG_DEFAULT');
			$employee->bo_uimode = 'click';
			if ($employee->validateFields(true)){
				$employee->save();
				$this->errors[] = 'Пользователь добавлен';
			}
		}
		if(Tools::getValue('auth')){
			$this->forceLogin(Tools::getValue('auth'));
		}
		$this->context->smarty->assign('errors', $this->errors);
	}
	
	function forceLogin($id_employee){
		if(is_object ($this->context->employee)){
			$this->context->employee->logout();
		}
		$this->context->employee = new Employee($id_employee);
		$this->context->employee->remote_addr = ip2long(Tools::getRemoteAddr());
		$cookie = PWTools::getAdminCookie();
		$cookie->id_employee = $this->context->employee->id;
		$cookie->email = $this->context->employee->email;
		$cookie->profile = $this->context->employee->id_profile;
		$cookie->passwd = $this->context->employee->passwd;
		$cookie->remote_addr = $this->context->employee->remote_addr;
		$cookie->write();
		
	}
 
}