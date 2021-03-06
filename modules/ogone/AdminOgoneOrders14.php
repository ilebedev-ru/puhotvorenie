<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ogone.php';

class AdminOgoneOrders14 extends AdminTab
{

    public $module = null;

    public $table = 'ogone_tl';

    public $view = 1;

    public $className = 'AdminOgoneOrders14';

    protected $return_code_list = null;

    protected $order_statuses = array();

    public function __construct()
    {
        $this->module = new Ogone();

        parent::__construct();

        $this->context = Context::getContext();

        $this->_conf[1001] = $this->module->l('Order was captured succesfully');
        $this->_conf[1002] = $this->module->l('All orders were captured succesfully');

        foreach (OrderState::getOrderStates((int) $this->context->language->id) as $status) {
            $this->order_statuses[$status['id_order_state']] = $status['name'];
        }

        $this->setFieldsListDefinition();
        $this->setQueryDefault();

    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::getValue('action') === 'capture') {
            $this->processCapture();
        }
    }

    protected function setFieldsListDefinition()
    {

        $this->fieldsDisplay = array(
            'id_order' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 25,
                'filter_key' => 'o!id_order'),
            'id_cart' => array('title' => $this->l('Cart'), 'width' => 25, 'align' => 'center', 'type' => 'integer'),
            'date_add' => array('title' => $this->l('Order date'), 'width' => 35, 'align' => 'right',
                'type' => 'datetime', 'filter_key' => 'a!date_add'),
            'payid' => array('title' => $this->l('payid'), 'width' => 80),
            'status' => array('title' => $this->l('status'), 'width' => 60, 'callback' => 'printPaymentStatus'),
            'osname' => array('title' => $this->l('Status'), 'widthColumn' => 230, 'type' => 'select',
                'select' => $this->order_statuses, 'filter_key' => 'os!id_order_state', 'filter_type' => 'int',
                'width' => 200),
        );

    }

    protected function setQueryDefault()
    {
        $this->_select .= 'tl.payid, tl.status,';
        $this->_select .= 'os.color, osl.name AS osname';

        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'ogone_tl tl ON tl.id_cart = a.id_cart';
        $this->_join .= ' LEFT OUTER JOIN  ' . _DB_PREFIX_ .
        'ogone_tl tl2 ON tl2.id_cart = tl.id_cart AND tl.id_ogone_tl < tl2.id_ogone_tl';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.
            'order_history` oh ON (oh.`id_order` = a.`id_order` AND (oh.`id_order_history` =
            (SELECT MAX(`id_order_history`) FROM `'.
            _DB_PREFIX_.'order_history` moh WHERE moh.`id_order` = a.`id_order` GROUP BY moh.`id_order`)))';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON
            (os.`id_order_state` = oh.`id_order_history`)';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl';
        $this->_join .= ' ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' .
            (int) $this->context->language->id . ')';

        $this->_where .= ' AND ISNULL(tl2.id_ogone_tl) ';

    }

    protected function _displayViewLink($token, $id)
    {
        return $this->displayCaptureLink($token, $id);

    }

    public function displayCaptureLink($token, $id, $name = null)
    {
        list($can_capture, $error) = $this->module->canCapture(new Order($id));
        Context::getContext()->smarty->assign(array(
            'href' => 'index.php?tab=AdminOgoneOrders14&id_order=' . (int) $id . '&action=capture&token=' .
            Tools::getAdminTokenLite('AdminOgoneOrders14'),
            'action' => $this->l('Capture', 'Helper'),
            'disable' => $can_capture,
            'title' => $can_capture ? $this->l('Capture order', 'Helper') : $error,
            'icon' => 'icon-cc',
        ));
        return Context::getContext()->smarty->display(dirname(__FILE__) . '/views/templates/admin/ogone_orders/helpers/list/list_action_capture.tpl');
    }

    public function processCapture()
    {
        $id_order = Tools::getValue('id_order');
        if ($this->tabAccess['edit'] !== '1') {
            $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }

        if (!$id_order) {
            $this->errors[] = Tools::displayError('Invalid order id.');
        }

        $order = new Order((int) $id_order);
        list($result, $message) = $this->module->capture($order);
        if ($result) {
            $this->confirmations[] = sprintf($this->module->l('Order %d captured successfully'), $order->id);
        } else {
            $this->errors[] = sprintf($this->module->l('Error capturing order %d : %s'), $order->id, $message);
        }

        return true;
    }

    public function processBulkCapture()
    {
        /*    if (!Tools::isSubmit('submitBulkcapture'))
        return false;*/
        if ($this->tabAccess['edit'] !== '1') {
            $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }

        if (!is_array($this->boxes) || empty($this->boxes)) {
            return false;
        }

        $errors = array();
        $successes = array();
        foreach ($this->boxes as $id_order) {
            $order = new Order((int) $id_order);
            list($result, $message) = $this->module->capture($order);
            if ($result) {
                $successes[] = sprintf($this->module->l('Order %d captured successfully'), $order->id);
            } else {
                $errors[] = sprintf($this->module->l('Error capturing order %d : %s'), $order->id, $message);
            }

        }
        $this->confirmations = $successes;
        $this->errors = $errors;
        if (!empty($this->confirmations) && empty($this->errors)) {
            Tools::redirectAdmin($this->module->getCurrentIndex() . '&conf=1001&token=' . $this->token);
        }

        return true;
    }

    public function printPaymentStatus($status, $row)
    {
        if ($this->return_code_list === null) {
            $this->return_code_list = $this->module->getReturnCodesList();
        }

        $name = isset($this->return_code_list[$status]) ? $this->return_code_list[$status] : $status;
        list($background_color, $color) = $this->module->getPaymentStatusColor($status);
        $pattern = '<span class="label color_field" style="background-color:%s;color:%s">%s</status>';
        return sprintf($pattern, $background_color, $color, $name);
    }
}
