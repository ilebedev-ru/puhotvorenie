<?php
if (!defined('_PS_VERSION_'))
    exit;
//start_class
//require_once dirname(__FILE__).'/classes/pwproducttimerClass.php';
//end_class
class pwproducttimer extends Module
{

    public $products;
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.2;
        $this->author = 'PrestaWeb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Модуль акций");
        $this->description = $this->l("Отображение товара со скидкой на главной странице");
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
    }



    public function install()
    {

        if ( !parent::install() 
			OR !$this->registerHook(Array(
				'displayHome',
			))
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
		  `id_product` int(10) NOT NULL,
		  PRIMARY KEY (`id_'.$name.'`)
		) DEFAULT CHARSET=utf8;';

        foreach ($query as $q)
            if (!Db::getInstance()->Execute($q))
                return false;

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


    //end_helper
    public function getContent()
    {
        $this->context->controller->addJqueryPlugin('tagify');
        $this->context->controller->addJqueryUI('ui.widget');

        if (Tools::isSubmit('submitPWPRODUCTTIMER')) {
            Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . $this->name);
            $product_IDS = explode(',', $_POST['product_id']);

            if (!empty($product_IDS)) {
                foreach ($product_IDS as $product_ID) {
                    $product = new Product($product_ID);
                    $specificPrice = SpecificPrice::getByProductId($product->id);
                    $product->specificPrice = $specificPrice;
                    if ($product->active == '1' && !empty($specificPrice) && $product->specificPrice[0]['to'] != '0000-00-00 00:00:00') {
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.$this->name. '`(id_product) VALUE('.$product->id.')');
                    } else {
                        unset($product);
                    }
                }
            }
        }

        $ids = Db::getInstance()->executeS('SELECT id_product FROM `' . _DB_PREFIX_ . $this->name);

        foreach ($ids as $id) {
            self::getProductsStatus($id);
        }

        $prd_ids = array();
        foreach ($ids as $id) {
            foreach ($id as $tmp) {
                $prd_ids[] = $tmp;
            }
        }
        $this->smarty->assign(array(
            'AdminTokenLite' => Tools::getAdminTokenLite('AdminModules'),
            'product_ids' => $prd_ids,
        ));
        return $this->display(__FILE__, 'views/templates/admin/pwproducttimer.tpl');
    }

    public function getProductsStatus($id)
    {
        $product = new Product($id);
        $specificPrice = SpecificPrice::getByProductId($product->id);
        $product->specificPrice = $specificPrice;

        $time = new DateTime();

        if ($product->id == '0') {
            return $this->deleteIdFromTable($product->id);
        } elseif (empty($product->specificPrice)) {
            return $this->deleteIdFromTable($product->id);
        } elseif (strtotime($product->specificPrice[0]['to']) <= time()) {
            return $this->deleteIdFromTable($product->id);
        } else
            return $product;
    }

    public function deleteIdFromTable($id)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_ .$this->name.'` WHERE product_id = '.$id);
    }

    public function getProductRandomId()
    {
        $id = Db::getInstance()->getRow('SELECT DISTINCT id_product FROM `' . _DB_PREFIX_ . $this->name . '` ORDER BY RAND()');
        if (!empty($id['id_product'])) {
            return $id['id_product'];
        } else
            return false;
    }



    public function timeFormat($timeTO)
    {
        $time = new DateTime($timeTO);
        $now = strtotime("now");
        $newyear = strtotime($time->format('d M Y'));
        $timeU = $newyear - $now;


        $min = 60;
        $hour = 60 * 60;
        $day = 60 * 60 * 24;

        $r_days = floor ($timeU / $day);
        $r_hours = floor (($timeU - ($r_days * $day))/$hour);
        $r_min = floor (($timeU - ($r_days * $day) - ($r_hours * $hour))/$min);
        $r_sec = (($timeU - ($r_days * $day) - ($r_hours * $hour) - ($r_min * $min)));

        return $times = array(
            'days' => $r_days,
            'hours' => $r_hours,
            'mins' => $r_min,
        );
    }

    public function getRandom()
    {

        if ($this->getProductRandomId()) {
            $id = $this->getProductRandomId();
        } else
            return;

        $product = new Product($id);
        $specificPrice = SpecificPrice::getByProductId($product->id);
        $image = Image::getCover($id);
        $imagePath = $this->context->link->getImageLink($product->link_rewrite, $image['id_image']);
        $product->image  = $imagePath;
        $product->specificPrice = $specificPrice;
        return $product;
    }

    public function hookdisplayHomeProductTimer($params)
    {

        $prd = $this->getRandom();
        $this->smarty->assign(array(
            'product' => $prd,
            'time' => $this->timeFormat($prd->specificPrice[0]['to']),
        ));
        if (empty($prd)) {
            return;
        } else
            return $this->display(__FILE__, 'views/templates/hook/pwproducttimerView.tpl');

    }

    public function hookDisplayHome ($params) 
    {
        $ids = Db::getInstance()->executeS('SELECT * FROM '. _DB_PREFIX_ . $this->name);
        foreach ($ids as $id) {
            $sp = SpecificPrice::getByProductId($id['id_product']);

            $productTime = strtotime($sp[0]['to'].'+05:00');
            $time = time();
            if ($productTime < $time) {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.$this->name. ' WHERE id_product='.$id['id_product']);
            }
        }
        return self::hookdisplayHomeProductTimer($params);
    }

}


