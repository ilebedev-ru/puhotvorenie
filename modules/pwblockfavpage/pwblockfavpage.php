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

if (!defined('_PS_VERSION_')) {
    exit;
}

/** @changelog **
 * 22.04.2016 - все переделали, версия 1.3
 * 27/07 - убрал домен в ссылках, при переносе доставляло неудобство
 */

class PWBlockFavPage extends Module
{
    private $data;
    private $errors;
    private $success;

    public function __construct()
    {
        $this->name          = 'pwblockfavpage';
        $this->tab           = 'front_office_features';
        $this->version       = 1.4;
        $this->author        = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        $this->data          = $this->getData();
        $this->errors        = array();
        $this->success       = false;

        parent::__construct();

        $this->displayName = $this->l('Простое меню');
        $this->description = $this->l('Добавляет блок ссылок.');
    }

    public function install()
    {
        return parent::install() AND $this->registerHook(array(
            'header', 'actionAdminControllerSetMedia', 'displayNav',
        )) AND $this->makeDefault();
    }

    public function uninstall()
    {
        Configuration::deleteByName('PW_BLOCK_FAV_PAGE_LINKS');
        return parent::uninstall();
    }

    private function makeDefault()
    {
        $pages = CMS::getCMSPages($this->context->cookie->id_lang, 1);


        $data['block_name'] = 'Меню';
        foreach ($pages as $page)
            $data['links'][] =
                array(
                    'url'  => str_replace('http://'.$this->context->shop->domain, '', $this->context->link->getCMSLink($page['id_cms'], $page['link_rewrite'])),
                    'name' => $page['meta_title'],
                );

        if ( !$this->setData($data) ) return false;
        return true;
    }

    public function getData()
    {
        if ( ($data = Configuration::get('PW_BLOCK_FAV_PAGE_LINKS')) )
            return (array) Tools::jsonDecode($data, true);
        else return array('block_name' => '', 'links' => array());
    }

    public function setData($data)
    {
        $this->data = $data;

        try {
            Configuration::updateValue('PW_BLOCK_FAV_PAGE_LINKS', Tools::jsonEncode($this->data));
            $this->success = true;
        }
        catch (Exception $e) {
            $this->errors[] = Tools::displayError('Не удалось сохранить значения. ').$e->getMessage();
            return false;
        }
        return true;
    }

    public function getContent()
    {
        if ( Tools::isSubmit('submitPWBlockFavPageModule') )
            $this->postProcess();

        $this->context->smarty->assign(array(
            'data'    => Tools::jsonDecode(Configuration::get('PW_BLOCK_FAV_PAGE_LINKS')),
            'errors'  => $this->errors,
            'success' => $this->success,
        ));

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        return $output;
    }

    protected function postProcess()
    {
        $values = Tools::getAllValues();

        if ( isset($values['link-name']) AND $values['link-name'] AND isset($values['link-url']) AND $values['link-url'] )
        {
            $links = array();
            foreach ($values['link-name'] as $key => $link_name)
            {
                if ( $values['link-name'][$key] AND $values['link-url'][$key] )
                    $links[] = array(
                        'name' => $values['link-name'][$key],
                        'url'  => $values['link-url'][$key],
                    );
                elseif ( ($key+1) != count($values['link-name']) )
                {
                    // проверка $key потому что в форме есть скрытое поле для клонирования всегда пустое
                    $this->errors[] = Tools::displayError('У ссылки нужно указать и Название и URL');
                    return;
                }
            }

            $block_name = '';
            if ( isset($values['block-name']) AND $values['block-name'] ) $block_name = $values['block-name'];

            $this->setData(array(
                'block_name' => $block_name,
                'links'      => $links,
            ));
        }

    }

    public function hookActionAdminControllerSetMedia()
    {
        if ( Tools::getValue('module_name') == $this->name )
            $this->context->controller->addJS($this->_path.'views/js/back.js');
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayNav()
    {
        $this->context->smarty->assign(array('name' => $this->data['block_name'], 'links' => $this->data['links']));
        return $this->display(__FILE__, $this->name.'-top.tpl');
    }
    public function hookdisplayTop()
    {
        return $this->hookDisplayNav();
    }

}
