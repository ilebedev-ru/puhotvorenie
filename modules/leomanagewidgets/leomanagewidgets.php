<?php
/*
**************************************
**        PrestaShop V1.5.4.x        *
**            LeoAdvanceModule       *
**    http://www.brainos.com         *
**             V 1.0                 *
**    Author-team: Land of coder     *
**************************************
*/
if (!defined('_PS_VERSION_'))
    exit;
include_once(_PS_MODULE_DIR_ . 'leomanagewidgets/libs/Params.php');
include_once(_PS_MODULE_DIR_ . 'leomanagewidgets/classes/LeoManageWidget.php');

class LeoManageWidgets extends Module
{
    public $_html = '';
    public $params = null;
    public $base_config_url = null;

    const INSTALL_SQL_FILE = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';

    public function __construct()
    {
        global $currentIndex;
        $this->name = 'leomanagewidgets';
        $this->tab = 'LeoTheme';
        $this->version = '1.1';
        $this->author = 'LeoTheme';
        $this->secure_key = Tools::encrypt($this->name);

        parent::__construct();

        $this->displayName = $this->l('Leo Manage Widgets');
        $this->description = $this->l('Leo Manage Widgets');
        $this->base_config_url = $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
        $this->params = new LeoHomeParams($this, $this->name);
    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        $this->_clearCache('leomanagewidgets.tpl');
        if (!parent::install() || !$this->registerHook('displayHome') || !$this->registerHook('displayTop') || !$this->registerHook('displayHeaderRight') || !$this->registerHook('displaySlideshow')
            || !$this->registerHook('topNavigation') || !$this->registerHook('displayPromoteTop') || !$this->registerHook('rightColumn') || !$this->registerHook('leftColumn')
            || !$this->registerHook('displayFooter') || !$this->registerHook('displayBottom') || !$this->registerHook('displayContentBottom') || !$this->registerHook('displayFootNav')
            || !$this->registerHook('addproduct')
            || !$this->registerHook('updateproduct')
            || !$this->registerHook('deleteproduct')
            || !$this->registerHook('actionShopDataDuplication')
            || !$this->registerHook('actionObjectLanguageAddAfter')
        )
            return false;

        $return = $this->installTable();

        return $return;
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        $this->_clearCache('leomanagewidgets.tpl');
        if (!parent::uninstall())
            return false;
        return $this->uninstallTable();
    }

    /**
     * Install Table
     * Return boole
     */
    public function installTable()
    {
        if (file_exists(dirname(__FILE__) . "/install/db_sample.php") && file_exists(_PS_MODULE_DIR_ . "leotempcp/DataSample.php")) {
            require_once(_PS_MODULE_DIR_ . "leotempcp/DataSample.php");
            $dataSample = new Datasample();
            return $dataSample->installWidgetsModule(dirname(__FILE__) . "/install/db_sample.php", $this->name);
        }

        if (!file_exists(dirname(__FILE__) . '/install/' . self::INSTALL_SQL_FILE))
            return (false);
        else if (!$sql = file_get_contents(dirname(__FILE__) . '/install/' . self::INSTALL_SQL_FILE))
            return (false);
        $sql = str_replace('ps_', _DB_PREFIX_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query) {
            if (!empty($query)) {
                if (!Db::getInstance()->Execute(trim($query)))
                    return (false);
            }
        }

        $res = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'leomanagewidgets_shop`');
        if ($res) {
            $new_res = true;
            $langs = Language::getLanguages(false);
            foreach ($res as $row) {
                $configs = unserialize(base64_decode($row['configs']));
                foreach ($langs as $lang) {
                    if (isset($configs['content_' . $lang['id_lang']]))
                        $configs['content_' . $lang['id_lang']] = str_replace('modules/leomanagewidgets/', __PS_BASE_URI__ . 'modules/leomanagewidgets/', $configs['content_' . $lang['id_lang']]);
                    if (isset($configs['description_' . $lang['id_lang']]))
                        $configs['description_' . $lang['id_lang']] = str_replace('modules/leomanagewidgets/', __PS_BASE_URI__ . 'modules/leomanagewidgets/', $configs['description_' . $lang['id_lang']]);
                }
                $new_res &= Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'leomanagewidgets_shop` SET configs=\'' . base64_encode(serialize($configs)) . '\'  WHERE `id_leomanagewidgets` = ' . (int)$row['id_leomanagewidgets'] . ' AND id_shop = ' . (int)$row['id_shop']);
            }
        }
        return true;
    }

    /**
     * Uninstall Table
     * return boole
     */
    public function uninstallTable()
    {
        if (!file_exists(dirname(__FILE__) . '/install/' . self::UNINSTALL_SQL_FILE))
            return (false);
        else if (!$sql = file_get_contents(dirname(__FILE__) . '/install/' . self::UNINSTALL_SQL_FILE))
            return (false);
        $sql = str_replace('ps_', _DB_PREFIX_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query)
            if (!Db::getInstance()->Execute(trim($query)))
                return (false);
        return true;
    }

    /**
     * @see Module::getContent()
     */
    public function getContent()
    {
        $this->_html .= '<h2>' . $this->displayName . '.</h2>';
        if (((Tools::isSubmit('deleteAdvance') || Tools::isSubmit('changeStatus')) && Tools::getValue('id_leomanagewidgets'))) {
            //if($this->_postValidation())
            $this->_postProcess();
        }
        $this->_displayForm();
        //echo $this->_html;
        return $this->_html;
    }

    /**
     * @see Module::_displayForm()
     */
    public function _displayForm()
    {
        $msg = Tools::getValue('msg');
        if ($msg) {
            $msg = unserialize(base64_decode($msg));
            if (isset($msg['error']) && $msg['error'])
                $this->_html .= $this->displayError(implode('<br />', $msg['error']));
            if (isset($msg['success']) && $msg['success'])
                $this->_html .= $this->displayConfirmation(implode('<br />', $msg['success']));
        }
        $exception = Tools::getValue('exception');
        $hookModules = LeoManageWidget::getsHook($exception);
        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/assets/admin/style.css');
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/assets/admin/jquery-ui-1.10.3.custom.min.js');
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/assets/admin/script.js');
        require_once(dirname(__FILE__) . '/main_config.php');

    }

    /**
     * @see Module::_postValidation()
     */
    public function _postValidation()
    {
        $errors = array();
        $error = array();
        if (Tools::isSubmit('submitSave')) {
            $task = Tools::getValue('task');
            if ($task == 'tab' || $task == 'carousel') {
                if (!Tools::getValue('itemspage') || !Validate::isUnsignedInt(Tools::getValue('itemspage'))) {
                    $errors[] = $this->l('The field "Items Per Page" invalid');
                }
                if (!Tools::getValue('columns') || !Validate::isUnsignedInt(Tools::getValue('columns'))) {
                    $errors[] = $this->l('The field "Colums In Tab" invalid');
                }
                if (!Tools::getValue('itemstab') || !Validate::isUnsignedInt(Tools::getValue('itemstab'))) {
                    $errors[] = $this->l('The field "Items In Tab" invalid');
                }
            }
            $id_lang_default = (int)(Configuration::get('PS_LANG_DEFAULT'));
            if (!Tools::getValue('title_' . $id_lang_default)) {
                $errors[] = $this->l('The field "Title" invalid');
            }
            if (count($errors)) {
                $error['status'] = 'error';
                $error['msg'] = $this->displayError(implode('<br />', $errors));
            }
        }
        return $error;
    }

    /**
     * @see Module::_postProcess()
     */
    public function _postProcess()
    {
        $errors = array();
        $html = '';
        if (Tools::isSubmit('submitSave')) {
            foreach ($_POST as $key => $row) {
                $_POST[$key] = Tools::getValue($key);
            }
            $id = Tools::getValue('id_leomanagewidgets');
            if ($id)
                $obj = new LeoManageWidget($id);
            else
                $obj = new LeoManageWidget();

            $id_lang_default = (int)(Configuration::get('PS_LANG_DEFAULT'));
            $langs = Language::getLanguages(false);
            $titles = array();
            foreach ($langs as $lang) {
                $titles[$lang['id_lang']] = (Tools::getValue('title_' . $lang['id_lang']) ? Tools::getValue('title_' . $lang['id_lang'], '') : Tools::getValue('title_' . $id_lang_default, ''));
            }
            $obj->title = $titles;
            $obj->configs = $_POST;
            $obj->hook = Tools::getValue('hook');
            $obj->task = Tools::getValue('task');
            $obj->active = Tools::getValue('active_mod');
            $obj->file_names = (Tools::getValue('exceptions') ? explode(',', Tools::getValue('exceptions')) : array());
            $id_shop = Tools::getValue('id_shop');
            if ($id) {
                $res = $obj->update(false, $id_shop);
            } else
                $res = $obj->add(true, false, $id_shop);
            if (!$res) {
                $return['msg'] = $this->l('Save data is error!');
                $return['status'] = 'error';
            } else {
                $this->_clearCache('leomanagewidgets.tpl');
                $this->_html .= $this->displayConfirmation($this->l('Save data successfully.'));
                $return['msg'] = $this->displayConfirmation($this->l('Save data successfully.'));
                $return['status'] = 'success';
            }
            $return['obj'] = $obj;
            return $return;
        } elseif (Tools::isSubmit('deleteAdvance')) {
            $id = Tools::getValue('id_leomanagewidgets');
            if ($id) {
                $obj = new LeoManageWidget($id);
                if (Validate::isLoadedObject($obj)) {
                    $res = $obj->delete();

                    if (!$res)
                        $msg['error'][] = $this->l('Delete data is error!');
                    else {
                        $this->_clearCache('leomanagewidgets.tpl');
                        $msg['success'][] = $this->l('Delete data successfully.');
                    }
                } else {
                    $msg['error'][] = $this->l('Object can\'t loaded.');
                }
            } else {
                $msg['error'][] = $this->l('Object can\'t loaded.');
            }
            Tools::redirectAdmin($this->base_config_url . '&msg=' . base64_encode(serialize($msg)));
        } elseif (Tools::isSubmit('changeStatus')) {
            $id = Tools::getValue('id_leomanagewidgets');
            if ($id) {
                $obj = new LeoManageWidget($id);
                if (Validate::isLoadedObject($obj)) {
                    $res = $obj->toggleStatus();

                    if (!$res)
                        $msg['error'][] = $this->l('Update Status is error!');
                    else {
                        $this->_clearCache('leomanagewidgets.tpl');
                        $msg['success'][] = $this->l('Update Status successfully.');
                    }
                } else {
                    $msg['error'][] = $this->l('Object can\'t loaded.');
                }
            } else {
                $msg['error'][] = $this->l('Object can\'t loaded.');
            }
            Tools::redirectAdmin($this->base_config_url . '&msg=' . base64_encode(serialize($msg)));
        }
        /* Display errors if needed */
        if (count($errors))
            $this->_html .= $this->displayError(implode('<br />', $errors));
    }

    function renderLink($hook, $args)
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" . $_SERVER['HTTP_HOST'] : "http://" . $_SERVER['HTTP_HOST'];

        $html = '
		<div class="pos-right"><a class="addnew" href="javascript:void(0)" title="' . $this->l('Add New') . '">' . $this->l('Add New') . '</a>
		<ul style="display:none;">';
        foreach ($args as $task => $val) {
            $link = $protocol . _MODULE_DIR_ . $this->name . '/popup.php?hook=' . $hook . '&task=' . $task . '&addNew&id_shop=' . $this->context->shop->id . '&id_lang=' . $this->context->language->id . '&token=' . Tools::getValue('token');
            $html .= '<li><a href="' . $link . '" class="fancybox">' . $val . '</a></li>';
        }
        $html .= '
		</ul>
		</div>';
        return $html;
    }

    function renderItem($module)
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" . $_SERVER['HTTP_HOST'] : "http://" . $_SERVER['HTTP_HOST'];
//		$link = $protocol. _MODULE_DIR_. $this->name.'/popup.php?editAdvance&id_leomanagewidgets='.$module['id_leomanagewidgets'].'&id_shop='.$this->context->shop->id.'&id_lang='.$this->context->language->id.'&token='.Tools::getValue('token');
        $link = _MODULE_DIR_ . $this->name . '/popup.php?editAdvance&id_leomanagewidgets=' . $module['id_leomanagewidgets'] . '&id_shop=' . $this->context->shop->id . '&id_lang=' . $this->context->language->id . '&token=' . Tools::getValue('token');
        $html = '
			<div class="module-pos" id="module-' . $module['id_leomanagewidgets'] . '" data-position="' . $module['hook'] . '">
				<div class="edithook">' . $this->displayStatus($module['id_leomanagewidgets'], $module['active']) . '
					<a href="' . $link . '" class="edit fancybox"><i></i></a>
					<a href="' . $this->base_config_url . '&deleteAdvance&id_leomanagewidgets=' . $module['id_leomanagewidgets'] . '" title="' . $this->l('Delete') . '" class="delete"><i></i></a>
				</div>
				<div class="leo-editmodule">
					' . $module['title'][$this->context->language->id] . '
				</div>
			</div>';
        return $html;
    }

    public function displayStatus($id_leomanagewidgets, $active)
    {
        $title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
        $img = ((int)$active == 0 ? 'disabled.gif' : 'enabled.gif');
        $html = '<a href="' . AdminController::$currentIndex .
            '&configure=' . $this->name . '
				&token=' . Tools::getAdminTokenLite('AdminModules') . '
				&changeStatus&id_leomanagewidgets=' . $id_leomanagewidgets . '" title="' . $title . '"><img src="' . _PS_ADMIN_IMG_ . '' . $img . '" alt="" /></a>';
        return $html;
    }

    /**
     * Hook
     */
    public function getContents($data, $obj)
    {
        $configs = $data['configs'];
        $task = $data['task'];
        $results = array();

        if ($task == 'tab') {
            $ordering = $obj->getConfig('ordering', '', $data['id_leomanagewidgets']);
            if (!$ordering)
                $ordering = array('featured' => 0, 'special' => 1, 'new' => 2, 'bestseller' => 3, 'category' => 4);
            asort($ordering);
        } else {
            $ordering = array($obj->getConfig('type', '', $data['id_leomanagewidgets'], 6) => 0);
        }
        foreach ($ordering as $key => $val) {
            if (($obj->getConfig('show_' . $key, '', $data['id_leomanagewidgets']) && $task == 'tab') || ($obj->getConfig('type', '', $data['id_leomanagewidgets']) && $task == 'carousel')) {
                $nb = $obj->getConfig('itemstab', '', $data['id_leomanagewidgets'], 6);
                $itemspage = $obj->getConfig('itemspage', '', $data['id_leomanagewidgets'], 3);
                $columns = $obj->getConfig('columns', '', $data['id_leomanagewidgets'], 3);
                $plimit = $itemspage;
                $order_by = $obj->getConfig('porder', '', $data['id_leomanagewidgets']);
                $order_way = $obj->getConfig('way', '', $data['id_leomanagewidgets']);
                $id_lang = $this->context->language->id;
                $id_categ = (int)$obj->getConfig('id_category', '', $data['id_leomanagewidgets']);
                switch ($key) {
                    case 'featured':
                        $category = new Category($id_categ, (int)Context::getContext()->language->id);
//							$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
                        $products = $category->getProducts($id_lang, 0, $nb, 'position', $order_way);
                        $img = $obj->getConfig('img_featured', '', $data['id_leomanagewidgets'], '');
                        $results[] = array(
                            'products' => $this->leoarray_chunk($products, $plimit),
                            'title' => $this->l('Featured Products'),
                            'task' => $key,
                            'id' => $key,
                            'configs' => $configs,
                            'image' => ($img ? __PS_BASE_URI__ . "modules/" . $this->name . "/img/icons/" . $img : ''),
                            'description' => $obj->getConfig('featured_des_' . $this->context->language->id, '', $data['id_leomanagewidgets'], '')
                        );
                        break;
                    case 'special':
                        $products = Product::getPricesDrop($id_lang, 0, $nb, false, $order_by, $order_way);
                        $img = $obj->getConfig('img_special', '', $data['id_leomanagewidgets'], '');
                        $results[] = array('products' => $this->leoarray_chunk($products, $plimit), 'title' => $this->l('Special Products'),
                            'task' => $key, 'id' => $key, 'configs' => $configs, 'image' => ($img ? __PS_BASE_URI__ . "modules/" . $this->name . "/img/icons/" . $img : '')
                        , 'description' => $obj->getConfig('special_des_' . $this->context->language->id, '', $data['id_leomanagewidgets'], '')
                        );
                        break;
                    case 'new':
                        $products = Product::getNewProducts($id_lang, 0, $nb, false, $order_by, $order_way);
                        $img = $obj->getConfig('img_new', '', $data['id_leomanagewidgets'], '');
                        $results[] = array('products' => $this->leoarray_chunk($products, $plimit), 'title' => $this->l('New Products'),
                            'task' => $key, 'id' => $key, 'configs' => $configs, 'image' => ($img ? __PS_BASE_URI__ . "modules/" . $this->name . "/img/icons/" . $img : '')
                        , 'description' => $obj->getConfig('new_des_' . $this->context->language->id, '', $data['id_leomanagewidgets'], '')
                        );
                        break;
                    case 'bestseller':
                        $products = ProductSale::getBestSales($id_lang, 0, $nb);
                        $img = $obj->getConfig('img_bestseller', '', $data['id_leomanagewidgets'], '');
                        $results[] = array('products' => $this->leoarray_chunk($products, $plimit), 'title' => $this->l('Bestseller Products'),
                            'task' => $key, 'id' => $key, 'configs' => $configs, 'image' => ($img ? __PS_BASE_URI__ . "modules/" . $this->name . "/img/icons/" . $img : '')
                        , 'description' => $obj->getConfig('bestseller_des_' . $this->context->language->id, '', $data['id_leomanagewidgets'], '')
                        );
                        break;
                    case 'category':
                        $catids = $obj->getConfig('catids', '', $data['id_leomanagewidgets'], array());
                        foreach ($catids as $id_category) {
                            $objCate = new Category($id_category, $id_lang);
                            $products = $objCate->getProducts($id_lang, 0, $nb, $order_by, $order_way);
                            $leocblist = $obj->getConfig('leocblist', '', $data['id_leomanagewidgets'], array());
                            $img = '';
                            foreach ($leocblist as $val) {
                                $cat = explode('#@#', $val);
                                if ($cat[0] == $id_category) {
                                    $img = $cat[1];
                                    break;
                                }
                            }
                            $results[] = array('products' => $this->leoarray_chunk($products, $plimit), 'title' => $objCate->name,
                                'task' => $key, 'id' => $key . '_' . $objCate->id, 'configs' => $configs, 'image' => ($img ? __PS_BASE_URI__ . "modules/" . $this->name . "/img/icons/" . $img : '')
                            , 'description' => ''
                            );
                        }
                        break;
                }
            }

        }

        return $results;
    }

    public function leoarray_chunk($products, $plimit)
    {
        if ($products)
            $products = array_chunk($products, $plimit);
        return $products;
    }

    public function processData($hook)
    {
        $datas = LeoManageWidget::getsHook();
        if (!isset($datas[$hook]) || !$datas[$hook])
            return false;
        $results = array();
        $obj = new LeoManageWidget();
        $controller = Dispatcher::getInstance()->getController();
        foreach ($datas[$hook] as &$data) {
            $files_name = $obj->getExceptions($hook, $this->context->shop->id, $data['id_leomanagewidgets']);
            if (!in_array($controller, $files_name)) {
                if (($data['task'] == 'carousel' || $data['task'] == 'tab') && $data['active'] == 1) {
                    $data['title'] = $data['title'][$this->context->language->id];
                    $data['description'] = $obj->getConfig('description_' . $this->context->language->id, '', $data['id_leomanagewidgets'], '');
                    $data['contents'] = $this->getContents($data, $obj);
                    $data['scolumn'] = 12 / $obj->getConfig('columns', '', $data['id_leomanagewidgets'], 3);
                    $results[] = $data;
                } elseif ($data['task'] == 'custom' && $data['active'] == 1) {
                    $data['title'] = $data['title'][$this->context->language->id];
                    $data['contents'] = $obj->getConfig('content_' . $this->context->language->id, '', $data['id_leomanagewidgets'], '');
                    $results[] = $data;
                }
            }
        }
        //echo "<pre>".print_r($results,1); die;
        return $results;
    }

    public function assignTpl($hook)
    {
        $dir = dirname(__FILE__) . "/themes/tab_products.tpl";
        $tdir = _PS_ALL_THEMES_DIR_ . _THEME_NAME_ . '/modules/' . $this->name . '/themes/' . $hook . '/tab_products.tpl';
        if (file_exists($tdir))
            $dir = $tdir;
        elseif (file_exists(_PS_ALL_THEMES_DIR_ . _THEME_NAME_ . '/modules/' . $this->name . '/themes/tab_products.tpl')) {
            $dir = _PS_ALL_THEMES_DIR_ . _THEME_NAME_ . '/modules/' . $this->name . '/themes/tab_products.tpl';
        } elseif (file_exists(dirname(__FILE__) . "/themes/" . $hook . "/tab_products.tpl")) {
            $dir = dirname(__FILE__) . "/themes/" . $hook . "/tab_products.tpl";
        }
        $this->smarty->assign('tab_product_tpl', $dir);

        $dir = dirname(__FILE__) . "/themes/carousel_products.tpl";
        $tdir = _PS_ALL_THEMES_DIR_ . _THEME_NAME_ . '/modules/' . $this->name . '/themes/' . $hook . '/carousel_products.tpl';
        if (file_exists($tdir))
            $dir = $tdir;
        elseif (file_exists(_PS_ALL_THEMES_DIR_ . _THEME_NAME_ . '/modules/' . $this->name . '/themes/carousel_products.tpl')) {
            $dir = _PS_ALL_THEMES_DIR_ . _THEME_NAME_ . '/modules/' . $this->name . '/themes/carousel_products.tpl';
        } elseif (file_exists(dirname(__FILE__) . "/themes/" . $hook . "/carousel_products.tpl")) {
            $dir = dirname(__FILE__) . "/themes/" . $hook . "/carousel_products.tpl";
        }
        $this->smarty->assign('carousel_product_tpl', $dir);
    }

    public function hookDisplayHome($params, $hook = 'displayHome')
    {
        $controller = Dispatcher::getInstance()->getController();
        $this->context->controller->addCSS($this->_path . 'assets/style.css');
        $tmpl = $this->getTmpl($hook);
        if (!$this->isCached($tmpl, $this->getCacheId(null, $hook, $controller))) {
            $datas = $this->processData($hook);
            if (!$datas)
                return;
            $this->smarty->assign('leomanagewidgets_datas', $datas);
            $this->smarty->assign('hook', $hook);
            $this->assignTpl($hook);
        }

        return $this->display(__FILE__, $tmpl, $this->getCacheId(null, $hook, $controller));
    }

    public function hookDisplayTop($params)
    {
        return $this->hookDisplayHome($params, 'displayTop');
    }

    public function hookDisplayHeaderRight($params)
    {
        return $this->hookDisplayHome($params, 'displayHeaderRight');
    }

    public function hookDisplaySlideshow($params)
    {
        return $this->hookDisplayHome($params, 'displaySlideshow');
    }

    public function hookTopNavigation($params)
    {
        return $this->hookDisplayHome($params, 'topNavigation');
    }

    public function hookDisplayPromoteTop($params)
    {
        return $this->hookDisplayHome($params, 'displayPromoteTop');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayHome($params, 'displayRightColumn');
    }

    public function hookDisplayLeftColumn($params)
    {
        return $this->hookDisplayHome($params, 'displayLeftColumn');
    }

    public function hookDisplayFooter($params)
    {
        return $this->hookDisplayHome($params, 'displayFooter');
    }

    public function hookDisplayBottom($params)
    {
        return $this->hookDisplayHome($params, 'displayBottom');
    }

    public function hookDisplayContentBottom($params)
    {
        return $this->hookDisplayHome($params, 'displayContentBottom');
    }

    public function hookDisplayFootNav($params)
    {
        return $this->hookDisplayHome($params, 'displayFootNav');
    }

    public function hookActionShopDataDuplication($params)
    {
        Db::getInstance()->execute('
		INSERT IGNORE INTO ' . _DB_PREFIX_ . 'leomanagewidgets_shop (id_leomanagewidgets, id_shop, position, title, configs)
		SELECT id_leomanagewidgets, ' . (int)$params['new_id_shop'] . ', position, title, configs
		FROM ' . _DB_PREFIX_ . 'leomanagewidgets_shop
		WHERE id_shop = ' . (int)$params['old_id_shop']);

        Db::getInstance()->execute('
		INSERT IGNORE INTO ' . _DB_PREFIX_ . 'leomanagewidgets_exceptions (id_leomanagewidgets, id_shop, hook, file_name)
		SELECT id_leomanagewidgets, ' . (int)$params['new_id_shop'] . ', hook, file_name
		FROM ' . _DB_PREFIX_ . 'leomanagewidgets_exceptions
		WHERE id_shop = ' . (int)$params['old_id_shop']);

        $this->_clearCache("leomanagewidgets.tpl");
    }

    protected function getCacheId($name = null, $hook = '', $controller = '')
    {
        $cache_array = array(
            $name !== null ? $name : $this->name,
            $hook,
            $controller,
            date('Ymd'),
            (int)Tools::usingSecureMode(),
            (int)$this->context->shop->id,
            (int)Group::getCurrent()->id,
            (int)$this->context->language->id,
            (int)$this->context->currency->id,
            (int)$this->context->country->id
        );
        return implode('|', $cache_array);
    }

    public function hookAddProduct($params)
    {
        $this->_clearCache("leomanagewidgets.tpl");
    }

    public function hookUpdateProduct($params)
    {
        $this->_clearCache("leomanagewidgets.tpl");
    }

    public function hookDeleteProduct($params)
    {
        $this->_clearCache("leomanagewidgets.tpl");
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        $new_res = true;
        $field_name = array('content', 'title', 'description');
        $object = $params['object'];
        $res = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'leomanagewidgets_shop`');
        if ($res)
            foreach ($res as &$row) {
                $title = unserialize(base64_decode($row['title']));
                $configs = unserialize(base64_decode($row['configs']));
                $new_val = '';
                if ($title)
                    foreach ($title as $key => $val) {
                        $new_title[$key] = $val;
                        if ($key == Configuration::get('PS_LANG_DEFAULT')) {
                            $new_val = $val;
                        }
                    }
                $new_title[$object->id] = $new_val;
                $new_configs = array();
                if ($configs)
                    foreach ($configs as $key2 => $conf) {
                        $new_configs[$key2] = $conf;
                        foreach ($field_name as $field) {
                            if ($field . '_' . Configuration::get('PS_LANG_DEFAULT') == $key2) {
                                $new_configs[$field . '_' . $object->id] = $configs[$field . '_' . Configuration::get('PS_LANG_DEFAULT')];
                            }
                        }
                    }
                $new_res &= Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'leomanagewidgets_shop` SET title=\'' . base64_encode(serialize($new_title)) . '\', configs=\'' . base64_encode(serialize($new_configs)) . '\'  WHERE `id_leomanagewidgets` = ' . (int)$row['id_leomanagewidgets'] . ' AND id_shop = ' . (int)$row['id_shop']);
            }
        $this->_clearCache("leomanagewidgets.tpl");
        return $new_res;
    }

    public function getTmpl($hook)
    {
        $tmpl = "/themes/leomanagewidgets.tpl";
        if (file_exists(dirname(__FILE__) . "/themes/" . $hook . "/leomanagewidgets.tpl")) {
            $tmpl = "/themes/" . $hook . "/leomanagewidgets.tpl";
        }
        return $tmpl;
    }

    public function getFolderAdmin()
    {
        $folders = array('cache', 'classes', 'config', 'controllers', 'css', 'docs', 'download', 'img', 'js', 'localization', 'log', 'mails',
            'modules', 'override', 'themes', 'tools', 'translations', 'upload', 'webservice', '.', '..');
        $handle = opendir(_PS_ROOT_DIR_);
        if (!$handle) {
            return false;
        }
        while (false !== ($folder = readdir($handle))) {
            if (is_dir(_PS_ROOT_DIR_ . '/' . $folder)) {
                if (!in_array($folder, $folders)) {
                    $folderadmin = opendir(_PS_ROOT_DIR_ . '/' . $folder);
                    if (!$folderadmin)
                        return $folder;
                    while (false !== ($file = readdir($folderadmin))) {
                        if (is_file(_PS_ROOT_DIR_ . '/' . $folder . '/' . $file) && ($file == 'header.inc.php')) {
                            return $folder;
                        }
                    }
                }
            }
        }
        return $false;
    }

    public function displayFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
    {
        if (count($languages) == 1)
            return false;

        $output = '
		<div class="displayed_flag">
			<img src="' . __PS_BASE_URI__ . 'img/l/' . $default_language . '.jpg" class="pointer" id="language_current_' . $id . '" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_' . $id . '" class="language_flags">
			' . $this->l('Choose language:') . '<br /><br />';
        foreach ($languages as $language)
            if ($use_vars_instead_of_ids)
                $output .= '<img src="' . __PS_BASE_URI__ . 'img/l/' . (int)$language['id_lang'] . '.jpg" class="pointer" alt="' . $language['name'] . '" title="' . $language['name'] . '" onclick="changeLofLanguage(\'' . $id . '\', ' . $ids . ', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" /> ';
            else
                $output .= '<img src="' . __PS_BASE_URI__ . 'img/l/' . (int)$language['id_lang'] . '.jpg" class="pointer" alt="' . $language['name'] . '" title="' . $language['name'] . '" onclick="changeLofLanguage(\'' . $id . '\', \'' . $ids . '\', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" /> ';
        $output .= '</div>';

        if ($return)
            return $output;
        echo $output;
    }
}