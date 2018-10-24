<?php
/**
* Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
*
* @category  Front Office Features
* @package   Yandex Payment Solution
* @author    Yandex.Money <cms@yamoney.ru>
* @copyright © 2015 NBCO Yandex.Money LLC
* @license   https://money.yandex.ru/doc.xml?id=527052
*/
class AdminOrdersController extends AdminOrdersControllerCore
{
    /*
    * module: yamodule
    * date: 2016-10-17 10:06:43
    * version: 1.3.6.1
    */
    public function displayReturnsLink($token, $id)
    {
        return '<a href="'.$this->context->link->getAdminLink('AdminOrders').'&token='.$token
            .'&id_order='.$id.'&viewReturns"><i class="icon-gift"></i> Возвраты</a>';
    }
    /*
    * module: yamodule
    * date: 2016-10-17 10:06:43
    * version: 1.3.6.1
    */
    public function renderList()
    {
        if (Tools::isSubmit('viewReturns')) {
            $id_order = Tools::getValue('id_order', 0);
            if ($id_order) {
                $module = new Yamodule();
                $params = array('order' => new Order($id_order));
                $this->content .= $module->displayReturnsContentTabs($params);
                $this->content .= $module->displayReturnsContent($params);
            } else {
                $this->errors[] = $this->l('There is no order number!');
            }
        } else {
            return parent::renderList();
        }
    }
}
