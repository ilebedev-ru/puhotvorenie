{if empty($fast) || !$fast}{capture name=path}Оформление заказа{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}{/if}
{if $fast}<br />{/if}

<h1 class="page-heading">Оформить заказ</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($hideForm) && !$hideForm}
<script type="text/javascript">{literal}
	$(document).ready(function(){
		$('.have_account').click(function() {
			if ($('.have_account:checked').length > 0)
			{
				$('.account_enter').fadeIn();
			}else $('.account_enter').fadeOut();
		});
	});
{/literal}
</script>
{if $conf.eo_jscheck}
{literal}
	<script type="text/javascript">
		$(function(){
			$("#order-creation").submit(function(){
				var okay = true;
				$("input.required").each(function (i) {
					if($(this).val() == "0" || !$(this).val() || ($(this).attr('type') == 'checkbox' && !$(this).is(':checked'))){
						okay = false;
						$(this).css('border', '1px solid red');
					}
				});
				if(!okay){
					alert('Заполните все необходимые поля');
					return false;
				}
			});
		});
	</script>
{/literal}
{/if}
<div class="registration">
<form action="{$link->getModuleLink('pwexpressorder', 'display')}" method="post" id="account-creation_form" class="std">
	{if count($carriers)}
		<fieldset class="carrier-select">
		<h3>Выберите способ доставки</h3>
		<table class="std" id="carrier-select">
		{foreach from=$carriers item=car}
		<tr>
			<td class="radio"><input type="radio" name="id_carrier" id="carrier{$car.id_carrier}" value="{$car.id_carrier}"{if $smarty.post.id_carrier == $car.id_carrier} checked="checked"{/if}></td>
			<td class="name"><label for="carrier{$car.id_carrier}">{$car.name}</label></td><td class="cost">{$car.delay}</td>
			<td class="cost">{convertPrice price=$car.price}</td>
		</tr>
		{/foreach}
		</table>
	</fieldset>
	{/if}
    {if Module::isEnabled('frsnconnect')}
    {hook h='displaySocialAuth'}
    {/if}
	<div class="opc_account_form">
		{if $conf.eo_fname_show}
		<div class="required text form-group" id='first_p'>
                <label for="firstname">Введите имя{if $conf.eo_fname_required}{/if}</label>
                <input type="text" class="form-control validate text{if $conf.eo_fname_required} is_required{/if}" data-validate="isName" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape}{elseif isset($customer->firstname)}{$customer->firstname}{/if}" />
		</div>
		{/if}
		{if $conf.eo_lname_show}
		<div class="required text form-group">
                <label for="lastname">Фамилия{if $conf.eo_lname_required}<sup>*</sup>{/if}</label>
                <input type="text" class="form-control text{if $conf.eo_lname_required} is_required{/if}" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape}{elseif isset($customer->lastname)}{$customer->lastname}{/if}" />
		</div>
		{/if}
		{if $conf.eo_email_show}
		<div class="required text form-group">
                <label for="email">{l s='Введите E-mail' mod='expressorder'}{if $conf.eo_email_required}{/if}</label>
                <input type="text" class="form-control validate  text email{if $conf.eo_email_required} is_required{/if}" data-validate="isEmail" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape}{elseif isset($customer->email)}{$customer->email}{/if}" />
		</div>
		{/if}
		{if $conf.eo_password_show && !$isLogged}
		<div class="required text form-group">
                <label for="fname">{l s='Ваш пароль' mod='expressorder'}{if $conf.eo_password_required}<sup>*</sup>{/if}</label>
                <input type="password" class="form-control text{if $conf.eo_password_required} is_required{/if}" id="password" name="password" value="{if isset($smarty.post.password)}{$smarty.post.password|escape}{/if}" />
		</div>
		{/if}
		{if $conf.eo_mobilephone_show}
		<div class="required text form-group">
                <label for="mobile_phone">{l s='Mobile phone' mod='expressorder'}{if $conf.eo_mobilephone_required}<sup>*</sup>{/if}</label>
                <input type="text" class="form-control text{if $conf.eo_mobilephone_required} is_required{/if}" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape}{elseif isset($address.phone_mobile)}{$address.phone_mobile}{/if}" />
		</div>
		{/if}
		{if $conf.eo_address_show}
		<div class="required text form-group">
                <label for="address">Адрес{if $conf.eo_address_required}<sup>*</sup>{/if}</label>
                <input type="text" class="form-control text{if $conf.eo_address_required} is_required{/if}" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape}{elseif isset($address.address1)}{$address.address1}{/if}" />
		</div>
		{/if}
		{if $conf.eo_address2_show}
		<div class="required text form-group">
                <label for="address2">{l s='Address (2)' mod='expressorder'}{if $conf.eo_address2_required}<sup>*</sup>{/if}</label>
                <input type="text" class="form-control text{if $conf.eo_address2_required} is_required{/if}" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape}{/if}" />
		</div>
		{/if}
		{if $conf.eo_zip_show}
		<div class="required text form-group">
                <label for="zip">{l s='Почтовый индекс' mod='expressorder'}{if $conf.eo_zip_required}<sup>*</sup>{/if}</label>
                <input type="text" class="form-control text{if $conf.eo_zip_required} is_required{/if}" id="zip" name="zip" value="{if isset($smarty.post.zip)}{$smarty.post.zip|escape}{elseif isset($address.postcode)}{$address.postcode}{/if}" />
		</div>
		{/if}
		{if $conf.eo_city_show}
		<div class="required text form-group">
                <label for="city">Город{if $conf.eo_city_required}<sup>*</sup>{/if}</label>
                <input type="text" class="form-control text{if $conf.eo_city_required} is_required{/if}" id="city" name="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape}{elseif isset($address.city)}{$address.city}{/if}" />
		</div>
		{/if}
		{if $conf.eo_phone_show}
		<div class="required text form-group">
                <label for="phone">Введите телефон{if $conf.eo_phone_required}{/if}</label>
                <input type="text" class="form-control validate text{if $conf.eo_phone_required} is_required{/if}" data-validate="isPhoneNumber" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape}{elseif isset($address.phone)}{$address.phone}{/if}" />
 		</div>
		{/if}
		{if $conf.eo_country_show}
		<div class="required text form-group">
                <label for="id_country">{l s='Страна' mod='expressorder'}{if $conf.eo_country_required}<sup>*</sup>{/if}</label>
				<select name="id_country" id="id_country">
					<option value="">-</option>
					{foreach from=$countries item=v}
					<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
		</div>
		{/if}
		{if $conf.eo_state_show}
		<div class="required text form-group">
                <label for="id_state">{l s='State' mod='expressorder'}{if $conf.eo_state_required}<sup>*</sup>{/if}</label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
		</div>
		{/if}
		{if $conf.eo_other_show}
		<div class="required text form-group">
                <label for="other">Введите свои пожелания по заказу{if $conf.eo_other_required}<sup>*</sup>{/if}</label>
                <textarea class="form-control text{if $conf.eo_other_required} is_required{/if}" cols="40" id="other" name="other">{if isset($smarty.post.other)}{$smarty.post.other|escape}{/if}</textarea>
		</div>
		{/if}
		<div class="text">
			<input type="submit" name="submitPwExpressOrder" id="submitAccount" value="{l s='Подтвердить заказ' mod='expressorder'}" class="btn btn-default button button-medium" />
		</div>
	</div>
	<input type="hidden" name="email_create" value="1" />
	{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
</form>
</div>
{/if}
