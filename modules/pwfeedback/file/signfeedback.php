<?php

include(dirname(__FILE__).'/config/config.inc.php');
include(dirname(__FILE__).'/init.php');

/** VV004 **/
/** END VV004 **/
		
if(isset($_POST['process']) AND $_POST['process']==1)
	{
	  
		$name 	 	= pSQL(htmlentities(Tools::getValue('name'), ENT_COMPAT, 'UTF-8'));
		$youtitle 	 	= pSQL(htmlentities(Tools::getValue('youtitle'), ENT_COMPAT, 'UTF-8'));
		$feedback 	= pSQL(htmlentities(Tools::getValue('feedback'), ENT_COMPAT, 'UTF-8'));
		$permission = 1;
		$email 		= pSQL(htmlentities(Tools::getValue('email'), ENT_COMPAT, 'UTF-8'));
		$date       = date('Y-m-d');
		
		if(strlen($name) < 2 ||!Validate::isMessage($name)) $errors[] = Tools::displayError('Введите имя');
		if(!empty($email)) if(!Validate::isEmail($email)) $errors[] = Tools::displayError('Не корректный email');
		if(strlen($feedback) < 5 ||!Validate::isMessage($feedback)) $errors[] = Tools::displayError('Слишком короткий отзыв');
					
	
		if(!empty($errors))
		{
			global $cookie, $smarty;
			$smarty->assign(array(
			'errors'          => $errors,
			'name'              => $name,
			'feedback'          => $feedback,
			'permission'        => $permission,
			'email'             => $email
			));

		}
		else
		{
			$query = Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'feedback`
			( `name`,`feedback`,`permission`,`email`,`date`, `youtitle`) VALUES
			("'.$name.'","'.$feedback.'","'.$permission.'","'.$email.'","'.$date.'", "'.$youtitle.'")
			');
			$smarty->assign('confirmation', 1);        
			if($feedbackconf['mailsend']){
				$contact = new Contact(1, $cookie->id_lang);
				$var_list = Array('{name}' => $name, '{date}' => $date, '{feedback}' => $feedback);
				Mail::Send($cookie->id_lang, 'newfeedback', 'Новый отзыв на сайте', $var_list, $contact->email, $contact->name, $email,  $name, null, null, dirname(__FILE__).'/mails/');    
			}
		}
	}
		
				
include(dirname(__FILE__).'/header.php');
$smarty->display(_PS_MODULE_DIR_.'feedback/signfeedback.tpl');
include(dirname(__FILE__).'/footer.php');
?>