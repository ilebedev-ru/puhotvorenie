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
/*@TODO
	1. Check http:// in social links
	2. Try to get photo from social networks
	3. Moderation as option
	4. Fully Adaptive
	5. schema.org
	6. Rating + total rating
	7.Slider
*/
include_once _PS_MODULE_DIR_ . 'pwfeedback/feedbackClass.php';

class PWFeedback extends Module
{
    private $_html = '';
    private $_postErrors = array();

    const META_PAGE = "module-pwfeedback-view";

    public function __construct()
    {
        $this->name = 'pwfeedback';
        $this->tab = 'front_office_features';
        $this->author = 'prestaweb.ru';
        $this->version = '2.2.0';
        $this->module_key = "6789cbf54d1510737713f28b7fdb7808";
        $this->bootstrap = true;

        parent::__construct(); /* The parent construct is required for translations */

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Social Feedbacks Module');
        $this->description = $this->l('Allow for you shop to get customer social feedbacks to increase loyality');

    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if (!$this->_installDB()) {
            return false;
        }
        if (!$this->registerHook('home') or !$this->registerHook('header')) {
            return false;
        }
        if(!$this->installSampleData()) return false;

        Configuration::updateValue('PWFEEDBACK_VK', 1);
        Configuration::updateValue('PWFEEDBACK_FB', 1);
        Configuration::updateValue('PWFEEDBACK_ODK', 1);
        Configuration::updateValue('PWFEEDBACK_TWITTER', 1);
        Configuration::updateValue('PWFEEDBACK_YOUTUBE', 1);
        return true;
    }


    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        if (!$this->_uninstallDB()) {
            return false;
        }
        return true;
    }


    public function _installDB()
    {
        $query = Array();
        $query[] = "CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "pwfeedback (
		  `id_feedback` int(10) NOT NULL AUTO_INCREMENT,
		  `name` text,
		  `email` text,
		  `feedback` text,
		  `answer` text,
		  `rating` int(5) DEFAULT '0',
		  `fb` varchar(255) DEFAULT NULL,
		  `vk` varchar(255) DEFAULT NULL,
		  `twitter` varchar(255) DEFAULT NULL,
		  `odk` varchar(255) DEFAULT NULL,
		  `youtube` varchar(255) DEFAULT NULL,
		  `status` int(1) DEFAULT NULL,
		  `date_add` datetime DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (`id_feedback`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

        /*$query[] = "INSERT INTO `ps_pwfeedback` (`id_feedback`, `name`, `email`, `feedback`, `answer`, `fb`, `vk`, `twitter`, `odk`, `youtube`, `status`, `date_add`) VALUES
		(1, 'Иван', 'ja200714@gmail.com', 'Отличный сервис! Буду обращаться еще!', '', '', '', '', '', '', 1, '2015-06-14 19:25:05'),
		(2, 'Александра', 'sasha91@gmail.com', 'Делала заказ пару недель назад, товар уже пришел и наконец-то могу поделиться впечатлениями.\r\nОчень огорчила мятая упаковка, но сам товар пришел целым. Уже появились первые результаты от использования. ', '<p>Очень жаль, что упаковка пришла мятой. К сожалению, это зависит от транспортной компании. Но мы работаем над этим. Будем рады Вашим замечаниями!</p>', '', '', '', '', '', 1, '2015-06-14 19:33:11');
		";*/
         foreach ($query as $q)
            if (!Db::getInstance()->Execute($q))
                return false;

        $config = Array('count'=>4, 'PWFEEDBACK_DESIGN' => 2); //Default Settings
        Configuration::updateValue('PWFEEDBACK_CONFIG', serialize($config));
        return true;
    }

    private function installSampleData(){
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $feedback = new feedbackClass();
        $feedback->email = "robert@gmail.com";
        $feedback->name = $this->l("Robert Douglas");
        $feedback->rating = 5;
        $feedback->feedback = $this->l("I would say this shop is my favourite site without any second thought. My first online shopping was through this service. It was my laptops charger. Since then I have made countless purchases. Thank You!!");
        $feedback->answer = $this->l("Dear John, its was be very pleasant to get review from you! Thank you for your review.");
        $feedback->fb = "https://www.facebook.com/robert.a.douglas";
        $feedback->status = 1;
        if($lang->iso_code == "ru"){
            $feedback->fb = "";
            $feedback->name = $this->l("Иван");
            $feedback->feedback = $this->l("Отличный сервис! Буду обращаться еще!");
            $feedback->answer = $this->l("Спасибо! Будем рады!");
        }
        if(!$feedback->add()) return false;

        $feedback = new feedbackClass();
        $feedback->email = "sasha@gmail.com";
        $feedback->name = $this->l("Sasha Connor");
        $feedback->rating = 4;
        $feedback->feedback = $this->l("I have been buying from this website from a long time. I have purchased more than 100 items from here. The variety and concept is great. I have never faced any problem, thanks your for very helpful site and pretty serivce!!");
        $feedback->fb = "https://www.facebook.com/alexandra.connor.5";
        $feedback->status = 1;
        if($lang->iso_code == "ru"){
            $feedback->fb = "";
            $feedback->name = $this->l("Оля");
            $feedback->feedback = $this->l("Делала заказ пару недель назад, товар уже пришел и наконец-то могу поделиться впечатлениями.\r\nОчень огорчила мятая упаковка, но сам товар пришел целым. Уже появились первые результаты от использования. ");
            $feedback->answer = $this->l("Очень жаль, что упаковка пришла мятой. К сожалению, это зависит от транспортной компании. Но мы работаем над этим. Будем рады Вашим замечаниями!");
        }
        if(!$feedback->add()) return false;

        $meta = new Meta();
        $meta->page = self::META_PAGE;
        $meta->title = $this->l('Feedbacks about our shops');
        $meta->description  = $this->l('Our customers reviews with grades and opinions');
        $meta->url_rewrite  = "feedbacks";
        if($lang->iso_code == "ru"){
            $meta->page = self::META_PAGE;
            $meta->title = $this->l('Отзывы о нашем магазине');
            $meta->description  = $this->l('Странице отзывов о нашем магазине');
            $meta->url_rewrite  = "otzivi";
        }
        $meta->add();
        return true;
    }

    private function _uninstallDB()
    {
        $id_meta = Db::getInstance()->getValue('SELECT id_meta FROM `'._DB_PREFIX_.'meta` WHERE page =  "'.self::META_PAGE.'"');
        if($id_meta) {
            $meta = new Meta($id_meta);
            $meta->delete();
        }
        $query = 'DROP TABLE ' . _DB_PREFIX_ . 'pwfeedback';
        if (Db::getInstance()->Execute($query))
            return true;
        return false;
    }

    protected function initList()
    {
        $this->fields_list = array(
            'id_feedback' => array(
                'title' => $this->l('ID'),
                'width' => 120,
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text'
            ),
            'feedback' => array(
                'title' => $this->l('Feedback'),
                'width' => 140,
                'type' => 'text'
            ),
            'status' => array(
                'title' => $this->l('Moderated'),
                'width' => 30,
                'type' => 'bool'
            )
        );

        //if (Shop::isFeatureActive())
        //	$this->fields_list['id_shop'] = array('title' => $this->l('ID Shop'), 'align' => 'center', 'width' => 25, 'type' => 'int');
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array('edit', 'delete');
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->identifier = 'id_feedback';
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&add' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );

        $helper->title = $this->displayName;
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        return $helper;
    }

    protected function getListContent($id_lang)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'pwfeedback`
			WHERE 1 ORDER BY `id_feedback` DESC');
    }


    public function getContent()
    {
        $this->_html .= '<h2>' . $this->displayName . '</h2>';
        $id_feedback = (int)Tools::getValue('id_feedback');
        $html = "";
        if (Tools::isSubmit('submitPWConfig')) {
            $config = Tools::getValue('config');
            if (Tools::getValue('config')) Configuration::updateValue('PWFEEDBACK_CONFIG', serialize($config));

            /*Configuration::updateValue('PWFEEDBACK_VK', 0); Configuration::updateValue('PWFEEDBACK_FB', 0); Configuration::updateValue('PWFEEDBACK_ODK', 0); Configuration::updateValue('PWFEEDBACK_TWITTER', 0);Configuration::updateValue('PWFEEDBACK_YOUTUBE', 0);
            foreach($config as $key=>$row) Configuration::updateValue('PWFEEDBACK_'.strtoupper($key), 1);*/
        } elseif (Tools::isSubmit('savefeedback')) {
            if ($id_feedback = Tools::getValue('id_feedback'))
                $feedback = new feedbackClass((int)$id_feedback);
            else
                $feedback = new feedbackClass();
            $feedback->copyFromPost();

            if ($feedback->validateFields(false)) {
                $feedback->save();
                if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                    if ($error = ImageManager::validateUpload($_FILES['image']))
                        return false;
                    elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName))
                        return false;
                    elseif (!ImageManager::resize($tmpName, dirname(__FILE__) . '/photo/feedback-' . (int)$feedback->id . '.jpg'))
                        return false;
                    unlink($tmpName);
                    $feedback->save();
                }
                Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
            } else
                $html .= '<div class="conf error">' . $this->l('An error occurred during the save') . '</div>';
        }

        if (Tools::isSubmit('updatepwfeedback') || Tools::isSubmit('addpwfeedback')) {
            $helper = $this->initForm();
            if ($id_feedback) {
                $feedback = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'pwfeedback` WHERE id_feedback = ' . $id_feedback);

                if (file_exists(_PS_MODULE_DIR_ . 'pwfeedback/photo/feedback-' . $id_feedback . '.jpg')) {
                    $helper->fields_value['image']['image'] = "<img style='max-width:200px' src='/modules/pwfeedback/photo/feedback-" . $id_feedback . ".jpg?time=" . time() . "' />";
                    $helper->fields_value['image']['size'] = ceil(filesize(_PS_MODULE_DIR_ . 'pwfeedback/photo/feedback-' . $id_feedback . '.jpg') / 1024);
                } else $helper->fields_value['image']['image'] = '';
                $helper->fields_value['name'] = $feedback['name'];
                $helper->fields_value['feedback'] = $feedback['feedback'];
                $helper->fields_value['answer'] = $feedback['answer'];
                $helper->fields_value['status'] = $feedback['status'];
                $helper->fields_value['fb'] = $feedback['fb'];
                $helper->fields_value['twitter'] = $feedback['twitter'];
                $helper->fields_value['vk'] = $feedback['vk'];
                $helper->fields_value['odk'] = $feedback['odk'];
                $helper->fields_value['youtube'] = $feedback['youtube'];
                $helper->fields_value['date_add'] = $feedback['date_add'];
                $helper->fields_value['rating'] = $feedback['rating'];
                $this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_feedback');
                $helper->fields_value['id_feedback'] = (int)$id_feedback;
                $helper->id = $id_feedback;
            } else {
                $helper->fields_value['image'] = '';
                $helper->fields_value['name'] = '';
                $helper->fields_value['feedback'] = '';
                $helper->fields_value['answer'] = '';
                $helper->fields_value['status'] = '';
                $helper->fields_value['fb'] = '';
                $helper->fields_value['twitter'] = '';
                $helper->fields_value['vk'] = '';
                $helper->fields_value['odk'] = '';
                $helper->fields_value['youtube'] = '';
                $helper->fields_value['rating'] = '';
                $helper->fields_value['date_add'] = date("Y-m-d", time());
            }
            $helper->identifier = "id_feedback";

            return $html . $helper->generateForm($this->fields_form);
        } else if (Tools::isSubmit('deletepwfeedback')) {
            $feedback = new feedbackClass((int)$id_feedback);
            $feedback->delete();
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
        } else if (Tools::isSubmit('deletepwfeedback')) {
            $feedback = new feedbackClass((int)$id_feedback);
            $feedback->delete();
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
        } else if (Tools::isSubmit('deleteImage')) {
            $feedback = new feedbackClass((int)$id_feedback);
            if ($feedback->id)
                if (file_exists(_PS_MODULE_DIR_ . 'pwfeedback/photo/feedback-' . $id_feedback . '.jpg'))
                    unlink(_PS_MODULE_DIR_ . 'pwfeedback/photo/feedback-' . $id_feedback . '.jpg');
            Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
        } else {
            $helper = $this->initList();
            return $html . $helper->generateList($this->getListContent((int)Configuration::get('PS_LANG_DEFAULT')), $this->fields_list) . $this->displayForm();
        }
    }

    public function displayForm()
    {
        $config = unserialize(Configuration::get('PWFEEDBACK_CONFIG'));
        return '
		<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
			<div class="panel col-lg-12"><fieldset>
				<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Main Settings') . '</legend>
				<div class="margin-form">
					<input type="checkbox" id="pwfeedback_jscheck" name="config[jscheck]" value="1" ' . (isset($config['jscheck']) ? 'checked="checked"' : '') . ' />
					<label class="t" for="pwfeedback_jscheck">' . $this->l('Javascript check require fields') . '</label>
				</div>
				<div class="clear"></div>
				<label for="">' . $this->l('Mail alert') . '</label>
				<div class="margin-form">
					<input type="text" name="config[mailalert]" value="' . (isset($config['mailalert']) ? $config['mailalert'] : '') . '" />
					<p class="clear">' . $this->l('Email for mail alert about new feedbacks') . '</p>
				</div>
				<div class="clear"></div>
				<label for="">' . $this->l('How many feedbacks show on homepage or in sidebar?') . '</label>
				<div class="margin-form">
					<input type="text" name="config[count]" value="' . $config['count'] . '" />
					<p class="clear">' . $this->l('Integer number, example 10') . '</p>
				</div>
				<div class="clear"></div>
			</fieldset></div>
			<div class="panel col-lg-12">
			<fieldset>
				<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Choose design') . '</legend>
				<div class="margin-form">
					<input type="radio" id="PWFEEDBACK_DESIGN_1" name="config[PWFEEDBACK_DESIGN]" value="1" ' . (isset($config['PWFEEDBACK_DESIGN']) && $config['PWFEEDBACK_DESIGN'] == 1 ? 'checked' : '') . ' />
					<label class="t" for="PWFEEDBACK_DESIGN_1">' . $this->l('Simple design') . '</label>
					<input type="radio" id="PWFEEDBACK_DESIGN_2" name="config[PWFEEDBACK_DESIGN]" value="2" ' . (isset($config['PWFEEDBACK_DESIGN']) && $config['PWFEEDBACK_DESIGN'] == 2 ? 'checked' : '') . ' />
					<label class="t" for="PWFEEDBACK_DESIGN_2">' . $this->l('Design with slider') . '</label>
				</div>
				<div class="clear"></div>
			</fieldset>
			</div>
			<div class="panel col-lg-12">
			<fieldset>
				<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Social Networks Settings') . '</legend>
				<div class="margin-form">
					<input type="checkbox" id="pwfeedback_vk" name="config[vk]" value="1" ' . (isset($config['vk']) ? 'checked="checked"' : '') . ' />
					<label class="t" for="pwfeedback_vk">' . $this->l('Use Link to Vkontakte') . '</label>
				</div>
				<div class="clear"></div>
				<div class="margin-form">
					<input type="checkbox" id="pwfeedback_twitter" name="config[twitter]" value="1" ' . (isset($config['twitter']) ? 'checked="checked"' : '') . ' />
					<label class="t" for="pwfeedback_twitter">' . $this->l('Use Link to Twitter') . '</label>
				</div>
				<div class="clear"></div>
				<div class="margin-form">
					<input type="checkbox" id="pwfeedback_fb" name="config[fb]" value="1" ' . (isset($config['fb']) ? 'checked="checked"' : '') . ' />
					<label class="t" for="pwfeedback_fb">' . $this->l('Use Link to Facebook') . '</label>
				</div>
				<div class="clear"></div>
				<div class="margin-form">
					<input type="checkbox" id="pwfeedback_odk" name="config[odk]" value="1" ' . (isset($config['odk']) ? 'checked="checked"' : '') . ' />
					<label class="t" for="pwfeedback_odk">' . $this->l('Use Link to Odnoklassniki') . '</label>
				</div>
				<div class="clear"></div>
				<div class="margin-form">
					<input type="checkbox" id="pwfeedback_youtube" name="config[youtube]" value="1" ' . (isset($config['youtube']) ? 'checked="checked"' : '') . ' />
					<label class="t" for="pwfeedback_youtube">' . $this->l('Use Link to Youtube') . '</label>
				</div>
				<div class="clear"></div>
			</fieldset>
			</div>
			<center><input type="submit" name="submitPWConfig" value="' . $this->l('Save') . '" class="button" /></center>
		</form>';
    }



    protected function initForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $image_url = (file_exists(_PS_MODULE_DIR_.'pwfeedback/photo/feedback-'.Tools::getValue('id_feedback').'.jpg') ? _MODULE_DIR_.'pwfeedback/photo/feedback-'.Tools::getValue('id_feedback').'.jpg' : '');
        $image_url = ImageManager::thumbnail(_PS_MODULE_DIR_.'pwfeedback/photo/feedback-'.Tools::getValue('id_feedback').'.jpg', 'feedback-'.Tools::getValue('id_feedback'), 350);
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Edit or add new feedback'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'value' => true
                ),

                array(
                    'type' => 'file',
                    'label' => $this->l('Image:'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false
                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Feedback:'),
                    'name' => 'feedback',
                    'cols' => 40,
                    'rows' => 10,
                    'value' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Answer:'),
                    'name' => 'answer',
                    'cols' => 40,
                    'autoload_rte' => true,
                    'rows' => 10,
                    'value' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Display'),
                    'name' => 'status',
                    'class' => 't',
                    'value' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'status_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'status_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Rating'),
                    'name' => 'rating',
                    'class' => 't',
                    'value' => true,
                    'is_bool' => false,
                    'values' => feedbackClass::getRatings()
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Facebook'),
                    'name' => 'fb',
                    'size' => 100,
                    'value' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Twitter'),
                    'name' => 'twitter',
                    'size' => 100,
                    'value' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Vkontakte'),
                    'name' => 'vk',
                    'size' => 100,
                    'value' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Odnoklassniki.ru'),
                    'name' => 'odk',
                    'size' => 100,
                    'value' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Youtube'),
                    'name' => 'youtube',
                    'size' => 100,
                    'value' => true
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Date'),
                    'name' => 'date_add',
                    'size' => 100,
                    'value' => true
                )

            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'feedback';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang)
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );

        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'savefeedback';
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' =>
                array(
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Back to list')
                )
        );
        return $helper;
    }

    public function hookRightColumn($params)
    {
        $pwconfig = unserialize(Configuration::get('PWFEEDBACK_CONFIG'));
        $pwconfig['rating'] = array_reverse(feedbackClass::getRatings());
        $this->context->smarty->assign(Array(
            'pwconfig' => $pwconfig,
            'feedbacks' => feedbackClass::getFeedbacks($pwconfig['count']),
            'this_path' => $this->_getApplicableTemplateDir('pwfeedback-item.tpl')
        ));
        return $this->display(__FILE__, 'pwfeedback-side.tpl');
    }

    public function hookHome($params)
    {
        $pwconfig = unserialize(Configuration::get('PWFEEDBACK_CONFIG'));
        $pwconfig['rating'] = array_reverse(feedbackClass::getRatings());
        $this->context->smarty->assign(Array(
            'pwconfig' => $pwconfig,
            'feedbacks' => feedbackClass::getFeedbacks($pwconfig['count']),
            'this_path' => $this->_getApplicableTemplateDir('pwfeedback-item.tpl')
        ));
        return $this->display(__FILE__, 'pwfeedback.tpl');
    }

    public function hookleftcolumn($params)
    {
        return $this->hookRightColumn($params);
    }

    public function hookDisplayHeader($params)
    {
        $pwconfig = unserialize(Configuration::get('PWFEEDBACK_CONFIG'));
        $this->context->controller->addCSS($this->_path . 'css/pwfeedback.css', 'all');
        $this->context->controller->addJS($this->_path . 'js/pwfeedback.js', 'all');
        if($pwconfig['PWFEEDBACK_DESIGN'] == 2){
            $this->context->controller->addCSS($this->_path . 'css/design-2.css', 'all');
            $this->context->controller->addJqueryPlugin('bxslider');
        }else $this->context->controller->addCSS($this->_path . 'css/design-1.css', 'all');
        $this->context->controller->addJS($this->_path . 'pwfeedback.js');
    }
}

