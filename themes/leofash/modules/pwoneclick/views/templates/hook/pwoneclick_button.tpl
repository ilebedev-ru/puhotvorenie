{*<a class="btn btn-default uipw-form_goods_modal" {if $page_name=='product'}id="pwoneclick"{/if} href="#uipw-form_goods_modal"*}
   {*data-pwoneclick-old-price="{$product.old_price}"*}
   {*data-pwoneclick-image="{$product.image}"*}
   {*data-pwoneclick-name="{$product.name}"*}
   {*data-pwoneclick-id="{$product.id}"*}
   {*data-pwoneclick-price="{$product.price}">{l s='Купить в 1 клик' mod='pwoneclick'}</a>*}

<form method="POST" action="{$link->getModuleLink('pwoneclick', 'ajax')}" id="pworderform">
    <span class="or">или введите<br>номер телефона</span>
    <div class="order-now">
        <label for="phone-number" class="phone-number">+7<input name="phone" id="phone-number" type="text"></label>
        <div class="phone_error"></div>
        <input type="hidden" name="id_product" value="{$product.id}"/>
        <input class="exclusive" type="submit" value="{l s='БЫСТРЫЙ ЗАКАЗ' mod='pwoneclick'} &rarr;" tabindex="4"/>
    </div>
    <div style="display:none;" class="pwwait success col-md-8 col-sm-6 col-sx-12 pull-right"></div>
    <div class="pleace_wait alert alert-info">{l s='Происходит оформление заказа, ожидайте...' mod='pwoneclick'}</div>
</form>