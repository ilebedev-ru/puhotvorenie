{*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block user information module HEADER -->
<div id="header_user" class="{if $PS_CATALOG_MODE}header_user_catalog{/if}">    
    {if !$PS_CATALOG_MODE}
        <ul id="header_nav" class="links list-inline">
            <li><a href="{$link->getPageLink($order_process, true)|escape:'html'}"
                   title="{l s='View my shopping cart' mod='blockuserinfo'}" rel="nofollow">
                   <!--  <i class="fa fa-shopping-cart"></i></a></li> -->
            <li id="shopping_cart">
                <a href="{$link->getPageLink($order_process, true)|escape:'html'}"
                   title="{l s='Просмотреть карзину' mod='blockuserinfo'}" rel="nofollow">
                    <span>{l s='Корзина:' mod='blockuserinfo'}</span>
                    <span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
<!--                     <span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">шт.</span>
                    <span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">шт.</span> -->
                    {*<span class="ajax_cart_total{if $cart_qties == 0} hidden{/if}">*}
					{*{if $cart_qties > 0}*}
                        {*{if $priceDisplay == 1}*}
                            {*{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}*}
                            {*{convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}*}
                        {*{else}*}
                            {*{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}*}
                            {*{convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}*}
                        {*{/if}*}
                    {*{/if}*}
				{*</span>*}
                    <span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='0' mod='blockuserinfo'}</span>
                    {if $cart_qties == 0}
                        <br>
                        <span class="ajax_cart_quantity no-goods">Товары пока не добавлены</span>
                    {/if}
                </a>
            </li>
        </ul>
    {/if}
    <div class="link-order-btn{if $cart_qties == 0} hidden{/if}">
        <a class="button" href="{$link->getPageLink($order_process, true)|escape:'html'}">Оформить заказ</a>
    </div>
</div>
<!-- /Block user information module HEADER -->
