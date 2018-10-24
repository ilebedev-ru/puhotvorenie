<?php
if (!defined('_PS_VERSION_'))
    exit;
class pwproductlabel extends Module
{

    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = "0.1.1";
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = "Отображает лейблы";
        $this->description = "Интегрирован в класс товаров";
    }

    public function install()
    {
        if ( !parent::install()
            OR !$this->registerHook(Array(
                'displayAdminProductsExtra',
                'actionAdminProductsControllerSaveAfter',
            ))

        ) return false;
        return true;
    }

    //start_adminproducthook
    public function hookActionAdminProductsControllerSaveAfter($params)
    {
        $product = $params['return'];
        if ($product->id AND Tools::getValue('id_label')) {
            $product->id_label = Tools::getValue('id_label');
            $product->save();
        }
    }

    public function hookdisplayAdminProductsExtra($params)
    {
        $product = new Product(Tools::getValue('id_product'));
        $this->context->smarty->assign(Array(
            'obj' => $product
        ));
        return $this->display(__FILE__, 'adminproducthook.tpl');
    }
    //end_adminproducthook
}