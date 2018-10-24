<div class="phone-numbers">
    {if Configuration::get('PS_SHOP_PHONE')}
        <p class="phone">
            <a href="tel:{Configuration::get('PS_SHOP_PHONE')}">{Configuration::get('PS_SHOP_PHONE')}</a>
        </p>
    {/if}
    {if Configuration::get('PS_SHOP_PHONE2')}
        <p class="phone">
            <a href="tel:{Configuration::get('PS_SHOP_PHONE2')}">{Configuration::get('PS_SHOP_PHONE2')}</a>
        </p>
    {/if}
    <div class="communication-btn">
        <a class="uipw-form_question_modal button exclusive" href="#uipw-form_question_modal">Задать вопрос</a>
        <a class="backcall-button button exclusive" href="#uipw-form_call_modal">Заказать звонок</a>
    </div>
</div>