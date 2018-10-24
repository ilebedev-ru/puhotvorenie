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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) exit;

require_once _PS_MODULE_DIR_.'pwblockhtml/classes/PWBlockHTMLObject.php';

class PWBlockHTML extends Module
{
    private $exists_hook_names;

    public function __construct()
    {
        $this->name          = strtolower(get_class());
        $this->tab           = 'seo';
        $this->tab_class     = 'Admin'.get_class();
        $this->version       = '1.2.0';
        $this->author        = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        $this->context       = Context::getContext();

        $this->exists_hook_names  = $this->getExistsHookNames();

        parent::__construct();

        $this->displayName = $this->l('Блок HTML');
        $this->description = $this->l('Позволяет размещать HTML код с привязкой к хукам.');
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install()
            AND $this->registerHook('displayBackOfficeHeader')
            AND $this->createTab($this->l('Блок HTML'));

    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        $this->deleteTab();
        return parent::uninstall();
    }

    private function createTab($text, $id_parent = 0)
    {
        $langs = Language::getLanguages();

        $tab = new Tab();
        $tab->class_name = $this->tab_class;
        $tab->module = $this->name;
        $tab->id_parent = $id_parent;

        foreach ($langs as $l)
            $tab->name[$l['id_lang']] = $this->l($text);

        if ( $tab->save() ){
            if($id_parent == 0)
                return $this->createTab($this->l('Блок HTML'), $tab->id);
            return true;
        }
        return false;
    }

    private function deleteTab()
    {
        $tab_id = Tab::getIdFromClassName($this->tab_class);
        $tab = new Tab($tab_id);
        $tab->delete();
    }
    
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/pwblockhtml-tab.css');
    }

    public function __call($function_name, $arguments)
    {

        if ( ($hook_name = $this->getHookName($function_name)) )
        {
            $this->context->smarty->assign(array(
                'html_blocks' => PWBlockHTMLObject::getActiveHTMLBlocksByHook($hook_name),
            ));
            return $this->display(__FILE__, 'html_block.tpl');
        }
    }

    private function getHookName($function_name)
    {
        $function_name = strtolower($function_name);

        preg_match('/^hook(.+)/', $function_name, $matches);
        $hook_name = $matches[1];

        if ( in_array($hook_name, $this->exists_hook_names) )
            return $hook_name;

        elseif ( in_array($function_name, $this->exists_hook_names) )
            return $function_name;

        return false;
    }

    private function getExistsHookNames()
    {
        $hook_names = array();

        $hooks   = Hook::getHooks();
        $aliases = Hook::getHookAliasList();

        foreach ($hooks as $hook)
            $hook_names[] = strtolower($hook['name']);

        foreach ($aliases as $alias => $hook_name)
            $hook_names[] = strtolower($alias);

        return $hook_names;
    }
    
    public function getContent()
    {
        Tools::redirectAdmin(Link::getAdminLink($this->tab_class));
    }
}
