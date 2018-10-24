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

class OgoneAliasesModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        parent::initContent();
        $this->dispatch();
    }

    protected function dispatch()
    {
        if (!$this->context->customer || !$this->context->customer->isLogged()) {
            $url = 'index.php?controller=authentication&back=' .
                urlencode($this->context->link->getModuleLink('ogone', 'aliases'));
            Tools::redirect($url);
        } elseif (Tools::getValue('result')) {
            return $this->processAliasCreationReturn();
        } elseif (Tools::getValue('action') === 'delete' && Tools::getValue('id_alias')) {
            $this->processDelete();
        }

        $this->assignList();
    }

    protected function processAliasCreationReturn()
    {
        $tpl_vars = array();
        $this->display_header = false;
        $this->display_footer = false;
        if (Tools::getValue('result') == 'ok' && Tools::getValue('alias')) {
            $data = $this->getAliasReturnVariables();
            list($result, $message) = $this->module->createAlias($this->context->customer->id, $data);
            if ($result) {
                $this->setTemplate('parent-reload.tpl');
                return true;
            } else {
                $tpl_vars['error'] = $message;
            }

        } else {
            $tpl_vars['error'] = Tools::getValue('NCError') ?
                Tools::getValue('NCError') :
                $this->module->l('Alias creation error');
        }

        $this->context->smarty->assign($tpl_vars);
        $this->setTemplate('aliases-error.tpl');

    }

    protected function getAliasReturnVariables()
    {
        $data = array();
        $data['ALIAS'] = Tools::getValue('alias');
        $data['CARDNO'] = Tools::getValue('CardNo');
        $data['CN'] = Tools::getValue('CN');
        $data['ED'] = Tools::getValue('ED');
        $data['BRAND'] = Tools::getValue('Brand');
        $data['NCERROR'] = Tools::getValue('NCError');
        $data['STATUS'] = Tools::getValue('status');
        if (Tools::getIsset('StorePermanently')) {
            $data['STOREPERMANENTLY'] = Tools::getValue('StorePermanently');
        }

        $data['SHASIGN'] = Tools::getValue('SHASign');
        return $data;
    }

    protected function assignList()
    {
        $customer = $this->context->customer;
        $module_url = $this->context->link->getModuleLink('ogone', 'aliases');
        if (!$customer || !$customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($module_url));
        }

        if ($this->module->canUseAliases()) {
            $aliases = array();
            foreach (OgoneAlias::getCustomerActiveAliases($customer->id) as $alias) {
                $alias['delete_link'] = $this->context->link->getModuleLink(
                    'ogone',
                    'aliases',
                    array('action' => 'delete', 'id_alias' => $alias['id_ogone_alias'])
                );
                $alias['logo'] = $this->module->getAliasLogoUrl($alias, 'cc_small.png');
                $aliases[] = $alias;
            }
            $tpl_vars = array(
                'url' => $module_url,
                'aliases' => $aliases,
                'htp_url' => $this->module->getHostedTokenizationPageRegistrationUrl($customer->id),
            );
            $this->context->smarty->assign($tpl_vars);
            $this->setTemplate('aliases.tpl');
        } else {
            $this->setTemplate('aliases-disabled.tpl');
        }

    }

    public function processDelete()
    {
        $id_alias = (int) Tools::getValue('id_alias');
        $alias = new OgoneAlias((int) $id_alias);
        if (!Validate::isLoadedObject($alias) || (int) $alias->id_customer !== (int) $this->context->customer->id) {
            $this->context->smarty->assign('errors', array($this->module->l('Invalid customer')));
            return false;
        } else {
            if ($alias->delete()) {
                $this->context->smarty->assign('messages', array($this->module->l('Alias deleted')));
                return true;
            }
        }

        $this->context->smarty->assign('errors', array($this->module->l('Unable delete alias')));
        return false;

    }
}
