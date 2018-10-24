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

require_once _PS_MODULE_DIR_.'pwblockhtml/classes/PWBlockHTMLObject.php';

class AdminPWBlockHTMLController extends ModuleAdminController
{
    public $module;

    public function __construct()
    {
        $this->table         = 'pwblockhtml';
        $this->className     = 'PWBlockHTMLObject';
        $this->module        = 'pwblockhtml';
        $this->lang          = false;
        $this->bootstrap     = true;
        $this->need_instance = 0;

        $this->context       = Context::getContext();
        $this->values        = $this->getAllValues();
        $this->errors        = array();
        $this->success       = 0;

        if ( $this->context->cookie->pwblockhtml_success )
        {
            $this->success = 1;
            $this->context->cookie->pwblockhtml_success = '';
        }
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();

        $this->fields_list = array(
            'id_pwblockhtml' => array(
                'title'    => $this->l('ID'),
                'type'     => 'text',
                'orderby'  => true
            ),
            'order' => array(
                'title'    => $this->l('Порядок'),
                'type'     => 'text',
            ),
            'name' => array(
                'title'    => $this->l('Название'),
                'type'     => 'text',
            ),
            'hooks' => array(
                'title'    => $this->l('Хуки'),
                'type'     => 'text',
                'callback' => 'getHookNames',
            ),
            'active' => array(
                'title'    => $this->l('Статус'),
                'type'     => 'bool',
                'align'    => 'center',
                'active'   => 'status',

            ),
        );

        $this->addRowAction('delete');
    }

    private function getAllValues()
    {
        return $_GET + $_POST;
    }

    public static function getHookNames($hook_ids_json)
    {
        if ( !($hook_ids = Tools::jsonDecode($hook_ids_json)) ) return '';

        $hook_names = array();
        foreach ($hook_ids as $hook_id)
            $hook_names[] = self::getHookNameById($hook_id);

        return $hook_names ? implode(', ', $hook_names) : '';
    }

    public function renderForm()
    {
        $iso = Language::getIsoById($this->context->cookie->id_lang);
        $iso_tiny_mce = file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
        $shopAsso = '';
        if(!Tools::version_compare(_PS_VERSION_, '1.6') && Shop::isFeatureActive()){
            $helpShop = new HelperTreeShops($this->id, 'Магазины');
            $helpShop->setSelectedShops($this->tshopList());
            $shopAsso = $helpShop->render();
        }
        $this->context->smarty->assign(array(
            'block_html'    => PWBlockHTMLObject::getBlockHTML($this->id_object),
            'hooks'         => Hook::getHooks(),
            'current_hooks' => $this->getCurrentHooks(),
            'ad'            => dirname($_SERVER['PHP_SELF']),
            'iso'           => $iso_tiny_mce,
            'success'       => $this->success,
            'shopAsso'      => $shopAsso,
        ));
        if(Tools::version_compare(_PS_VERSION_, '1.6')){
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.'pwblockhtml/views/templates/admin/add_html_form15.tpl');
        }
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'pwblockhtml/views/templates/admin/add_html_form.tpl');
    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addJquery();
        $this->addJS('/js/tiny_mce/tiny_mce.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/select2.min.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/select2.min.css');
        $this->addJS(_PS_CORE_DIR_.'/js/tiny_mce/tiny_mce.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/vendor/ace/src/ace.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/vendor/emmet/emmet.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/vendor/ace/src/ext-emmet.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/back.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/back.css');
    }
    
    protected function tshopList()
    {
        $res = array();
        if(Tools::isSubmit($this->identifier)){
            $r = Db::getInstance()->executeS('SELECT `id_shop` FROM `'._DB_PREFIX_.$this->table.'_shop` WHERE '.$this->identifier.' = '.Tools::getValue($this->identifier));
            foreach($r as $b){
                $res[] = $b['id_shop'];
            }
        }
        if(!count($res))
            $res[] = $this->context->shop->id;
        return $res;
    }

    public function postProcess()
    {
        if ( Tools::isSubmit('statuspwblockhtml') )
            $this->changeStatusBlockHTML(Tools::getValue('id_pwblockhtml'));

        if ( Tools::isSubmit('deletepwblockhtml') )
            $this->deleteBlockHTML(Tools::getValue('id_pwblockhtml'));

        if ( Tools::getValue('submitAddPWBlockHTML') )
            $this->saveBlockHTML();
    }
    
    /*рекурсивно используем addslashes на все элементы массива*/
    public static function addSlashesArray(&$array)
    {
        foreach($array as &$item){
            if(is_array($item))
                $item = self::addSlashesArray($item);
            else
                $item = addslashes($item);
        }
    }

    private function saveBlockHTML()
    {
        $this->validateBlockHTML($this->values);
        if ( $this->errors ) return;

        $hooks = $this->values['hooks'];
        if ( !get_magic_quotes_gpc() )
            self::addSlashesArray($this->values);
            // $this->values = array_map('addslashes', $this->values);

        if ( !$this->values['id_pwblockhtml'] ) $block_html = new PWBlockHTMLObject();
        else $block_html = new PWBlockHTMLObject($this->values['id_pwblockhtml']);
        $this->updateAssoShop($block_html->id);
        $this->processHooks($block_html, $hooks);
        $block_html->name        = $this->values['name'];
        $block_html->hooks       = strtolower(Tools::jsonEncode($hooks));
        $block_html->html        = $this->values['html'];
        $block_html->html_editor = $this->values['html_editor'];
        $block_html->need_css    = $this->values['need_css'];
        $block_html->css         = $this->values['css'];
        $block_html->css_editor  = $this->values['css_editor'];
        $block_html->need_js     = $this->values['need_js'];
        $block_html->js          = $this->values['js'];
        $block_html->js_editor   = $this->values['js_editor'];
        $block_html->order       = $this->values['order'];
        $block_html->active      = $this->values['active'];

        if ( $block_html->save() ) $this->success = 1;

        if ( !$this->values['id_pwblockhtml'] )
        {
            $this->redirect_after = $this->context->link->getAdminLink('AdminPWBlockHTML').
                '&id_pwblockhtml='.$block_html->id.'&updatepwblockhtml';
            $this->context->cookie->pwblockhtml_success = 1;
        }
    }

    private function validateBlockHTML($values)
    {
        if ( !$values['name'] )  $this->errors[] = Tools::displayError('Поле "Название" обязательно для заполнения.');
        if ( !$values['hooks'] ) $this->errors[] = Tools::displayError('Поле "Хуки" обязательно для заполнения.');
    }

    private function changeStatusBlockHTML($id_pwblockhtml)
    {
        if ( !(int)$id_pwblockhtml ) return;

        $block_html = new PWBlockHTMLObject($id_pwblockhtml);
        $block_html->active = $block_html->active ? 0 : 1;
        $block_html->save();
    }

    private function deleteBlockHTML($id_pwblockhtml)
    {
        $block_html = new PWBlockHTMLObject($id_pwblockhtml);
        $block_html->delete();
    }

    private function getCurrentHooks()
    {
        if ( !$this->id_object ) return Tools::jsonEncode(array());
        $block_html = new PWBlockHTMLObject($this->id_object);
        $hooks = Tools::jsonDecode(strtolower($block_html->hooks), true);
        foreach($hooks as &$hook){
            if(is_numeric($hook)) //для совместимости со старыми версиями
                $hook = self::getHookNameById($hook);
        }
        return Tools::jsonEncode($hooks);
    }

    /**
     * @param $block_html
     * @param array $hooks [key => hook_id if hook exist || hook_name if new hook]
     */
     /*можно было и гораздо короче все написать - стираем все хуки модуля, регистрируем все хуки модуля*/
    private function processHooks($block_html, &$hooks)
    {
        $current_hooks = Tools::jsonDecode($block_html->hooks); //текущие хуки этого блога
        $ex_hooks = PWBlockHTMLObject::getExistingHooks($block_html->id); //хуки модуля, за исключением текущего модуля
        foreach($current_hooks as &$coor_hook){ //удаляем все хуки
            if(is_numeric($coor_hook))
                $coor_hook = self::getHookNameById($coor_hook);
            if(!in_array($coor_hook, $ex_hooks)) //не удаляем хуки, которые зарегестрированы для других блоков
                $this->module->unregisterHook($coor_hook);
        }
        foreach ($hooks as $key => $hook) //регистрируем хуки
        {
            $this->module->registerHook($hook);
        }
    }
    
    /*copy from Hook::getNameById(), в 1,5 этой функции нет*/
    public static function getHookNameById($hook_id)
    {
        $cache_id = 'hook_namebyid_'.$hook_id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue('
							SELECT `name`
							FROM `'._DB_PREFIX_.'hook`
							WHERE `id_hook` = '.(int)$hook_id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }
}
