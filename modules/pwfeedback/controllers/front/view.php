<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
include_once _PS_MODULE_DIR_ . 'pwfeedback/feedbackClass.php';
class PWFeedbackViewModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();
        $pwconfig = unserialize(Configuration::get('PWFEEDBACK_CONFIG'));
        $pwconfig['rating'] = array_reverse(feedbackClass::getRatings());
        $this->context->smarty->assign(Array(
                'pwconfig' =>   $pwconfig,
                'feedbacks'=>   feedbackClass::getFeedbacks(999),
                'this_path'=>  $this->module->getPathUri(),
                'this_path_tpl'=>  $this->module->getTemplatePath('pwfeedback-item.tpl'),
                'addform' =>  $this->module->getTemplatePath('addform.tpl')
            )
        );

        $this->setTemplate('view.tpl');
	}

    public function displayAjax()
    {
        if(Tools::isSubmit('submitPWFeedback')) {
            $pwconfig = unserialize(Configuration::get('PWFEEDBACK_CONFIG'));
            $pwfeedback = new feedbackClass();

            $this->errors = array_unique(array_merge($this->errors, $pwfeedback->validateController()));
            $this->checkhttp($pwfeedback->vk);
            $this->checkhttp($pwfeedback->fb);
            $this->checkhttp($pwfeedback->odk);
            $this->checkhttp($pwfeedback->youtube);
            $this->checkhttp($pwfeedback->twitter);

            if ($this->errors) {
                die(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
            } else {
                if ($pwfeedback->add()) {
                    $var_list = Array('{id}' => $pwfeedback->id, '{name}' => $pwfeedback->name, '{feedback}' => $pwfeedback->feedback, '{date}' => $pwfeedback->date_add);
                    if ($pwconfig['mailalert']) Mail::Send($this->context->language->id, 'newfeedback', Mail::l('New feedback!', $this->context->language->id), $var_list, $pwconfig['mailalert'], null, $pwfeedback->email, $pwfeedback->name, null, null, _PS_MODULE_DIR_ . 'pwfeedback/mails/', true);
                    die(Tools::jsonEncode(array('hasError' => false, 'errors' => Array(), 'isSaved' => true)));
                }
                die(Tools::jsonEncode(array('hasError' => true, 'errors' => Array($this->module->l('Can\'t save comments')))));
            }
        }
        die(Tools::jsonEncode(array('hasError' => true, 'errors' => Array($this->module->l('Can\'t save comments')))));
    }

    public function checkhttp(&$url){
        $url = trim($url);
        if(!empty($url))
            if(Tools::substr($url,0,4) != "http")
                $url='http://'.$url;
    }
}
