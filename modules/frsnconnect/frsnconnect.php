<?php
/**
 * Social Network connect modules
 * frsnconnect 0.16 by froZZen
 */

if (!defined('_PS_VERSION_'))
    exit;

require_once(dirname(__FILE__) . '/SNTools.php');

class FRSnConnect extends Module
{

    private $fr_sn_servlist = array();

    public function __construct()
    {
        $this->name = 'frsnconnect';
        $this->tab = 'social_networks';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->version = '0.16.1';
        $this->author = 'froZZen';
        $this->need_instance = 0;
        $this->module_key = '311a7edeaa66b94049e25d931cf1ec1b';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Social Network Connection');
        $this->description = $this->l('Adds a block for customer to login or register via Social Networks');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayMyAccountBlock')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('displayFooter')
            || !$this->registerHook('displaySocialAuth')
            || !Configuration::updateValue('FRSNCONN_EMPTYEMAIL', 'test@mail.com')
        )
            return false;
        include dirname(__FILE__) . '/upgrade/install-0.15.1.php';
        upgrade_module_0_15_1($this);
        include dirname(__FILE__) . '/upgrade/install-0.15.3.php';
        upgrade_module_0_15_3($this);
        include dirname(__FILE__) . '/upgrade/install-0.16.1.php';
        upgrade_module_0_16_1($this);
        return true;

    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Configuration::deleteByName('FRSNCONN_EMPTYEMAIL')
        )
            return false;
        Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'sn_customer`, `' . _DB_PREFIX_ . 'sn_service`');
        return true;

    }

    public function hookDisplayFooter($params)
    {
        $currcontroller = strtolower(get_class($this->context->controller));
        if ($currcontroller == 'authcontroller' ||
            $currcontroller == 'orderopccontroller' ||
            $currcontroller == 'ordercontroller') 
        {
            $this->context->controller->addCSS($this->_path . 'frsnconnect.css');
            if (empty(Context::getContext()->fr_sn_servlist) || !count(Context::getContext()->fr_sn_servlist)) {
                require_once(_PS_MODULE_DIR_ . 'frsnconnect/SNTools.php');
                Context::getContext()->fr_sn_servlist = SNTools::GetSNServiceList();
            }
            $fr_sn_list = Context::getContext()->fr_sn_servlist;
            $tpl_path = $this->getTemplatePath('frsnconnect_form.tpl');
            $auth_css_path = _THEME_CSS_DIR_ . 'authentication.css';

            $this->context->smarty->assign('not_services', $fr_sn_list);
            $this->context->smarty->assign('tpl_path', $tpl_path);
            $this->context->smarty->assign('auth_css_path', $auth_css_path);
            $this->context->smarty->assign('auth', 1);
            $tplname = '';       
            if ($currcontroller == 'authcontroller' || $currcontroller == 'ordercontroller')
                $tplname = 'frsnconnect-top.tpl';
            elseif ($currcontroller == 'orderopccontroller')
                $tplname = 'frsnconnect-top-opc.tpl';

            $html = $this->display(__FILE__, $tplname);
            $html = str_replace(array("\r", "\n"), "", $html);
            $this->context->smarty->assign('html', $html);
            return $this->display(__FILE__, $tplname);
        }

    }

    public function hookDisplayCustomerAccount($params)
    {
        $this->smarty->assign('in_footer', false);
        return $this->display(__FILE__, 'frsnconnect-my-account.tpl');

    }

    public function hookDisplayMyAccountBlock($params)
    {
        $this->smarty->assign('in_footer', true);
        return $this->display(__FILE__, 'frsnconnect-my-account.tpl');

    }

    public function hookDisplaySocialAuth($params)
    {
        if($this->context->customer->logged)
            return;
        if (empty(Context::getContext()->fr_sn_servlist)) {
            require_once(_PS_MODULE_DIR_ . 'frsnconnect/SNTools.php');
            Context::getContext()->fr_sn_servlist = SNTools::GetSNServiceList();
        }
        $fr_sn_list = Context::getContext()->fr_sn_servlist;
        $this->context->smarty->assign('not_services', $fr_sn_list);
        $this->context->smarty->assign('auth', 1);
        return $this->display(__FILE__, 'frsnconnect_form_auth.tpl');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('updatesn_service'))
            $output .= $this->renderSNForm((int)Tools::getValue('id_sn_service'));
        else {
            $this->_postProcess();

            $this->fr_sn_servlist = SNTools::GetSNServiceListSetup();
            $output .= $this->renderSNList();
            $output .= $this->renderAdditionalForms();
        }
        return $output;
    }


    public function renderAdditionalForms()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Default e-mail'),
                        'name' => 'FRSNCONN_EMPTYEMAIL',
                    ),
                ),
                'submit' => array(
                    'name' => 'submitFrSnSetup',
                    'title' => $this->l('Save'),
                ),
            ),
        );
        $fields_form1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Guide'),
                    'icon' => 'icon-info-circle'
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' => $this->l('Facebook register application:'),
                        'name' => 'Help_FB',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('VKontakte register application:'),
                        'name' => 'Help_VK',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('Odnoklassniki register application:'),
                        'name' => 'Help_OK',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('Twitter register application:'),
                        'name' => 'Help_TW',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('Google register application:'),
                        'name' => 'Help_GL',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('Yandex register application:'),
                        'name' => 'Help_YA',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('MailRu register application:'),
                        'name' => 'Help_MR',
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('LinkedIn register application:'),
                        'name' => 'Help_LD',
                    ),
                ),
            ),
        );

        $fields_form2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Support, feedback and issues'),
                    'icon' => 'icon-asterisk'
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' => $this->l('e-Mail:'),
                        'name' => 'FR_SUPPORT_EMAIL',
                    ),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => array(
                'FRSNCONN_EMPTYEMAIL' => Tools::safeOutput(Tools::getValue('FRSNCONN_EMPTYEMAIL', Configuration::get('FRSNCONN_EMPTYEMAIL'))),
                'FR_SUPPORT_EMAIL' => '<b class="form-control"><a href="mailto:frozzen@pisem.net">frozzen(at)pisem.net</a></b>',
                'Help_FB' => '<span class="form-control"><a href="https://developers.facebook.com/apps/">https://developers.facebook.com/apps/</a></span>',
                'Help_VK' => '<span class="form-control"><a href="http://vkontakte.ru/editapp?act=create&site=1">http://vkontakte.ru/editapp?act=create&site=1</a></span>',
                'Help_OK' => '<span class="form-control" style="height:auto;"><a href="http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=12878032">http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=12878032</a><br /> 
   <a href="http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev">http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev</a>
   <br />Need VALUABLE ACCESS
   <br />Field <b>sn_service_key_secret</b> must be: <i>client_secret<b>;</b>client_public</i></span>',
                'Help_TW' => '<span class="form-control"><a href="https://dev.twitter.com/apps/new">https://dev.twitter.com/apps/new</a></span>',
                'Help_GL' => '<span class="form-control"><a href="https://code.google.com/apis/console/">https://code.google.com/apis/console/</a></span>',
                'Help_YA' => '<span class="form-control" style="height:auto;"><a href="https://oauth.yandex.ru/client/my">https://oauth.yandex.ru/client/my</a><br /><a href="http://api.yandex.ru/oauth/doc/dg/tasks/register-client.xml">http://api.yandex.ru/oauth/doc/dg/tasks/register-client.xml</a></span>',
                'Help_MR' => '<span class="form-control"><a href="http://api.mail.ru/sites/my/add">http://api.mail.ru/sites/my/add</a></span>',
                'Help_LD' => '<span class="form-control"><a href="https://www.linkedin.com/secure/developer">https://www.linkedin.com/secure/developer</a></span>',
            ),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $output = $helper->generateForm(array($fields_form));
        $output .= $helper->generateForm(array($fields_form1));
        $output .= $helper->generateForm(array($fields_form2));

        return $output;

    }

    protected function _postProcess()
    {
        if (Tools::isSubmit('submitEditSNConfiguration')) {

            include_once(dirname(__FILE__) . '/SNConfiguration.php');

            $sns = new SNConfiguration((int)Tools::getValue('id_sn_service'));

            $sns->sn_service_name_full = Tools::getValue('sn_service_name_full');
            $sns->sn_service_key_id = Tools::getValue('sn_service_key_id');
            $sns->sn_service_key_secret = Tools::getValue('sn_service_key_secret');
            $sns->active = Tools::getValue('active');

            $sns->save();
        } elseif (Tools::isSubmit('statussn_service')) {

            include_once(dirname(__FILE__) . '/SNConfiguration.php');

            $sns = new SNConfiguration((int)Tools::getValue('id_sn_service'));
            if ($sns->id) {
                $sns->active = (int)(!$sns->active);
                $sns->save();
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&conf=4&module_name=' . $this->name);
        } elseif (Tools::isSubmit('submitFrSnSetup')) {

            Configuration::updateValue('FRSNCONN_EMPTYEMAIL', Tools::getValue('FRSNCONN_EMPTYEMAIL'));

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&conf=4&module_name=' . $this->name);
        }

    }

    public function renderSNForm($id_sn_service = 0)
    {

        include_once(dirname(__FILE__) . '/SNConfiguration.php');

        $sns = new SNConfiguration((int)$id_sn_service);

        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Edit Social Service'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_sn_service',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'sn_service_name',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'class',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('sn_service_name_full'),
                        'name' => 'sn_service_name_full',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('sn_service_key_id'),
                        'name' => 'sn_service_key_id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('sn_service_key_secret'),
                        'name' => 'sn_service_key_secret',
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('active'),
                        'name' => 'active',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('On')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Off')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitEditSNConfiguration',
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'sn_service';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEditSNConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value = array(
            'id_sn_service' => $sns->id_sn_service,
            'sn_service_name' => $sns->sn_service_name,
            'sn_service_name_full' => $sns->sn_service_name_full,
            'sn_service_key_id' => $sns->sn_service_key_id,
            'sn_service_key_secret' => $sns->sn_service_key_secret,
            'class' => $sns->class,
            'active' => $sns->active,
        );

        return $helper->generateForm(array($fields_form_1));

    }

    public function renderSNList()
    {

        $fields_list = array(
            'sn_service_name' => array(
                'title' => $this->l('sn_service_name'),
                'type' => 'text',
            ),
            'sn_service_name_full' => array(
                'title' => $this->l('sn_service_name_full'),
                'type' => 'text',
            ),
            'sn_service_key_id' => array(
                'title' => $this->l('sn_service_key_id'),
                'type' => 'text',
            ),
            'sn_service_key_secret' => array(
                'title' => $this->l('sn_service_key_secret'),
                'type' => 'text',
            ),
            'active' => array(
                'title' => $this->l('active'),
                'active' => 'status',
                'type' => 'bool',
            ),
        );

        $helper = new HelperList();

        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array('edit');
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->identifier = 'id_sn_service';
        $helper->title = $this->l('Social services list');
        $helper->table = 'sn_service';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateList($this->fr_sn_servlist, $fields_list);

    }

}

?>
