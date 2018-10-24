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

if (!defined('_PS_VERSION_')){
    exit;
}

class PWRelPrevNext extends Module
{
    public $next;
    public $prev;

    public function __construct()
    {
        $this->name = "pwrelprevnext";
        $this->tab = 'seo';
        $this->version = '1.2.4';
        $this->author = 'Prestaweb';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '12767ab733454f92bf74b04fad8f321f';

        parent::__construct();

        $this->displayName = $this->l("Rel prev/next");
        $this->description = $this->l("Add rel=prev/next to product listings");
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook(array('displayFooter'))) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit'.$this->name))
        {
            Configuration::updateValue('PWRELPREVNEXT_ENABLE_REL', Tools::getValue('PWRELPREVNEXT_ENABLE_REL'));
            Configuration::updateValue('PWRELPREVNEXT_ENABLE_CANONICAL', Tools::getValue('PWRELPREVNEXT_ENABLE_CANONICAL'));
            $this->context->controller->confirmations[] = $this->l('Settings updated');
        }
        return $this->displayForm();
    }

    public function displayForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Включить prev и next rel'),
                    'name' => 'PWRELPREVNEXT_ENABLE_REL',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'PWRELPREVNEXT_ENABLE_REL_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'PWRELPREVNEXT_ENABLE_REL_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Включить канонические url'),
                    'name' => 'PWRELPREVNEXT_ENABLE_CANONICAL',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'PWRELPREVNEXT_ENABLE_CANONICAL_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'PWRELPREVNEXT_ENABLE_CANONICAL_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
         
        $helper = new HelperForm();
         
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
         
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
         
        // Load current value
        $helper->fields_value = Configuration::getMultiple(array(
            'PWRELPREVNEXT_ENABLE_REL', 'PWRELPREVNEXT_ENABLE_CANONICAL'
        ));
         
        return $helper->generateForm($fields_form);
    }

    
    public function hookDisplayFooter($params)
    {
        if (Configuration::get('PWRELPREVNEXT_ENABLE_REL')) {
            $p = 1;
            if (!empty($this->context->controller->p)) {
                $p = $this->context->controller->p;
            } elseif (Tools::isSubmit('p')) {
                $p = Tools::getValue('p');
            } elseif (Tools::isSubmit('page')) {
                $p = Tools::getValue('page');
            }
            $pages_nb = $this->context->smarty->getTemplateVars('pages_nb');
            if ($this->context->controller instanceof smartblogModuleFrontController) {
                $pages_nb = $this->context->smarty->getTemplateVars('totalpages');
            }
            $requestPage = $this->getPageLink();
            if ($p > 1) {
                $this->prev = $this->goPage($requestPage, $p - 1);
            }
            if ($p < $pages_nb) {
                $this->next = $this->goPage($requestPage, $p + 1);
            }
            $this->context->smarty->assign(array(
                'prev' => $this->prev,
                'next' => $this->next,
            ));
        }
        if (Configuration::get('PWRELPREVNEXT_ENABLE_CANONICAL')) {
            // $canonical_url = preg_replace('/\\?.*$/', '', $_SERVER['REQUEST_URI']);
            $this->context->smarty->assign(array(
                'canonical_url' => $this->getPageLink(),//$canonical_url
            ));
        }
        $ret = $this->display(__FILE__, 'pwrelprevnext.tpl');
        $HOOK_HEADER = $this->context->smarty->getTemplateVars('HOOK_HEADER');
        $this->context->smarty->assign('HOOK_HEADER', $HOOK_HEADER.$ret); //ассижнить из футера в хедер вообще не получилось, даже через указатели. Так что вот так
    }

    public function removeIncorrectGetParams($url) {
        $pattern = '/\?+\W+(\w)/i';
        $replacement = '?$1';
        return preg_replace($pattern, $replacement, $url);
    }
    
    //return pagination link
    private function goPage($link, $p)
    {
        if ($this->context->controller instanceof smartblogCategoryModuleFrontController) {
            $slug = $this->context->smarty->getTemplateVars('cat_link_rewrite');
            if (empty($slug) && !Tools::getValue('id_category')) {
                return smartblog::GetSmartBlogLink('smartblog_list_pagination' , array('page' => $p));
            }
            return smartblog::GetSmartBlogLink('smartblog_category_pagination' , array('id_category' => Tools::getValue('id_category'), 'page' => $p, 'slug' => $slug));
        }
        return $this->context->link->goPage($link, $p);
    }
    
    //return canonical link
    private function getPageLink()
    {
        $link = $this->context->link->getPaginationLink(false, false, false, false, true, false);
        if ($this->context->controller instanceof CategoryController) {
            $link = $this->context->link->getPaginationLink('category', (int)Tools::getValue('id_category'), false, false, true, false);
        } elseif ($this->context->controller instanceof ManufacturerController) {
            $link = $this->context->link->getPaginationLink('manufacturer', (int)Tools::getValue('id_manufacturer'), false, false, true, false);
        } elseif ($this->context->controller instanceof SupplierController) {
            $link = $this->context->link->getPaginationLink('supplier', (int)Tools::getValue('id_supplier'), false, false, true, false);        
        } elseif ($this->context->controller instanceof smartblogCategoryModuleFrontController) {
            $slug = $this->context->smarty->getTemplateVars('cat_link_rewrite');
            if (empty($slug) && !Tools::getValue('id_category')) {
                //return smartblog::GetSmartBlogLink('smartblog_list');
                return smartblog::GetSmartBlogLink('smartblog');

            }
            $link = smartblog::GetSmartBlogLink('smartblog_category', array('id_category' => Tools::getValue('id_category'), 'slug' => $slug));
        } elseif ($this->context->controller instanceof smartblogDetailsModuleFrontController) {
            return ; // Был баг со статьями
        } elseif (isset($this->context->controller->module) && $this->context->controller->module instanceof Module) {
            $route = $this->context->controller->page_name;
            $params = array();
            if (Dispatcher::getInstance()->hasRoute($route)) {
                foreach ($_GET as $keyword => $val) {
                    if (Dispatcher::getInstance()->hasKeyword($route, $this->context->language->id, $keyword)) {
                        $params[$keyword] = $val;
                    }
                }
            }
            try {
                $link = $this->context->link->getModuleLink(Tools::getValue('module'), Tools::getValue('controller'), $params);
            } catch(Exception $e) {
                // $link = $this->context->link->getPaginationLink(false, false, false, false, true, false);
            }
        }
        return rtrim(preg_replace('/controllerUri=\w+&?/i', '', $link), '?');
    }
}
