<?php
include_once('PWModuleFrontController.php');
class PWDeveloperLinksModuleFrontController extends PWModuleFrontController
{
    public function initContent() 
	{
        parent::initContent();
        $generatedLink = null;
        if (Tools::getValue('confirmationLink')) {
            $id_order = (int)Tools::getValue('confirmationLink');
            $generatedLink = $this->getOrderConfirmationLink($id_order);
        }
        if (Tools::getValue('id_category')) {
            $id_category = (int)Tools::getValue('id_category');
            $generatedLink = $this->getCategoryLink($id_category);
        }
        if (Tools::getValue('id_product')) {
            $id_product = (int)Tools::getValue('id_product');
            $generatedLink = $this->getProductLink($id_product);
        }
        if (Tools::getValue('module_name') && Tools::getValue('controller_name')) {
            $module_name = Tools::getValue('module_name');
            $controller = Tools::getValue('controller_name');
            $params = Tools::getValue('addMod')?parse_str(Tools::getValue('addMod')):array();
            $generatedLink = Context::getContext()->link->getModuleLink($module_name, $controller, $params);
        }
        $this->context->smarty->assign(array(
            'generatedLink' => $generatedLink,
        ));
		$this->setTemplate('links.tpl');
    }
    
    private function getProductLink($id_product = null)
    {
        return Context::getContext()->link->getProductLink($id_product);
    }
    
    private function getCategoryLink($id_category = null)
    {
        $category = new Category($id_category, Context::getContext()->language->id);
        return Context::getContext()->link->getCategoryLink($id_category, $category->link_rewrite);
    }
    
    private function getOrderConfirmationLink($id_order = null)
    {
        if (!$id_order) {
            $id_order = Db::getInstance()->getValue('SELECT id_order FROM '._DB_PREFIX_.'orders ORDER BY id_order DESC');
        }
        $order = new Order($id_order);
        $customer = new Customer($order->id_customer);
        $id_module = Module::getModuleIdByName($order->module);
        return Context::getContext()->link->getPageLink(
                'order-confirmation',
                null,
                null,
                'id_order='.$id_order.'&id_module='.$id_module.'&key='.$customer->secure_key.'&id_cart='.$order->id_cart
            );
    }
}