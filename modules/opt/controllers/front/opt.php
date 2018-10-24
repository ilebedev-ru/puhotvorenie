<?php

if (!defined('_PS_VERSION_'))
    exit;

class optOptModuleFrontController extends ModuleFrontController
{	
	public function postProcess(){

        if(Tools::getValue('submitMessage')){
            //if(Tools::getValue('submitMessage')){
                $fio = Tools::getValue('fio');
                $email = Tools::getValue('email');
                $phone = Tools::getValue('phone');
                $comment = Tools::getValue('comment');
                $data = Array(
                    '{fio}' => $fio,
                    '{email}' => $email,
                    '{phone}'  => $phone,
                    '{comment}' => $comment
                );
                if(empty($fio)) $this->errors[] = "Укажите ваше имя";
                if(empty($phone)) $this->errors[] = "Укажите ваш телефон";
                if(empty($email)) $this->errors[] = "Укажите вашу почту";
                if(empty($this->errors)){
                    $to = Configuration::get('PS_SHOP_EMAIL');
                    // $to = 'eugene.sh94@gmail.com';
                    //$to = 'ja200714@gmail.com';
                    if(Mail::Send($this->context->language->id, 'opt', 'Заявка на опт', $data, $to, 'Администратору сайта', $email, $fio)) die(json_encode('ok'));
                    else die('{"hasError" : true, "errors" : ["'.implode('\',\'', Array('Произошла ошибка при отправке заявки')).'"]}');
                } else die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
           // }
        }
        if(Tools::isSubmit('getPrice')){
            header("Content-Type: application/csv");
            header('Content-Disposition: attachment; filename="pricelist.csv"');
            /*TODO: кеширование прайс листа*/
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT p.*, pl.*
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
                ($childrens_string ? ' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`)' : '').'
                WHERE pl.`id_lang` = '.(int)($this->cookie->id_lang).
                ' AND p.`active` = 1
                ORDER BY pl.`name` ASC, p.price ASC LIMIT 20000');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT p.*, pl.*
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
                ($childrens_string ? ' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`)' : '').'
                WHERE pl.`id_lang` = '.(int)($this->context->language->id).
                ' AND p.`active` = 1
                ORDER BY pl.`name` ASC, p.price ASC LIMIT 20000');
            $d = ";";
            $out =  Configuration::get('PS_SHOP_NAME').$d."http://".Configuration::get('PS_SHOP_DOMAIN')."\n".
                Configuration::get('PS_SHOP_PHONE').$d.Configuration::get('PS_SHOP_PHONE2')."\n\n\n".
                "Артикул".$d."Название".$d."Розница".$d."Оптовая цена".$d."Ссылка"."\n";
            foreach($result as $val){
                $opt_small = Tools::displayPrice($val['opt'] <= 0 ? $val['price'] : $val['opt']);
                $val['price'] = Tools::displayPrice($val['price']);
                $out.= $val['reference'].$d.$val['name'].$d.$val['price'].$d.$opt_small.$d.self::$link->getProductLink($val['id_product'], $val['link_rewrite'])."\n";

            }
            
            echo iconv("UTF-8","Windows-1251", $out);die();
        }
    }
	
    public function displayContent()
    {
        // // $cms = new CMS(11, $this->cookie->id_lang);
        // $this->context->smarty->assign(Array('cmscontent' => $cms->content));
        
        // return $this->setTemplate('opt.tpl');
    }

	public function initContent()
	{
        parent::initContent();

		// // $cms = new CMS(11, $this->cookie->id_lang);
		// $this->context->smarty->assign(Array('cmscontent' => $cms->content));
        
        return $this->setTemplate('opt.tpl');
	}
}


