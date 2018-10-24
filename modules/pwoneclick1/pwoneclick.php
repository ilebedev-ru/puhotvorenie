<?php

if (!defined('_PS_VERSION_'))
    exit;

class PwOneClick extends PaymentModule
{
    /**
     * @var
     */
    public $html;

    public $active = true;

    /**
     * @var array|mixed
     */
    public $config = array();

    /**
     * @var array
     */
    public $errors = array();

    /**
     * PwOneClick constructor.
     */
    public function __construct()
    {
        $this->name = strtolower(get_class());
        $this->tab = 'other';
        $this->version = 0.2;
        $this->author = 'Prestaweb.ru';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("Купить в 1 клик");
        $this->description = $this->l("Оформление быстрого заказа");
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $config = Configuration::get('PW_ONE_CLICK');
        $this->config = unserialize($config);
    }

    /**
     * @return bool
     */
    public function install()
    {
        $config = array(
            'text' => $this->l('Ваш заказ оформлен. В течение рабочего дня наш менеджер свяжется с Вами. Спасибо!'),
            'option_of_submit' => '1',
        );

        if ( !parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayFooter')
            || !$this->registerHook('displayProductButtons')
            || !$this->registerHook('displayProductListFunctionalButtons')
            || !$this->registerHook('paymentReturn')
            || !Configuration::updateValue('PW_ONE_CLICK', serialize($config))
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if ( !parent::uninstall()
            || !$this->unregisterHook('displayHeader')
            || !$this->unregisterHook('displayFooter')
            || !$this->unregisterHook('displayProductButtons')
            || !$this->unregisterHook('displayProductListFunctionalButtons')
            || !Configuration::deleteByName('PW_ONE_CLICK')

        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function hookDisplayFooter($params)
    {
        if ( !$this->active) {
            return;
        }
        $order = array(
            'link'  => $this->context->link->getModuleLink($this->name, 'ajax')
        );

        $this->context->smarty->assign(array(
            'order' => $order
        ));

        return $this->display(__FILE__, 'pwoneclick_form.tpl');
    }

    /**
     * @param $params
     * @return mixed
     */
    public function hookDisplayProductButtons($params)
    {
        if ( !$this->active) {
            return;
        }
        $old_price = '';
        if(!isset($params['product']) || !is_object ($params['product']))
            return false;

        if(!$this->checkForMinimalPrice($params['product']->price)) return false;

        $price_without_reduction = $params['product']->getPriceWithoutReduct(false, NULL);
        if ($params['product']->price > 0
            && isset($params['product']->specific_prices)
            && $params['product']->specific_prices
            && isset($params['product']->specific_prices['reduction'])
            && $params['product']->specific_prices['reduction'] > 0
            && $price_without_reduction > $params['product']->price)
        {
            $old_price = Tools::displayPrice($params['product']->price_without_reduction);
        }

        /*Проверка на возможность покупки */
        if((!$params['product']->isAvailableWhenOutOfStock((int)$params['product']->out_of_stock) && $params['product']->quantity <= 0)
            || Configuration::get('PS_CATALOG_MODE')
            || !$params['product']->available_for_order)
        return;

        $id_cover = Image::getCover($params['product']->id);
        $id_cover = $id_cover['id_image'];

        $id_image = Configuration::get('PS_LEGACY_IMAGES')==1 ? $params['product']->id.'-'.$id_cover : $id_cover; //По разному надо отображать в зависиомсти от тпа хранения
        
        $product = array(
            'id'        => $params['product']->id,
            'name'      => $params['product']->name,
            'price'     => Tools::displayPrice($params['product']->price),
            'old_price' => $old_price,
            'id_image'  => $id_image,
            'link_rewrite' => $params['product']->link_rewrite
        );
        $this->context->smarty->assign(array(
            'product' => $product
        ));
        return $this->display(__FILE__, 'pwoneclick_button.tpl');
    }

    /**
     * @param $params
     * @return mixed
     */
    public function hookDisplayProductListFunctionalButtons($params)
    {
        if ( !$this->active) {
            return;
        }
        if(!isset($params['product']) || !is_object ($params['product']))
            return false;

        if(!$this->checkForMinimalPrice($params['product']['price'])) return false;

        $old_price = '';

        if ($params['product']['price_without_reduction'] > 0
            && isset($params['product']['specific_prices'])
            && $params['product']['specific_prices']
            && isset($params['product']['specific_prices']['reduction'])
            && $params['product']['specific_prices']['reduction'] > 0) {
            $old_price = Tools::displayPrice($params['product']['price_without_reduction']);
        }

        $product = array(
            'id'        => $params['product']['id_product'],
            'name'      => $params['product']['name'],
            'price'     => Tools::displayPrice($params['product']['price']),
            'old_price' => $old_price,
            'image'     => $this->context->link->getImageLink($params['product']['id_product'], $params['product']['id_image'], 'home_default')
        );

        $this->context->smarty->assign(array(
            'product' => $product
        ));

        return $this->display(__FILE__, 'pwoneclick_button.tpl');
    }

    public function checkForMinimalPrice($price){
        if($price < Configuration::get('PS_PURCHASE_MINIMUM')) return false;
        return true;
    }

    /**
     * @return mixed
     */ 
    public function renderForm()
    {
        $this->context->smarty->assign(array(
            'action'    => $_SERVER['REQUEST_URI'], //переделать эту беду
            'config'    => $this->config,
        ));

        return $this->display(__FILE__, 'views/templates/admin/pwoneclick.tpl');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (Tools::isSubmit('submitpwoneclick'))
        {
            $this->config['text'] = Tools::getValue('success_message');
            $this->config['option_of_submit'] = Tools::getValue('option_of_submit');
            if ( !Configuration::updateValue('PW_ONE_CLICK', serialize($this->config))) {
                $this->errors[] = $this->l('Произошла ошибка');
            }

            if (!empty($this->errors)) {
                $this->html .= $this->displayError($this->errors);
            } else {
                $this->html .= $this->displayConfirmation($this->l('Настройки обновлены'));
            }


        }
        return $this->html.$this->renderForm();
    }

    /**
     * @param $params
     */
	public function hookDisplayHeader($params){
        if ( !$this->active) {
            return;
        }
        $this->context->controller->addJqueryPlugin('fancybox');
		$this->context->controller->addCSS(($this->_path) . 'views/css/' . ($this->name) . '.css', 'all');
		$this->context->controller->addJS(($this->_path) . 'views/js/' . ($this->name) .'.js');
	}

    public function hookPaymentReturn($params)
    {
        $config = Configuration::get('PW_ONE_CLICK');
        $config = unserialize($config);

        if (!$this->active && $config['option_of_submit'] != 2)
            return;

        $message = $config['text'];
        
        $this->smarty->assign(array(
            'message' => $message
        ));

        return $this->display(__FILE__, 'payment_return.tpl');
    }
}
