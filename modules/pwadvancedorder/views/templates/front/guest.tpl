{*{if !$isLogged}
*}{*<p>{l s="Если у Вас есть регистрация на сайте, пожалуйста, " mod="pwadvancedorder"} *}{*
    <a class="auth" href="{$link->getPageLink('authentication', null, null, ['back' => 'order'])}">{l s="Войдите в личный кабинет>>>" mod="pwadvancedorder"}</a>
    {l s="или заполните поля ниже:" mod="pwadvancedorder"} 
</p>
{/if}*}
<form action="{$link->getPageLink('authentication', null, null, ['back' => 'order'])}" method="POST" class="advancedForm">
    <input type="hidden" name="advStep" value="information"/>
    {if $isLogged}
    <input type="hidden" name="logged" value="1"/>
    {/if}
    {if isset($fields.firstname)}
            <label class="{if !empty($fields.firstname.is_required)}required{/if}">{l s="Фамилия, имя, отчество" mod="pwadvancedorder"}</label>
            <div class="row">
                <div class="col-md-7 col-sm-6"><div class="form-group">
                <input class="form-control" name="firstname" type="text" value="{$customer->firstname}" {if !empty($fields.firstname.is_required)}required{/if}>
                    </div></div>
                {*<div class="col-md-5 col-sm-6 hidden-xs">*}
                    {*<div class="note">{l s="Введите ваше имя, чтобы мы знали как к Вам обращаться" mod="pwadvancedorder"}</div>*}
                {*</div>*}
            </div>
        {$fields.firstname = null}
    {/if}
    {if isset($fields.middlename)}
            <label class="{if !empty($fields.middlename.is_required)}required{/if}">{l s="Отчество" mod="pwadvancedorder"}</label>
            <div class="row">
                <div class="col-md-7 col-sm-6"><div class="form-group">
                <input class="form-control" name="middlename" type="text" value="{$customer->middlename}" {if !empty($fields.middlename.is_required)}required{/if}>
                    </div></div>
            </div>
        {$fields.middlename = null}
    {/if}
    {if isset($fields.company)}
        <label class="{if !empty($fields.company.is_required)}required{/if}">{l s="Отчество" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
            <input class="form-control" name="company" type="text" value="" {if !empty($fields.company.is_required)}required{/if}>
                </div></div>
        </div>
    {$fields.company = null}
    {/if}
    {if isset($fields.lastname)}
        <label class="{if !empty($fields.lastname.is_required)}required{/if}">{l s="Фамилия" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
            <input class="form-control" name="lastname" type="text" value="{$customer->lastname}" {if !empty($fields.lastname.is_required)}required{/if}>
                </div></div>
        </div>
    {$fields.lastname = null}
    {/if}
    {if isset($fields.id_state)}
        <label class="{if !empty($fields.id_state.is_required)}required{/if}">{l s="Регион" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
                    <select name="id_state" data-jcf={literal}'{"wrapNative": false, "wrapNativeOnMobile": false}'{/literal}>
                        <option value="" selected disabled hidden>Выберите регион</option>
                        {foreach from=$states item=state}
                            <option value="{$state.id_state}">{$state.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        {$fields.id_state = null}
    {/if}
    {if isset($fields.id_country)}
        <label class="{if !empty($fields.id_country.is_required)}required{/if}">{l s="Страна" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
                    <select name="id_country" data-jcf={literal}'{"wrapNative": false, "wrapNativeOnMobile": false}'{/literal}>
                        {foreach from=$countries item=country}
                            <option value="{$country.id_country}">{$country.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        {$fields.id_country = null}
    {/if}
    {if isset($fields.city)}
        <label class="{if !empty($fields.city.is_required)}required{/if}">{l s="Город" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group ui-widget">
                    <input class="form-control" name="city" type="text" value="{if $address->city}{$address->city}{/if}" {if !empty($fields.city.is_required)}required{/if}>
                </div>
            </div>
            {*<div class="col-md-5 col-sm-6 hidden-xs">*}
                {*<div class="note">{l s="Укажите город для расчёта доставки" mod="pwadvancedorder"}</div>*}
            {*</div>*}
        </div>
        {$fields.city = null}
    {/if}
    {if isset($fields.postcode)}
        <div class="hidden">
        <label class="{if !empty($fields.postcode.is_required)}required{/if}">{l s="Почтовый индекс" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
            <input class="form-control validate city-id" {if !empty($fields.postcode.is_required)}required{/if} data-validate="isPostCode" name="postcode" type="number" value="{if $address->postcode}{$address->postcode}{/if}" {if !empty($fields.postcode.is_required)}required{/if}>
                </div></div>
        </div>
        <div class="col-md-3">
            <a rel="nofollow" target="_blank" href="//indexp.ru">{l s="Не помните индекс?"}</a>
        </div>
        </div>
    {$fields.postcode = null}
    {/if}
    {if isset($fields.address1)}
        <label class="{if !empty($fields.address1.is_required)}required{/if}">{l s="Адрес" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
            <input class="form-control" name="address1" type="text" value="" {if !empty($fields.address1.is_required)}required{/if}>
                </div></div>
            <div class="col-md-5 col-sm-6 hidden-xs">
                <div class="note">{l s="Укажите, улицу, дом" mod="pwadvancedorder"}</div>
            </div>
        </div>
    {$fields.address1 = null}
    {/if}
    {if isset($fields.email)}
        <label class="{if !empty($fields.email.is_required)}required{/if}">{l s="E-mail" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
            <input class="form-control" name="email" type="text" value="{$customer->email}" {if !empty($fields.email.is_required)}required{/if}>
                </div></div>
            {*<div class="col-md-5 col-sm-6 hidden-xs">*}
                {*<div class="note">{l s="Укажите e-mail, на который будет выслан пароль" mod="pwadvancedorder"}</div>*}
            {*</div>*}
        </div>
    {$fields.email = null}
    {/if}
    {if isset($fields.phone)}
        <label class="{if !empty($fields.phone.is_required)}required{/if}">{l s="Телефон" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
                    <input class="form-control" name="phone" type="text" value="{if $address->phone}{$address->phone}{/if}" placeholder="+7" {if !empty($fields.phone.is_required)}required{/if}>

                </div>
            </div>
            {*<div class="col-md-5 col-sm-6 hidden-xs">*}
                {*<div class="note">{l s="Укажите телефон для уточнения заказа" mod="pwadvancedorder"}</div>*}
            {*</div>*}
        </div>
        {$fields.phone = null}
    {/if}
    {if isset($fields.phone_mobile)}
        <label class="{if !empty($fields.phone_mobile.is_required)}required{/if}">{l s="Второй телефон" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6">
                <div class="form-group">
                    <input class="form-control" name="phone_mobile" type="text" value="" placeholder="+7" {if !empty($fields.phone_mobile.is_required)}required{/if}>
                </div></div>
        </div>
        {$fields.phone_mobile = null}
    {/if}
    {if isset($fields.email)}
        {if !$isLogged}
            <div class="password" style="display:none;">
                <label class="{if !empty($fields.email.is_required)}required{/if}">{l s="Пароль" mod="pwadvancedorder"}</label>
                <div class="row">
                    <div class="col-md-7 col-sm-6"><div class="form-group">
                            <input class="form-control" name="passwd" type="password" value="" {if !empty($fields.email.is_required)}required{/if}>
                        </div></div>
                </div>
            </div>
        {/if}
    {/if}
    {if isset($fields.other)}
        <label class="{if !empty($fields.other.is_required)}required{/if}">{l s="Дополнительно" mod="pwadvancedorder"}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
            <textarea class="form-control" name="other"></textarea>
                </div></div>
            <div class="col-md-5 col-sm-6 hidden-xs">
                <div class="note">{l s="Пожелания к заказу" mod="pwadvancedorder"}</div>
            </div>
        </div>
    {$fields.other = null}
    {/if}
    {foreach from=$fields key=key item=field}
    {if $field}
        <label class="{if !empty($fields[$key]['is_required'])}required{/if}">{{$field.name}}</label>
        <div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
            <input class="form-control" name="{$key}" type="text" value="" {if !empty($fields[$key]['is_required'])}required{/if}>
                </div></div>
        </div>
    {/if}
    {/foreach}
        <button class="btn submitAdvanceForm invert" type="submit"><span>{l s="К выбору доставки" mod="pwadvancedorder"}</span></button>
    {if !empty($conditions)}
    <div class="col-md-6 col-xs-12">
        <div class="checkbox">
        <label for="cgv">
            <input type="checkbox" name="cgv" id="cgv" checked value="1">
            {l s="Я даю согласие на обработку своих персональных данных в соответствии с законом №152-ФЗ от 27.07.2006 и принимаю условия " mod="pwadvancedorder"}<a class="fancybox cgvlink" href="{$link_conditions}">{l s="Пользовательского соглашения" mod="pwadvancedorder"}</a>
        </label>
        </div>
    </div>
    {/if}
</form>