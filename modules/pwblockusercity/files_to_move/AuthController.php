<?php
class AuthController extends AuthControllerCore{

public function preProcess()
	{
		parent::preProcess();

		if (self::$cookie->isLogged() AND !Tools::isSubmit('ajax'))
			Tools::redirect('my-account.php');

		if (Tools::getValue('create_account'))
		{
			$create_account = 1;
			self::$smarty->assign('email_create', 1);
		}

		if (Tools::isSubmit('SubmitCreate'))
		{
			if (!Validate::isEmail($email = Tools::getValue('email_create')) OR empty($email))
				$this->errors[] = Tools::displayError('Invalid e-mail address');
			elseif (Customer::customerExists($email, false, false))
			{
				$this->errors[] = Tools::displayError('An account is already registered with this e-mail, please fill in the password or request a new one.');
				$_POST['email'] = $_POST['email_create'];
				unset($_POST['email_create']);
			}
			else
			{
				$create_account = 1;
				self::$smarty->assign('email_create', Tools::safeOutput($email));
				$_POST['email'] = $email;
				$_POST['city'] 	= self::$cookie->city;
			}
		}
	}
}		