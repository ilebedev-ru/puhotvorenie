<div id="uipw-form_goods_modal">
    <div class="goods_info">
        <div class="goods_img">
            <img id="bigpic" itemprop="image" src="" width="{$largeSize.width}" height="{$largeSize.height}"/>
        </div>
        <div class="title"></div>
        <div class="price"><span class="current-price"></span> <sup class="discount"></sup></div>

    </div>
    <div class="goods_order">
        <form method="POST" action="{$order.link}" id="pworderform">
            <div class="title">{l s='Форма заказа' mod='pwoneclick'}</div>
            <div class="system_error"></div>
            <div class="uipw-modal_form_fields">
                <div>
                    <label for="goods_name">{l s='Имя' mod='pwoneclick'}<sup>*</sup></label>
                    <input name="firstname" id="goods_name" type="text" tabindex="1"/>
                    <div class="firstname_error"></div>
                </div>
                <div>
                    <label for="goods_phone">{l s='Телефон' mod='pwoneclick'}</label>
                    <input name="phone" id="goods_phone" type="tel" tabindex="2"/>
                    <div class="phone_error"></div>
                </div>
                <div>
                    <label for="goods_email">{l s='E-mail' mod='pwoneclick'}<sup>*</sup></label>
                    <input name="email" id="goods_email" type="email" tabindex="3"/>
                    <div class="email_error"></div>
                </div>
                <input type="hidden" name="id_product" value=""/>
                <input type="submit" value="{l s='Заказать' mod='pwoneclick'} &rarr;" tabindex="4"/>
                <div class="pleace_wait alert alert-info">{l s='Происходит оформление заказа, ожидайте...' mod='pwoneclick'}</div>
            </div>
        </form>
    </div>
    <section class="uipw-form_success alert alert-success"></section>
</div>
