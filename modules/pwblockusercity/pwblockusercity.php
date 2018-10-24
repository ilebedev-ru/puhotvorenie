<?php
require_once dirname(__FILE__)."/classes/CityRule.php";
class PWblockusercity extends Module
{
    public $userIP          = null;
    public $userInfo        = null;
    private $CURLOPT_URL    = 'http://api.sypexgeo.net/json/';
    public $defaultCity     = "Санкт-Петербург";
    public $cities          = null;
    public $pop_cities      = null;
    public $other_cities    = null;
    public $errors          = Array();
    
    public function __construct()
    {
        global $cookie;
        $this->name = 'pwblockusercity';
        $this->tab = 'Custom modules';
        $this->version = '1.1';
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 1;
        if(version_compare(_PS_VERSION_, '1.5', '>=')){
            $this->context = Context::getContext();
        }
        parent::__construct();

        $this->userIP = $this->get_client_ip();
        if(version_compare(_PS_VERSION_, '1.5', '>=')){
	        if(!isset($this->context->cookie->city) || empty($this->context->cookie->city)) $this->userInfo = $this->getUserInfo();
        }else{
	        if(!isset($cookie->city) || empty($cookie->city)) $this->userInfo = $this->getUserInfo();
        }
        
        $this->displayName = $this->l('Отображение региона пользователя');
        include($_SERVER['DOCUMENT_ROOT']."/modules/pwblockusercity/vendor/allcities.php");
        include($_SERVER['DOCUMENT_ROOT']."/modules/pwblockusercity/vendor/pop_cities.php");
        $this->cities = $cities;
        $this->pop_cities = $pop_cities;
        $this->other_cities = $other_cities;
    }

    public function install()
    {
        $sqls = Array();
        $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pwblockusercity` (
                  `id_city_rule` int(8) NOT NULL AUTO_INCREMENT,
                  `city` varchar(255) DEFAULT NULL,
                  `description` text,
                  PRIMARY KEY (`id_city_rule`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
                ';
        $sqls[] = '
            INSERT INTO `'._DB_PREFIX_.'pwblockusercity` (`city`, `description`) VALUES
            (\'Екатеринбург\', \'<table style="width: 400px;" border="0">\r\n<tbody>\r\n<tr><td style="padding: 5px;"><img src="/modules/pwblockusercity/img/d/pochta.png" alt="Почта России" width="100" /></td><td style="text-align: left;"><strong>Почта России</strong> <br /> <strong>от 150 до 400 руб.</strong></td></tr><tr><td style="padding: 5px;"><img src="/modules/pwblockusercity/img/d/ems.png" alt="EMS" width="100" /></td><td style="text-align: left;"><strong>EMS курьерская доставка</strong> <br /> <strong>от 600 до 1500 руб.</strong></td></tr><tr><td style="padding: 5px;"><img src="/modules/pwblockusercity/img/d/kurier.png" alt="EMS" width="100" /></td><td style="text-align: left;"><strong>Курьерская доставка по Екатеринбургу</strong> <br /> <strong>от 100 до 300 руб.</strong><br /> <em>При заказе от 1000 руб. - бесплатно</em></td></tr></tbody></table>\');
            ';
        foreach ($sqls as $sql) {
            if(!Db::getInstance()->Execute($sql)) return false;
        }

        Configuration::updateValue('PS_PWBLOCKUSERCITY_RULE', '<table style="width: 400px;" border="0"><tbody><tr><td style="padding: 5px;"><img src="/modules/pwblockusercity/img/d/pochta.png" alt="Почта России" width="100" /></td><td style="text-align: left;"><strong>Почта России</strong> <br /> <strong>от 150 до 400 руб.</strong></td></tr><tr><td style="padding: 5px;"><img src="/modules/pwblockusercity/img/d/ems.png" alt="EMS" width="100" /></td><td style="text-align: left;"><strong>EMS курьерская доставка</strong> <br /> <strong>от 600 до 1500 руб.</strong></td></tr></tbody></table>', true);
        Configuration::updateValue('PS_PWBLOCKUSERCITY_CITY', $this->defaultCity);
        /*
    	if(version_compare(_PS_VERSION_, '1.5', '<')){
	    	$destin = "/override/controllers";
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$destin."/AuthController.php")){
                $this->context->controller->errors[] = 'AuthController.php exist in /override/controllers/front/';
                return false;
            }
            if(!copy($_SERVER['DOCUMENT_ROOT']."/modules/pwblockusercity/files_to_move/AuthController.php", $_SERVER['DOCUMENT_ROOT'].$destin."/AuthController.php")){
                $this->context->controller->errors[] = 'Cant copy file AuthController.php';
                return false;
            }
    	}else if(version_compare(_PS_VERSION_, '1.5', '>=')){
	    	$destin = "/override/controllers/front";	    	
            $file = "AddressController.php";
    	
        	if(file_exists($_SERVER['DOCUMENT_ROOT'].$destin."/AddressController.php")){
                $this->context->controller->errors[] = 'AddressController.php exist in /override/controllers/front/';
    	    	return false;
        	}
        	if(!copy($_SERVER['DOCUMENT_ROOT']."/modules/pwblockusercity/files_to_move/AddressController.php", $_SERVER['DOCUMENT_ROOT'].$destin."/AddressController.php")){
    	    	$this->context->controller->errors[] = 'Cant copy file AddressController.php';
    	    	return false;
        	}
        }
        //Remove class_index.php
            if(file_exists($_SERVER['DOCUMENT_ROOT']."/cache/class_index.php")){
                unlink($_SERVER['DOCUMENT_ROOT']."/cache/class_index.php");
            }
        //
        */
        if(version_compare(_PS_VERSION_, '1.5', '<')){
            return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('top');
        }else if(version_compare(_PS_VERSION_, '1.4', '>=')){
            return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayTop')
            && $this->registerHook('actionValidateOrder');
        }
    }

    public function uninstall()
    {
        Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'pwblockusercity`;');
        return parent::uninstall();
    }

    /**
    * PS 1.4 hook
    */
    public function hookHeader($params){

        Tools::addCSS($this->_path.'css/pwblockusercity.css');
        Tools::addJS(_PS_JS_DIR_.'jquery/jquery.autocomplete.js');
        Tools::addJS($this->_path.'js/pwblockusercity.js');
    }

    public function hookTop($params){
        global $cookie, $smarty;
        if(!isset($cookie->city) || empty($cookie->city)){
            if(empty($this->userInfo->city->name_ru)){
                $city = Configuration::get('PS_PWBLOCKUSERCITY_CITY');
            }else{
                $city = $this->userInfo->city->name_ru;
            }
            $cookie->city =  $city;
            $cookie->write();
        }else{
            $city = $cookie->city;
        }

        $smarty->assign($this->getConfig($city));
        return $this->display(__FILE__, '/views/templates/front/pwblockusercity.tpl');
    }

    /** PS 1.5-1.6 */
    
    public function hookDisplayHeader($params){
        $this->context->controller->addCSS($this->_path.'css/pwblockusercity.css');
        if (method_exists($this->context->controller, 'addJquery')){
            $this->context->controller->addJS($this->_path.'js/pwblockusercity.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'jquery/jquery.autocomplete.js');
        }
        if(!isset($this->context->cookie->city) || empty($this->context->cookie->city)){
	        if(empty($this->userInfo->city->name_ru)){
	            $city = Configuration::get('PS_PWBLOCKUSERCITY_CITY');
	        }else{
	            $city = $this->userInfo->city->name_ru;
	        }
	        $this->context->cookie->__set("city", $city);
        }else{
	        $city = $this->context->cookie->city;
        }

        $this->smarty->assign($this->getConfig($city));
        // return $this->display(__FILE__, '/views/templates/front/pwblockusercity.tpl');
    }

    public function getConfig($city){
        return array(
            "city" => $city,
            "pop_cities" => $this->pop_cities,
            "other_cities" => $this->other_cities,
            "deliveryShow" => Configuration::get('PS_PWBLOCKUSERCITY_ON'),
            "currentDelivery" => CityRule::getForCity($city)
        );
    }

    private function getUserInfo(){
        $curl = curl_init();
        $url = $this->CURLOPT_URL.$this->userIP;
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp);
    }

    public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        elseif(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        elseif(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        elseif(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        elseif(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        elseif(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function getRules(){
       return Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pwblockusercity` WHERE 1');
    }

    public function getContent(){
        global $cookie, $smarty, $currentIndex;
        $tpl = "";
        if(Tools::getValue('editRule')){
            $city_rule = new CityRule((int)Tools::getValue('editRule'));
            if($city_rule->city) {
                $_POST = array_map('stripslashes', $city_rule->getFields());
                $smarty->assign(Array());
            }else $this->errors[] = "Правило для города не найдено";
        }
        if(Tools::getValue('deleteRule')){
            $city_rule = new CityRule((int)Tools::getValue('deleteRule'));
            if($city_rule->city) {
                if($city_rule->delete()) $tpl.= '<div class="conf confirm"><img src="../img/admin/ok.gif"/>'.$this->l('Правило удалено').'</div>';
            }else $this->errors[] = "Правило для города не найдено";
        }
        if(isset($_POST)) $tpl.=$this->setPost();

        $smarty->assign(array(
            'module_name' => $this->name,
            'uri_start' => $currentIndex.'&configure='.urlencode($this->name).'&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module='.$this->tab.'&module_name='.urlencode($this->name),
            'rules' => self::getRules(),
            'default_city' => Configuration::get('PS_PWBLOCKUSERCITY_CITY'),
            'default_rule' => Configuration::get('PS_PWBLOCKUSERCITY_RULE'),
            'turn_on' => Configuration::get('PS_PWBLOCKUSERCITY_ON')
        ));

        if($this->errors){
            $smarty->assign(Array(
                'errors' => $this->errors
            ));
        }
        $tpl.= $smarty->fetch(_PS_MODULE_DIR_.'pwblockusercity/views/templates/admin/index.tpl');
        $iso = Language::getIsoById((int)($cookie->id_lang));
        $isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
        $ad = dirname($_SERVER["PHP_SELF"]);
        $tpl.= '
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
        return $tpl;
    }

    public function setPost(){
        if(Tools::isSubmit('submitConfig')){
            if(Tools::getValue('default_city')) Configuration::updateValue('PS_PWBLOCKUSERCITY_CITY', Tools::getValue('default_city'));
            if(Tools::getValue('default_rule')) Configuration::updateValue('PS_PWBLOCKUSERCITY_RULE', Tools::getValue('default_rule'), true);
            if(Tools::getValue('turn')) Configuration::updateValue('PS_PWBLOCKUSERCITY_ON', Tools::getValue('turn'));
            return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Настройки обновлены').'</div>';
        }
        if(Tools::isSubmit('submitEditRule')){
            if(Tools::getValue('id_city_rule')) $city_rule = new CityRule((int)Tools::getValue('id_city_rule'));
            else $city_rule = new CityRule();
            $this->errors = array_unique(array_merge($this->errors, $city_rule->validateControler()));
            if(!count($this->errors)){
                if($city_rule->save()){
                    if(Tools::getValue('id_city_rule'))  return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Правило для города обновлено').'</div>';
                    else return '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Правило для города создано').'</div>';
                }
            }
        }
    }
   
    public function hookactionValidateOrder($params)
    {
        if(empty($this->context->cookie->city)){
	        if(empty($this->userInfo->city->name_ru)){
	            $city = Configuration::get('PS_PWBLOCKUSERCITY_CITY');
	        }else{
	            $city = $this->userInfo->city->name_ru;
	        }
	        $this->context->cookie->__set("city", $city);
        }else{
	        $city = $this->context->cookie->city;
        }
        $order = &$params['order'];
        $id_address = $order->id_address_delivery;
        $id_address2 = $order->id_address_invoice;
        $address = new Address($id_address);
        $address->city = $city;
        $address->save();
        $address = new Address($id_address2);
        $address->city = $city;
        $address->save();
    }

}
