{if !$fast}{capture name=path}Оформление заказа{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}{/if}
{if $fast}<br />{/if}
{if $conf.eo_jscheck}
{literal}
	<script>
		$(function(){
			$("#account-creation").submit(function(){
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
			
			$('#fullfill').change(function(){
				checkState();
			});
			checkState();
		});
		function checkState(){
			if($('#fullfill').attr('checked')) $('.dop-fields').slideDown();
			else $('.dop-fields').slideUp();
		}
	</script>
{/literal}
{/if}

{include file="$tpl_dir./errors.tpl"}

{if !$hideForm}
<link rel="stylesheet" type="text/css" media="screen" href="/modules/expressorder/css/expressorder.css" />
<form action="{$base_dir}modules/expressorder/makeorder.php" method="post" id="account-creation" class="std">
	{$HOOK_CREATE_ACCOUNT_TOP}
	{if $gift.enable == '1'}
	<fieldset class="account_creation order_creation">
		<div class="choose-var">
			<label for="gift">Добавить <a class="fancybox" href="/img/gift_wrap.png">подарочную упаковку</a> <b>{if $gift}({$gift.price} {$gift.currency}){/if}</b>
				<input type="checkbox" name="gift" id="gift">
			</label>
		</div>
	</fieldset>
	{/if}
	<h1>Оформление заказа</h1>
	{if count($carriers)}
		<fieldset class="carrier-select">
		<h3>Выберите способ доставки</h3>
		<table class="std" id="carrier-select">
		{foreach from=$carriers item=car name=carriers}
		<tr{if $smarty.foreach.carriers.first} class="first_row"{/if}>
			<td class="radio"><input type="radio" name="id_carrier" value="{$car.id_carrier}"
			{if $smarty.post.id_carrier == $car.id_carrier} checked="checked"{elseif $id_carrier==$car.id_carrier} checked="checked"{/if}>
			</td><td class="name">{$car.name}</td><td class="cost">{$car.delay}</td>
		</tr>
		{/foreach}
		</table>
	</fieldset>
	{/if}
	<fieldset class="account_creation order_creation">
		{if $conf.eo_fname_show}
		<p class="required text" id='first_p'>
                <label for="fname">Имя</label>
                <input type="text" class="text{if $conf.eo_fname_required} required{/if}" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape}{elseif isset($customer->firstname)}{$customer->firstname}{/if}" />
                {if $conf.eo_fname_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_phone_show}
		<p class="required text">
                <label for="phone">Телефон</label>
                <input type="text" placeholder="Для подтверждения заказа" class="text{if $conf.eo_phone_required} required{/if}" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape}{elseif isset($address.phone)}{$address.phone}{/if}" />
                {if $conf.eo_phone_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		<div class="choose-var">
			<input type="checkbox" name="fullfill" value="1" id="fullfill">
			<label for="fullfill">Заполнить адрес доставки</label>
		</div>
		<div class="dop-fields hidden">
		{if $conf.eo_lname_show}
		<p class="required text">
                <label for="lastname">Фамилия</label>
                <input type="text" class="text{if $conf.eo_lname_required} required{/if}" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape}{elseif isset($customer->lastname)}{$customer->lastname}{/if}" />
                {if $conf.eo_lname_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		
		<p class="required text">
                <label for="lastname">Отчество</label>
                <input type="text" class="text" id="middlename" name="middlename" value="{if isset($smarty.post.middlename)}{$smarty.post.middlename|escape}{elseif isset($address.middlename)}{$address.middlename}{/if}" />
				
		</p>
		
		{if $conf.eo_email_show}
		<p class="required text">
                <label for="email">{l s='E-mail' mod='expressorder'}</label>
                <input type="text" class="text email{if $conf.eo_email_required} required{/if}" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape}{elseif isset($customer->email)}{$customer->email}{/if}" />
                {if $conf.eo_email_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_password_show}
		<p class="required text">
                <label for="fname">{l s='Password' mod='expressorder'}</label>
                <input type="password" class="text{if $conf.eo_password_required} required{/if}" id="password" name="password" value="{if isset($smarty.post.password)}{$smarty.post.password|escape}{/if}" />
                {if $conf.eo_password_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_mobilephone_show}
		<p class="required text">
                <label for="mobile_phone">{l s='Mobile phone' mod='expressorder'}</label>
                <input type="text" class="text{if $conf.eo_mobilephone_required} required{/if}" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape}{elseif isset($address.phone_mobile)}{$address.phone_mobile}{/if}" />
                {if $conf.eo_mobilephone_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_address_show}
		<p class="required text">
                <label for="address">Адрес</label>
                <input type="text" class="text{if $conf.eo_address_required} required{/if}" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape}{elseif isset($address.address1)}{$address.address1}{/if}" />
                {if $conf.eo_address_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_address2_show}
		<p class="required text">
                <label for="address2">{l s='Address (2)' mod='expressorder'}</label>
                <input type="text" class="text{if $conf.eo_address2_required} required{/if}" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape}{/if}" />
                {if $conf.eo_address2_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_zip_show}
		<p class="required text">
                <label for="zip">{l s='Почтовый индекс' mod='expressorder'}</label>
                <input type="text" class="text{if $conf.eo_zip_required} required{/if}" id="zip" name="zip" value="{if isset($smarty.post.zip)}{$smarty.post.zip|escape}{elseif isset($address.postcode)}{$address.postcode}{/if}" />
                {if $conf.eo_zip_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_city_show}
		<p class="required text">
                <label for="city">{l s='Город' mod='expressorder'}</label>
                <input type="text" class="text{if $conf.eo_city_required} required{/if}" id="city" name="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape}{elseif isset($address.city)}{$address.city}{/if}" />
                {if $conf.eo_city_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		
		{if $conf.eo_country_show}
		<p class="required text">
                <label for="id_country">{l s='Страна' mod='expressorder'}</label>
				<select name="id_country" id="id_country">
					<option value="">-</option>
					{foreach from=$countries item=v}
					<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
                {if $conf.eo_country_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		{if $conf.eo_state_show}
		<p class="required text">
                <label for="id_state">{l s='State' mod='expressorder'}</label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
                {if $conf.eo_state_required}<sup>*</sup>{/if}
				
		</p>
		{/if}
		</div>
		{if $conf.eo_other_show}
		<p class="required text">
                <label for="other">Комментарии</label>
                <textarea placeholder="Ваши пожелания к заказу" class="text{if $conf.eo_other_required} required{/if}" cols="40" id="other" name="other">{if isset($smarty.post.other)}{$smarty.post.other|escape}{/if}</textarea>
                {if $conf.eo_other_required}<sup>*</sup>{/if}
		</p>
		{/if}
		<p class="required text">
			<input type="submit" name="submitAccount" id="submitAccount" value="{l s='Сохранить и выбрать способ оплаты »' mod='expressorder'}" class="exclusive" />
		</p>
	</fieldset>
	<p class="cart_navigation required submit">
		<input type="hidden" name="email_create" value="1" />
		{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
	</p>
</form>
{/if}
