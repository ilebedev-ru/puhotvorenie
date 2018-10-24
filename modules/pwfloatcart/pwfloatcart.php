<?php

if (!defined('_PS_VERSION_')) exit;

class PwFloatCart extends Module
{

    /**
     * @var string
     */
    public $html = '';

    /**
     * Status of mywishlist module
     *
     * @var bool
     */
    public $mywishlist = false;

    /**
     * PwFloatCart constructor.
     */
    public function __construct()
    {
        $this->tab              = 'other';
        $this->name             = 'pwfloatcart';
        $this->author           = 'PrestaWeb.ru';
        $this->version          = '1.0.0';
        $this->bootstrap        = true;
        $this->need_instance    = 0;

        parent::__construct();

        $this->displayName = "Плавающая корзина";
        $this->description = "Модуль правающей корзины";
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Install module
     *
     * @return bool
     */
    public function install()
    {
        if ( !parent::install()
            || !$this->registerHook('footer')
            || !$this->registerHook('header')
            || !Configuration::updateValue('PW_FCART_BACKGROUND', '#000000') // Заливка блока
            || !Configuration::updateValue('PW_FCART_TEXT_COLOR', '#ffffff') // Цвет текста
            || !Configuration::updateValue('PW_FCART_WISH_QUANTITY_COLOR', '#000000') // Цвет колличества желаемого
            || !Configuration::updateValue('PW_FCART_WISH_QUANTITY_TEXT_COLOR', '#be40df') // Цвет текста
            || !Configuration::updateValue('PW_FCART_CART_TEXT_COLOR', '#ffffff') // Цвет текста корзины
            || !Configuration::updateValue('PW_FCART_CART_BACKGROUND', '#be40df') // Заливка корзины

        )
            return false;
        return true;
    }

    /**
     * Uninstall module
     *
     * @return bool
     */
    public function uninstall()
    {
        //$this->_clearCache('pwbackcall.tpl');
        if ( !parent::uninstall()
            || !$this->unregisterHook('footer')
            || !$this->unregisterHook('header')
            || !Configuration::deleteByName('PW_FCART_BACKGROUND')
            || !Configuration::deleteByName('PW_FCART_TEXT_COLOR')
            || !Configuration::deleteByName('PW_FCART_WISH_QUANTITY_COLOR')
            || !Configuration::deleteByName('PW_FCART_WISH_QUANTITY_TEXT_COLOR')
            || !Configuration::deleteByName('PW_FCART_CART_TEXT_COLOR')
            || !Configuration::deleteByName('PW_FCART_CART_BACKGROUND')
        )
            return false;
        return true;
    }

    /**
     * Hook header method
     */
    public function hookHeader()
    {
        $this->context->controller->addJqueryPlugin('scrollTo');
        $this->context->controller->addJS(($this->_path).'views/js/pwfloatcart.js');
        $this->context->controller->addCSS(($this->_path).'views/css/pwfloatcart.css', 'all');
    }

    /**
     * Update settings and flash messages
     * @return string
     */
    public function getContent()
    {
        if (Tools::isSubmit('save')) {

            $errors = array();

            if ( !Configuration::updateValue('PW_FCART_BACKGROUND', Tools::getValue('PW_FCART_BACKGROUND'))
                || !Configuration::updateValue('PW_FCART_TEXT_COLOR', Tools::getValue('PW_FCART_TEXT_COLOR'))
                || !Configuration::updateValue('PW_FCART_WISH_QUANTITY_COLOR', Tools::getValue('PW_FCART_WISH_QUANTITY_COLOR'))
                || !Configuration::updateValue('PW_FCART_WISH_QUANTITY_TEXT_COLOR', Tools::getValue('PW_FCART_WISH_QUANTITY_TEXT_COLOR'))
                || !Configuration::updateValue('PW_FCART_CART_TEXT_COLOR', Tools::getValue('PW_FCART_CART_TEXT_COLOR'))
                || !Configuration::updateValue('PW_FCART_CART_BACKGROUND', Tools::getValue('PW_FCART_CART_BACKGROUND'))
            ) {
                $errors[] = $this->l('Произошла ошибка');
            }

            if ( !empty($errors)) {
                $this->html .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->html .= $this->displayConfirmation($this->l('Настройки обновлены'));
            }
        }

        return $this->html.$this->displayForm();
    }

    public function displayForm()
    {
        $helper = new HelperOptions();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $fields_options = array(
            'general' => array(
                'title' => $this->l('Настройка цвета'),
                'icon' =>    'icon-cogs',
                'fields' => array(
                    'PW_FCART_BACKGROUND' => array(
                        'title' => $this->l('Цвет заливки общего блока'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_FCART_TEXT_COLOR' => array(
                        'title' => $this->l('Цвет текста'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_FCART_WISH_QUANTITY_COLOR' => array(
                        'title' => $this->l('Цвет колличества желаемого'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_FCART_WISH_QUANTITY_TEXT_COLOR' => array(
                        'title' => $this->l('Цвет текста желаемого'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_FCART_CART_TEXT_COLOR' => array(
                        'title' => $this->l('Цвет текста корзины'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                    'PW_FCART_CART_BACKGROUND' => array(
                        'title' => $this->l('Цвет заливки корзины'),
                        'type'  => 'text',
                        'cast'  => 'strval'
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Сохранить'),
                    'name'  => 'save'
                )
            )
        );

        return $helper->generateOptions($fields_options);
    }

    /**
     * Check installed mywishlist module
     *
     * @return bool
     */
    public function isMyWishList()
    {
        return (Module::isInstalled('blockwishlist') && Module::isEnabled('blockwishlist')) ? 1 : 0;
    }

    public function hookFooter()
    {
        $this->context->smarty->assign(array(
            'blockwishlist' => $this->isMyWishList(),
            'countwishlist' => $this->getCountWishListProducts(),
            'wishlistslink' => $this->context->link->getModuleLink('blockwishlist'),
            'colors' => Configuration::getMultiple(array(
                'PW_FCART_BACKGROUND',
                'PW_FCART_TEXT_COLOR',
                'PW_FCART_WISH_QUANTITY_COLOR',
                'PW_FCART_WISH_QUANTITY_TEXT_COLOR',
                'PW_FCART_CART_TEXT_COLOR',
                'PW_FCART_CART_BACKGROUND'
            )),
        ));
        

        return $this->display(__FILE__, 'PwFloatCart.tpl');
    }

    public function getCountWishListProducts()
    {
        if ( !$this->isMyWishList() || !$this->context->customer->isLogged()) {
            return false;
        }

        $wishlist = WishList::getInfosByIdCustomer($this->context->customer->id);

        return !empty($wishlist[0]['nbProducts']) ? $wishlist[0]['nbProducts'] : 0;
    }
}


