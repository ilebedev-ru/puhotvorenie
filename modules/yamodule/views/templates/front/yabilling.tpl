{**
* Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
*
* @category  Front Office Features
* @package   Yandex Payment Solution
* @author    Yandex.Money <cms@yamoney.ru>
* @copyright © 2015 NBCO Yandex.Money LLC
* @license   https://money.yandex.ru/doc.xml?id=527052
*}

{capture name=path}
    {l s='Payment with Yandex.Billing.' mod='yamodule'}
{/capture}

<h1 class="page-heading">
    {l s='Order description' mod='yamodule'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
    <p class="alert alert-warning">
        {l s='Your basket is empty.' mod='yamodule'}
    </p>
{else}
    <form action="{$payment_link|escape:'quotes':'UTF-8'}" method="post" id="ym-billing-form">
        {if $empty || $error}
            <input type="hidden" name="cnf" value="1" checked />
        {/if}
        <div class="box cheque-box">
            <h3 class="page-subheading">
                {l s='Yandex.Billing (bank card, e-wallets).' mod='yamodule'}
            </h3>
            <p class="cheque-indent">
                <strong class="dark">
                    {l s='Order short information:' mod='yamodule'}
                </strong>
            </p>
            <p>
                - {l s='Order amount' mod='yamodule'}
                <span id="amount" class="price">{displayPrice price=$total}</span>
                {if $use_taxes == 1}
                    {l s='including tax' mod='yamodule'}
                {/if}
            </p>
            <br />
            {if $empty || $error}
                <div class="required form-group{if $error} form-error{/if}">
                    <label for="ym-billing-fio">{l s='Payer\'s full name ' mod='yamodule'}</label>
                    <input id="ym-billing-fio" class="is_required validate form-control" type="text" name="ym_billing_fio" value="{$fio}" />
                </div>
            {else}
                <input type="hidden" name="formId" value="{$formId}" />
                <input type="hidden" name="narrative" value="{$narrative}" />
                <input type="hidden" name="fio" value="{$fio}" />
                <input type="hidden" name="sum" value="{$total_sum}" />
                <input type="hidden" name="quickPayVersion" value="2" />
            {/if}
        </div>
    </form>
    <p class="cart_navigation clearfix" id="cart_navigation">
        <a
                class="button-exclusive btn btn-default"
                href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Another payment methods' mod='yamodule'}
        </a>
        <button
                class="button btn btn-default button-medium"
                id="ym-billing-confirm-payment">
            <span>{l s='Confirm order' mod='yamodule'}<i class="icon-chevron-right right"></i></span>
        </button>
    </p>
    {if !$error && !$empty}
        <script type="text/javascript"> document.getElementById('ym-billing-form').submit(); </script>
    {/if}
{/if}