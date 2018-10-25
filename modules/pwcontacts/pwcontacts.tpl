<div class="phone-numbers">
    <ul class="head-tel">
        {if Configuration::get('PS_SHOP_PHONE')}
            <li><a href="tel:{Configuration::get('PS_SHOP_PHONE')}">{Configuration::get('PS_SHOP_PHONE')}</a></li> 
         {/if}
        {if Configuration::get('PS_SHOP_PHONE2')}
         <li><a href="tel:{Configuration::get('PS_SHOP_PHONE2')}">{Configuration::get('PS_SHOP_PHONE2')}</a></li> 
        {/if} 
    </ul>
    <div class="communication-btn">
        <a class="backcall-button button exclusive" href="#uipw-form_call_modal">Заказать звонок</a>
    </div>
</div>