{if !$show_head}
<style>
header{
display: none;
}
</style>
{/if}
<div id="advancedcarrier" class="">
    {if $productNumber}
        {include file="$tpl_dir./shopping-cart.tpl" opc=true}
        <div class="h2">{l s='Оформление заказа' mod='pwadvancedorder'}</div>
        {if $show_one_click}
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a data-toggle="pill" href="#checkoutadvanced">{l s='Обычный заказ' mod='pwadvancedorder'}</a></li>
            <li class=""><a data-toggle="pill" href="#oneclickadvanced">{l s='Купить в 1 клик' mod='pwadvancedorder'}</a></li>
        </ul>
        {/if}
        <div class="tab-content">
            <div id="oneclickadvanced" class="tab-pane fade">
                <div class="panel panel-default">
                    <div class="item-title">
                        <strong class="panel-title">
                            {l s='Купить в 1 клик' mod='pwadvancedorder'}
                        </strong>
                    </div>
                    <div class="item-body">
                    <div class="form">
                        <form action="{$link->getModuleLink('pwadvancedorder', 'order')}" id="oneclickform" method="post">
                            <input type="hidden" name="oneclick" value="1">
                                <label class="required">Имя</label>
                                <div class="row">
                                    <div class="col-md-7 col-sm-6">
                                        <div class="form-group">
                                    <input class="form-control" name="firstname" type="text" value="{if isset($customer)}{$customer->firstname}{/if}" required="">
                                        </div></div>
                                    <div class="col-md-5 col-sm-6 hidden-xs">
                                        <div class="note">{l s="Введите ваше имя, чтобы мы знали как к Вам обращаться" mod="pwadvancedorder"}</div>
                                    </div>
                                </div>

                                <label class="required">Телефон</label>
                                <div class="row">
                                    <div class="col-md-7 col-sm-6">
                                        <div class="form-group">
                                    <input class="form-control" name="phone" type="text" value="{$summary.delivery->phone}" required="">
                                </div></div>
                                    <div class="col-md-5 col-sm-6 hidden-xs">
                                        <div class="note">{l s="Укажите телефон для уточнения заказа" mod="pwadvancedorder"}</div>
                                    </div>
                                </div>
                            <label class="required">{l s='E-mail' mod='pwoneclick'}</label>
                            <div class="row">
                                <div class="col-md-7 col-sm-6">
                                    <div class="form-group">
                                    <input name="email" id="goods_email" type="email" tabindex="3" class="form-control" required=""/>
                                </div></div>
                                    <div class="col-md-5 col-sm-6 hidden-xs">
                                        <div class="note">{l s="Укажите E-mail для уточнения заказа" mod="pwadvancedorder"}</div>
                                    </div>
                                </div>
                                <div class="agree">
                                    <input type="checkbox" name="cgv" id="cgv" checked value="1">
                                    <label for="cgv">
                                        {l s="Я даю согласие на обработку своих персональных данных в соответствии с законом №152-ФЗ от 27.07.2006 и принимаю условия" mod="pwadvancedorder"} <a class="fancybox cgvlink" href="{$link_conditions}">{l s="Пользовательского соглашения" mod="pwadvancedorder"}</a>
                                    </label>
                                </div>
                            <button class="btn submitAdvanceForm" type="submit">{l s="Оформить заказ" mod="pwadvancedorder"}</button>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
            <div id="checkoutadvanced" class="tab-pane fade in active">
                <div class="panel-group" id="pwadvcart">
                    <div class="panel panel-default openStep">
                        <a data-toggle="collapse" data-parent="#pwadvcart" href="#checkoutadv" class="togglesteps">
                        <div class="item-title">

                            {*<span class="number">1</span>*}
                            <strong class="panel-title">
                            {l s='1. Контактные данные' mod='pwadvancedorder'}
                            </strong>
                            <span class="navi"></span>

                        </div>
                        </a>
                        <div id="checkoutadv" class="panel-collapse collapse">
                            <div class="item-body">
                            <div class="form">
                                {*{if $isLogged && $addresses}*}
                                {*{include file="./customer.tpl"}*}
                                {*{else}*}
                                {include file="./guest.tpl"}
                                {*{/if}*}
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default {if empty($cart->id_address_delivery)}closeStep{else}openStep{/if}">
                            <a data-toggle="collapse" data-parent="#pwadvcart" href="#shippingtadv" class="togglesteps">
                                <div class="item-title">
                                    {*<span class="number">2</span>*}
                            <strong class="panel-title">
                            {l s='2. Способ доставки' mod='pwadvancedorder'}
                            </strong>
                                    <span class="navi"></span>
                                </div>
                            </a>
                        <div id="shippingtadv" class="panel-collapse collapse">
                            <div class="delivery">
                                {$carrier_data.carrier_block}
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default {if empty($cart->id_carrier) || empty($cart->id_address_delivery)}closeStep{else}openStep{/if}">

                            <a data-toggle="collapse" data-parent="#pwadvcart" href="#paymenttadv" class="togglesteps">
                                <div class="item-title">
                                {*<span class="number">3</span>*}
                            <strong class="panel-title">
                            {l s='3. Оплата' mod='pwadvancedorder'}
                            </strong>

                                    <span class="navi"></span>
                                </div>
                            </a>
                        <div id="paymenttadv" class="panel-collapse collapse item-body">
                            <p style="font-size: 16px;" ><b>{l s='Итого с учетом скидки:' mod='pwadvancedorder'}</b> {displayPrice price=$total_price_without_tax} </p>
                            <div class="payment">
                                 


                                <div class="hook_payment">

                                    {$HOOK_PAYMENT}
                                </div>

                            </div>
                            {*<button class="btn submitOrderPayment hidden" type="submit"><span>Оплатить</span></button>*}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else}
     <div class="warning">{l s="Отсутствуют товары в корзине" mod="pwadvancedorder"}</div>
     {capture name="path"}{l s="Корзина"  mod="pwadvancedorder"}{/capture}
    {/if}
</div>