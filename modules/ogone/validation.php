<?php
/**
 * 2007-2016 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__) . '/../../config/config.inc.php';

/**
 * @var ogone
 */
$ogone = Module::getInstanceByName('ogone');

$ogone->log('Validation.php called : ' . var_export($_GET, true));

$params = '<br /><br />' . $ogone->l('Received parameters:') . '<br /><br />' . PHP_EOL;

$secure_key = Tools::getIsset('secure_key') ? Tools::getValue('secure_key') : '';
$ogone->log('secure_key : ' . $secure_key);

$sha_sign_received = Tools::getIsset('SHASIGN') ? Tools::getValue('SHASIGN') : '';

foreach ($ogone->getNeededKeyList() as $k) {
    if (!Tools::getIsset($k)) {
        $msg = $ogone->l('Missing parameter:') . ' ' . $k;
        $ogone->log($msg);
        die($msg);
    } else {
        $params .= Tools::safeOutput($k) . ' : ' . Tools::safeOutput(Tools::getValue($k)) . '<br />' . PHP_EOL;
    }
}

$ogone->log('PARAMS: ' . $params);

/* Fist, check for a valid SHA-1 signature */
$ogone_params = array();
$ignore_key_list = $ogone->getIgnoreKeyList();

foreach ($_GET as $key => $value) {
    if (Tools::strtoupper($key) != 'SHASIGN' && $value != '' && !in_array($key, $ignore_key_list)) {
        $ogone_params[Tools::strtoupper($key)] = $value;
    }
}

$id_cart = (int) $ogone_params['ORDERID'];
$ogone->log('ID CART : ' . $id_cart);

/* Then, load the customer cart and perform some checks */
$cart = new Cart($id_cart);

if ($ogone->setShopContext($cart)) {
    $ogone->log('Shop context switched to : ' . Context::getContext()->shop->id);
}

if (!$ogone->active) {
    die($ogone->l('Module is desactivated'));
}

if (Validate::isLoadedObject($cart)) {

    if (!Configuration::get('OGONE_SHA_OUT')) {
        die($ogone->l('Invalid value of variable OGONE_SHA_OUT'));
    }

    $sha1 = $ogone->calculateShaSign($ogone_params, Configuration::get('OGONE_SHA_OUT'));
    $ogone->log('SHA CALCULATED : ' . $sha1);
    $ogone->log('SHA RECEIVED : ' . $sha_sign_received);

    if ($sha_sign_received && $sha1 == $sha_sign_received) {

        $ogone_return_code = (int) $ogone_params['STATUS'];
        $ogone->log('ogone_return_code : ' . $ogone_return_code);

        $existing_id_order = (int) Order::getOrderByCartId($id_cart);
        $ogone->log('existing_id_order : ' . $existing_id_order);

        $ogone_state = $ogone->getCodePaymentStatus($ogone_return_code);
        $ogone->log('ogone_state : ' . $ogone_state);

        $ogone_state_description = $ogone->getCodeDescription($ogone_return_code);
        $ogone->log('ogone_state_description : ' . $ogone_state_description);

        $payment_state_id = $ogone->getPaymentStatusId($ogone_state);
        $ogone->log('payment_state_id : ' . $payment_state_id);

        $amount_paid = ($ogone_state === Ogone::PAYMENT_ACCEPTED || $ogone_state === Ogone::PAYMENT_AUTHORIZED
            || $ogone_state === Ogone::PAYMENT_IN_PROGRESS  ?
            (float) $ogone_params['AMOUNT'] :
            0);
        $ogone->log('amount_paid : ' . $amount_paid);

        $ogone->addTransactionLog($id_cart, $existing_id_order, 0, $ogone_params);

        if ($existing_id_order) {
            $order = new Order($existing_id_order);

            /* Update the amount really paid */
            if ($order->total_paid_real !== $amount_paid) {
                $ogone->log('order->total_paid_real : ' . $order->total_paid_real);

                $order->total_paid_real = $amount_paid;
                $order->update();
            }
            $ogone->log('Adding history');
            /* Send a new message and change the state */
            $history = new OrderHistory();
            $history->id_order = (int) $existing_id_order;
            $history->changeIdOrderState($payment_state_id, (int) $existing_id_order);
            $history->addWithemail(true, array());

            /* Add message */
            $message = sprintf(
                '%s: %d %s %s %f',
                $ogone->l('Ogone update'),
                $ogone_return_code,
                $ogone_state,
                $ogone_state_description,
                $amount_paid
            );
            $ogone->log($message);
            $ogone->addMessage($existing_id_order, $message);
            $ogone->log('MESSAGE ADDED');
        } else {
            $message = sprintf('%s %s %s', $ogone_state_description, Tools::safeOutput($ogone_state), $params);
            $ogone->log($message);
            $ogone->log('Validating order, state ' . $payment_state_id);
            $result = $ogone->validate(
                (int) $ogone_params['ORDERID'],
                $payment_state_id,
                $amount_paid,
                $message,
                Tools::safeOutput($secure_key)
            );
            $ogone->log('Order validate result ' . ($result ? 'OK' : 'FAIL'));

        }
    } else {
        $message = $ogone->l('Invalid SHA-1 signature') . '<br />' . $ogone->l('SHA-1 given:') . ' ' .
            Tools::safeOutput($sha_sign_received) . '<br />' .$ogone->l('SHA-1 calculated:') . ' ' .
            Tools::safeOutput($sha1) . '<br />' . $ogone->l('Params: ') . ' ' . Tools::safeOutput($params);
        $ogone->log($message);
        $ogone->log($params);
        $ogone->log('Validating order, state ' . Configuration::get('PS_OS_ERROR'));
        $result = $ogone->validate(
            (int) $ogone_params['ORDERID'],
            Configuration::get('PS_OS_ERROR'),
            0,
            $message . '<br />' . $params,
            Tools::safeOutput($secure_key)
        );
        $ogone->log('Order validate result ' . ($result ? 'OK' : 'FAIL'));

    }
}
