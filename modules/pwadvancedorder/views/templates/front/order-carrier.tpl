<form method="POST" class="advancedForm">

    <input type="hidden" name="advStep" value="shipping">
    {if isset($virtual_cart) && $virtual_cart}
        <input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
        <p class="alert alert-warning">{l s='No carrier is needed for this order.'}</p>
    {else}
        <div id="HOOK_BEFORECARRIER">
            {if isset($carriers) && isset($HOOK_BEFORECARRIER)}
                {$HOOK_BEFORECARRIER}
            {/if}
        </div>
        {if isset($isVirtualCart) && $isVirtualCart}
            <p class="alert alert-warning">{l s='No carrier is needed for this order.'}</p>
        {else}
            <div class="delivery_options_address">
                {if isset($delivery_option_list)}
                    {foreach $delivery_option_list as $id_address => $option_list}
                        <div class="delivery_options">
                            <div class="item-body">
                                <div class="row delivery">
                            {foreach $option_list as $key => $option}

                                {*{foreach $option.carrier_list as $carrier}
                                    {if $delivery_city != 'Санкт-Петербург' && ($carrier.instance->id_reference == 1 || $carrier.instance->id_reference == 2)}

                                        {continue}
                                    {/if}
                                {/foreach}
*}
                                <div class="delivery_option {if ($option@index % 2)}alternate_{/if}item">
                                    <div>
                                                <div class="col-md-6">
                                                    <div class="d-item" onclick="this.children[0].click()">
                                                        {*<input name="delivery_option_key" type="hidden" value="{$key}" />*}
                                                        <input id="delivery_option_{$id_address|intval}_{$option@index}" class="delivery_option_radio" type="radio" name="delivery_option{*[{$id_address|intval}]*}" data-key="{$key}" data-id_address="{$id_address|intval}" value="{$key}"{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} checked="checked"{/if} />
                                                        <label for="delivery_option_{$id_address|intval}_{$option@index}">
                                                            <span class="descinfo">
                                                            {if $option.unique_carrier}
                                                                {foreach $option.carrier_list as $carrier}
                                                                    <strong>{$carrier.instance->name|escape:'htmlall':'UTF-8'}</strong>
                                                                {/foreach}
                                                                {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                                    <p class="delay">{l s='Срок доставки:'}&nbsp;{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}</p>
                                                                {/if}
                                                            {/if}
                                                            <p class="delivery_option_price">
                                                                {if $option.total_price_with_tax && !$option.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                                                    {if $use_taxes == 1}
                                                                        {if $priceDisplay == 1}
                                                                            {convertPrice price=$option.total_price_without_tax}{if $display_tax_label} {l s='(tax excl.)'}{/if}
                                                                        {else}
                                                                            {convertPrice price=$option.total_price_with_tax}{if $display_tax_label} {l s='(tax incl.)'}{/if}
                                                                        {/if}
                                                                    {else}
                                                                        {convertPrice price=$option.total_price_without_tax}
                                                                    {/if}
                                                                {else}
                                                                    <b>{l s='Free'}</b>
                                                                {/if}
                                                            </p>
                                                            </span>
                                                            {foreach $option.carrier_list as $carrier}
                                                                {if $carrier.logo}
                                                                    <img class="order_carrier_logo" src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}"/>
                                                                {elseif !$option.unique_carrier}
                                                                    {$carrier.instance->name|escape:'htmlall':'UTF-8'}
                                                                    {if !$carrier@last} - {/if}
                                                                {/if}
                                                            {/foreach}
                                                        </label>
                                                    </div>
                                                </div>

                                        {if !$option.unique_carrier}
                                            <table class="delivery_option_carrier{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} selected{/if} resume table table-bordered{if $option.unique_carrier} hide{/if}">
                                                <tr>
                                                    {if !$option.unique_carrier}
                                                        <td rowspan="{$option.carrier_list|@count}" class="delivery_option_radio first_item">
                                                            <input id="delivery_option_{$id_address|intval}_{$option@index}" class="delivery_option_radio" type="radio" name="delivery_option[{$id_address|intval}]" data-key="{$key}" data-id_address="{$id_address|intval}" value="{$key}"{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} checked="checked"{/if} />
                                                        </td>
                                                    {/if}
                                                    {assign var="first" value=current($option.carrier_list)}
                                                    <td class="delivery_option_logo{if $first.product_list[0].carrier_list[0] eq 0} hide{/if}">
                                                        {if $first.logo}
                                                            <img class="order_carrier_logo" src="{$first.logo|escape:'htmlall':'UTF-8'}" alt="{$first.instance->name|escape:'htmlall':'UTF-8'}"/>
                                                        {elseif !$option.unique_carrier}
                                                            {$first.instance->name|escape:'htmlall':'UTF-8'}
                                                        {/if}
                                                    </td>
                                                    <td class="{if $option.unique_carrier}first_item{/if}{if $first.product_list[0].carrier_list[0] eq 0} hide{/if}">
                                                        <input type="hidden" value="{$first.instance->id|intval}" name="id_carrier" />
                                                        {if isset($first.instance->delay[$cookie->id_lang])}
                                                            <i class="fa fa-info-sign"></i>
                                                            {strip}
                                                                {$first.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
                                                                &nbsp;
                                                                {if count($first.product_list) <= 1}
                                                                    ({l s='For this product:'}
                                                                {else}
                                                                    ({l s='For these products:'}
                                                                {/if}
                                                            {/strip}
                                                            {foreach $first.product_list as $product}
                                                                {if $product@index == 4}
                                                                    <acronym title="
                                                            {/if}
                                                            {strip}
                                                                {if $product@index >= 4}
                                                                    {$product.name|escape:'htmlall':'UTF-8'}
                                                                    {if isset($product.attributes) && $product.attributes}
                                                                        {$product.attributes|escape:'htmlall':'UTF-8'}
                                                                    {/if}
                                                                    {if !$product@last}
                                                                        ,&nbsp;
                                                                    {else}
                                                                        ">&hellip;</acronym>)
                                                            {/if}
                                                            {else}
                                                                {$product.name|escape:'htmlall':'UTF-8'}
                                                                {if isset($product.attributes) && $product.attributes}
                                                                    {$product.attributes|escape:'htmlall':'UTF-8'}
                                                                {/if}
                                                                {if !$product@last}
                                                                    ,&nbsp;
                                                                {else}
                                                                    )
                                                                {/if}
                                                            {/if}
                                                            {/strip}
                                                            {/foreach}
                                                        {/if}
                                                    </td>
                                                    <td rowspan="{$option.carrier_list|@count}" class="delivery_option_price">
                                                        <div class="delivery_option_price">
                                                            {if $option.total_price_with_tax && !$option.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                                                {if $use_taxes == 1}
                                                                    {if $priceDisplay == 1}
                                                                        {convertPrice price=$option.total_price_without_tax}{if $display_tax_label} {l s='(tax excl.)'}{/if}
                                                                    {else}
                                                                        {convertPrice price=$option.total_price_with_tax}{if $display_tax_label} {l s='(tax incl.)'}{/if}
                                                                    {/if}
                                                                {else}
                                                                    {convertPrice price=$option.total_price_without_tax}
                                                                {/if}
                                                            {else}
                                                                {l s='Free'}
                                                            {/if}
                                                        </div>
                                                    </td>
                                                </tr>
                                                {foreach $option.carrier_list as $carrier}
                                                    {if $carrier@iteration != 1}
                                                        <tr>
                                                            <td class="delivery_option_logo{if $carrier.product_list[0].carrier_list[0] eq 0} hide{/if}">
                                                                {if $carrier.logo}
                                                                    <img class="order_carrier_logo" src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}"/>
                                                                {elseif !$option.unique_carrier}
                                                                    {$carrier.instance->name|escape:'htmlall':'UTF-8'}
                                                                {/if}
                                                            </td>
                                                            <td class="{if $option.unique_carrier} first_item{/if}{if $carrier.product_list[0].carrier_list[0] eq 0} hide{/if}">
                                                                <input type="hidden" value="{$first.instance->id|intval}" name="id_carrier" />
                                                                {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                                    <i class="fa fa-info-sign"></i>
                                                                    {strip}
                                                                        {$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
                                                                        &nbsp;
                                                                        {if count($first.product_list) <= 1}
                                                                            ({l s='For this product:'}
                                                                        {else}
                                                                            ({l s='For these products:'}
                                                                        {/if}
                                                                    {/strip}
                                                                    {foreach $carrier.product_list as $product}
                                                                        {if $product@index == 4}
                                                                            <acronym title="
                                                                {/if}
                                                                {strip}
                                                                    {if $product@index >= 4}
                                                                        {$product.name|escape:'htmlall':'UTF-8'}
                                                                        {if isset($product.attributes) && $product.attributes}
                                                                            {$product.attributes|escape:'htmlall':'UTF-8'}
                                                                        {/if}
                                                                        {if !$product@last}
                                                                            ,&nbsp;
                                                                        {else}
                                                                            ">&hellip;</acronym>)
                                                                    {/if}
                                                                    {else}
                                                                        {$product.name|escape:'htmlall':'UTF-8'}
                                                                        {if isset($product.attributes) && $product.attributes}
                                                                            {$product.attributes|escape:'htmlall':'UTF-8'}
                                                                        {/if}
                                                                        {if !$product@last}
                                                                            ,&nbsp;
                                                                        {else}
                                                                            )
                                                                        {/if}
                                                                    {/if}
                                                                    {/strip}
                                                                    {/foreach}
                                                                {/if}
                                                            </td>
                                                        </tr>
                                                    {/if}
                                                {/foreach}
                                            </table>
                                        {/if}
                                    </div>
                                </div> <!-- end delivery_option -->
                            {/foreach}
                                    </div>
                                <button class="btn submitAdvanceForm invert" type="submit"><span>{l s="К выбору оплаты" mod="pwadvancedorder"}</span></button>
                                </div>
                        </div> <!-- end delivery_options -->
                        <div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address}">
                            {if isset($HOOK_EXTRACARRIER_ADDR) &&  isset($HOOK_EXTRACARRIER_ADDR.$id_address)}{$HOOK_EXTRACARRIER_ADDR.$id_address}{/if}
                        </div>
                        {foreachelse}
                        {assign var='errors' value=' '|explode:''}
                        <p class="alert alert-warning" id="noCarrierWarning">
                            {foreach $cart->getDeliveryAddressesWithoutCarriers(true, $errors) as $address}
                                {if empty($address->alias)}
                                    {l s='No carriers available.'}
                                {else}
                                    {assign var='flag_error_message' value=false}
                                    {foreach $errors as $error}
                                        {if $error == Carrier::SHIPPING_WEIGHT_EXCEPTION}
                                            {$flag_error_message = true}
                                            {l s='The product selection cannot be delivered by the available carrier(s): it is too heavy. Please amend your cart to lower its weight.'}
                                        {elseif $error == Carrier::SHIPPING_PRICE_EXCEPTION}
                                            {$flag_error_message = true}
                                            {l s='The product selection cannot be delivered by the available carrier(s). Please amend your cart.'}
                                        {elseif $error == Carrier::SHIPPING_SIZE_EXCEPTION}
                                            {$flag_error_message = true}
                                            {l s='The product selection cannot be delivered by the available carrier(s): its size does not fit. Please amend your cart to reduce its size.'}
                                        {/if}
                                    {/foreach}
                                    {if !$flag_error_message}
                                        {l s='No carriers available for the address "%s".' sprintf=$address->alias}
                                    {/if}
                                {/if}
                                {if !$address@last}
                                    <br />
                                {/if}
                                {foreachelse}
                                {l s='No carriers available.'}
                            {/foreach}
                        </p>
                    {/foreach}
                {/if}
            </div> <!-- end delivery_options_address -->
            <div id="extra_carrier" style="display: none;"></div>
        {/if}
    {/if}

</form>
{strip}
    {if !$opc}
        {addJsDef orderProcess='order'}
        {if isset($virtual_cart) && !$virtual_cart && $giftAllowed && $cart->gift == 1}
            {addJsDef cart_gift=true}
        {else}
            {addJsDef cart_gift=false}
        {/if}
        {addJsDef orderUrl=$link->getPageLink("order", true)|escape:'quotes':'UTF-8'}
        {addJsDefL name=txtProduct}{l s='Product' js=1}{/addJsDefL}
        {addJsDefL name=txtProducts}{l s='Products' js=1}{/addJsDefL}
    {/if}
    {if $conditions}
        {addJsDefL name=msg_order_carrier}{l s='You must agree to the terms of service before continuing.' js=1}{/addJsDefL}
    {/if}
{/strip}
