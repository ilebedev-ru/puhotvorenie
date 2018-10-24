<script src="https://use.fontawesome.com/6e2726bc60.js"></script>
<style>
.pwfloatcart {
    color: {$colors['PW_FCART_TEXT_COLOR']};
    background:{$colors.PW_FCART_BACKGROUND};
}

.pwfloatcart .block-up,
.pwfloatcart .block-list,
.pwfloatcart .block-cart {
    color:{$colors.PW_FCART_TEXT_COLOR};
}

{if $cart_qties}
.pwfloatcart .block-cart{
    background:{$colors.PW_FCART_CART_BACKGROUND};
}
{/if}


{if $color.PW_FCART_WISH_QUANTITY_TEXT_COLOR}
.pwfloatcart .block-list {
    color:{$colors.PW_FCART_WISH_QUANTITY_TEXT_COLOR};
}
{/if}

{if $blockwishlist}
.pwfloatcart .block-list .count{
    color:{$colors.PW_FCART_WISH_QUANTITY_COLOR};
}
{/if}


{if $color.PW_FCART_CART_TEXT_COLOR}
.pwfloatcart .block-cart {
    color:{$colors.PW_FCART_CART_TEXT_COLOR};
}
{/if}
</style>
<script>
    var float_cart_background = '{$colors.PW_FCART_CART_BACKGROUND}';
</script>

<div class="pwfloatcart">
    <div class="layout">
        <div class="float-cart">
            <div class="pwblock block-up"><span class="pw_label">Наверх</span> <i class="fa fa-angle-up" aria-hidden="true"></i></div>
            {if $blockwishlist}
            <a class="pwblock block-list" href="{$link->getModuleLink('blockwishlist', 'mywishlist')}"><spn class="pw_label">Список желаемого</spn> <span class="count ">{$countwishlist}</span></a>
            {/if}
			{if Module::isInstalled('pwfavoriteproducts')}
			 <a class="pwblock block-list" href="{$link->getModuleLink('pwfavoriteproducts', 'account')}"><spn class="pw_label">Список желаемого</spn></a>
            {/if}
            <a class="pwblock block-cart" href="{$link->getPageLink('order', true)}">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <span class="pw_label">Корзина</span>
                <span id="pw_ajax_cart_quantity">{if $cart_qties}{$cart_qties} шт.{/if}</span>
            </a>
        </div>
    </div>
</div>
