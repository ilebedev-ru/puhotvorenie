{if empty($fast) || !$fast}
	{capture name=path}Оформление заказа{/capture}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}
{if isset($fast)}<br />{/if}
<h1 class="page-heading">Оформление заказа</h1>
{include file="$tpl_dir./errors.tpl"}
{hook h='displaySocialAuth'}
<div class="pwexpressorder" id="pwexpressorder">
	<form action="{$pwexpressorder_uri}" method="post" class="std" id="pwexpressorder_form">
		{if isset($carriers) && count($carriers)}
			<div class="carrier-select">
                <h2>Выберите способ доставки</h2>
                <table class="table" id="carrier-select">

                {foreach from=$carriers item=car name=carriers}
                {*<tr{if $smarty.foreach.carriers.last} class="last"{/if}">
                    <td class="radio-select"><input type="radio" name="id_carrier" id="carrier{$car.id_carrier}" value="{$car.id_carrier}"{if isset($smarty.post.id_carrier) && $smarty.post.id_carrier == $car.id_carrier} checked="checked"{/if}></td>
                    <td class="name"><label for="carrier{$car.id_carrier}">{$car.name}</label></td>
                    <td class="delay">{$car.delay}</td>
                    <td class="cost">{if $car.price == 0}<span class="free">Бесплатно</span>{else}{convertPrice price=$car.price}{/if}</td>
                </tr>*}
					{if ($smarty.foreach.carriers.iteration == 1)}
						<h3>По России</h3>
                    {elseif ($smarty.foreach.carriers.iteration == 3)}
						<h3>По Санкт-Петербургу</h3>
					{/if}

					<div class="deliv-type">
						<span class="radio-select"><input type="radio" name="id_carrier" id="carrier{$car.id_carrier}" value="{$car.id_carrier}"{if isset($smarty.post.id_carrier) && $smarty.post.id_carrier == $car.id_carrier} checked="checked"{/if}></span>
						<span class="name">
							<label for="carrier{$car.id_carrier}">{$car.name}</label>
							<img class="logo" src="{$car.logo}" alt="">

						</span>
						<span class="delay">{$car.delay}</span>
						<span class="cost">{if $car.price == 0}<span class="free">Бесплатно</span>{else}{convertPrice price=$car.price}{/if}</span>
					</div>
                {/foreach}
                </table>
		    </div>
		{/if}
		<div class="opc_account_form">
			{if isset($conf.eo_fname_show) && $conf.eo_fname_show}
			<div class="text form-group" id='first_p'>
					<label for="firstname">Введите имя{if isset($conf.eo_fname_required) && $conf.eo_fname_required}<sup>*</sup>{/if}</label>
					<input type="text" placeholder="Как к Вам обращаться" class="form-control validate text{if isset($conf.eo_fname_required) && $conf.eo_fname_required} is_required{/if}" data-validate="isName" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape}{elseif isset($customer->firstname)}{$customer->firstname}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_lname_show) && $conf.eo_lname_show}
			<div class="text form-group">
					<label for="lastname">Фамилия{if isset($conf.eo_lname_required) && $conf.eo_lname_required}<sup>*</sup>{/if}</label>
					<input type="text" class="form-control text{if isset($conf.eo_lname_required) && $conf.eo_lname_required} is_required{/if}" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape}{elseif isset($customer->lastname)}{$customer->lastname}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_email_show) && $conf.eo_email_show}
			<div class="text form-group">
					<label for="email">{l s='Введите E-mail' mod='expressorder'}{if isset($conf.eo_email_required) && $conf.eo_email_required}<sup>*</sup>{/if}</label>
					<input type="text" placeholder="Для уведомлений о статусе заказа" class="form-control validate  text email{if isset($conf.eo_email_required) && $conf.eo_email_required} is_required{/if}" data-validate="isEmail" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape}{elseif isset($customer->email)}{$customer->email}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_password_show) && $conf.eo_password_show && !$isLogged}
			<div class="text form-group">
					<label for="fname">{l s='Ваш пароль' mod='expressorder'}{if $conf.eo_password_required}<sup>*</sup>{/if}</label>
					<input type="password" class="form-control text{if $conf.eo_password_required} is_required{/if}" id="password" name="password" value="{if isset($smarty.post.password)}{$smarty.post.password|escape}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_mobilephone_show) && $conf.eo_mobilephone_show}
			<div class="text form-group">
					<label for="mobile_phone">{l s='Mobile phone' mod='expressorder'}{if $conf.eo_mobilephone_required}<sup>*</sup>{/if}</label>
					<input type="text" class="form-control text{if $conf.eo_mobilephone_required} is_required{/if}" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape}{elseif isset($address.phone_mobile)}{$address.phone_mobile}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_address_show) && $conf.eo_address_show}
			<div class="text form-group">
					<label for="address">Адрес{if $conf.eo_address_required}<sup>*</sup>{/if}</label>
					<input type="text" class="form-control text{if $conf.eo_address_required} is_required{/if}" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape}{elseif isset($address.address1)}{$address.address1}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_address2_show) && $conf.eo_address2_show}
			<div class="text form-group">
					<label for="address2">{l s='Address (2)' mod='expressorder'}{if $conf.eo_address2_required}<sup>*</sup>{/if}</label>
					<input type="text" class="form-control text{if $conf.eo_address2_required} is_required{/if}" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_zip_show) && $conf.eo_zip_show}
			<div class="text form-group">
					<label for="zip">{l s='Почтовый индекс' mod='expressorder'}{if $conf.eo_zip_required}<sup>*</sup>{/if}</label>
					<input type="text" class="form-control text{if $conf.eo_zip_required} is_required{/if}" id="zip" name="zip" value="{if isset($smarty.post.zip)}{$smarty.post.zip|escape}{elseif isset($address.postcode)}{$address.postcode}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_city_show) && $conf.eo_city_show}
			<div class="text form-group">
					<label for="city">Город{if $conf.eo_city_required}<sup>*</sup>{/if}</label>
					<input type="text" class="form-control text{if $conf.eo_city_required} is_required{/if}" id="city" name="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape}{elseif isset($address.city)}{$address.city}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_phone_show) && $conf.eo_phone_show}
			<div class="text form-group">
					<label for="phone">Введите телефон{if $conf.eo_phone_required}<sup>*</sup>{/if}</label>
					<input type="text" placeholder="Для подтверждения заказа" class="form-control validate text{if isset($conf.eo_phone_required) && $conf.eo_phone_required} is_required{/if}" data-validate="isPhoneNumber" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape}{elseif isset($address.phone)}{$address.phone}{/if}" />
			</div>
			{/if}
			{if isset($conf.eo_country_show) && $conf.eo_country_show}
			<div class="text form-group">
					<label for="id_country">{l s='Страна' mod='expressorder'}{if $conf.eo_country_required}<sup>*</sup>{/if}</label>
					<select name="id_country" id="id_country">
						<option value="">-</option>
						{foreach from=$countries item=v}
						<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
			</div>
			{/if}
			{if isset($conf.eo_state_show) && $conf.eo_state_show}
			<div class="text form-group">
					<label for="id_state">{l s='State' mod='expressorder'}{if $conf.eo_state_required}<sup>*</sup>{/if}</label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
			</div>
			{/if}
			{if isset($conf.eo_other_show) && $conf.eo_other_show}
			<div class="text form-group">
					<label for="other">Введите свои пожелания по заказу{if isset($conf.eo_other_required) && $conf.eo_other_required}<sup>*</sup>{/if}</label>
					<textarea placeholder="Время доставки, вопросы по поводу товара, дополнительные пожелания" class="form-control text{if $conf.eo_other_required} is_required{/if}" cols="40" rows="5" id="other" name="other">{if isset($smarty.post.other)}{$smarty.post.other|escape}{/if}</textarea>
			</div>
			{/if}

			<div class="checkbox">
	            <label>
	              <input class="" name="addGiftBox" type="checkbox"> Добавить <a class="giftBoxFancyBox" href="{$modules_dir}/pwexpressorder/img/fancyimage.png" title="Подарочная упаковка - 100руб"><u>подарочную упаковку</u></a> <strong>(+100руб)</strong>
	            </label>
	        </div>

			<script type="text/javascript">
			    $(document).ready(function(){
			        $('.giftBoxFancyBox').fancybox({
			            maxWidth    : 800,
			            maxHeight   : 600,
			            fitToView   : false,
			            width       : '70%',
			            height      : '70%',
			            autoSize    : false,
			            closeClick  : false,
			            openEffect  : 'none',
			            closeEffect : 'none'
			        });

			    });
			</script>
            <div class="help_info"><i class="icon-info"></i>{l s='Перед отправкой заказа - наш оператор свяжется с Вами и сообщит всю необходимую информацию.' mod='pwexpressorder'}</div>

			<div class="text">
				<input type="submit" name="submitPwExpressOrder" id="submitPwExpressOrder" value="{if isset($conf.eo_payments)}{l s='Сохранить и перейти к выбору оплаты »' mod='pwexpressorder'}{else}{l s='Отправить заявку »' mod='pwexpressorder'}{/if}" class="btn btn-default button button-large" />
			</div>
		</div>
		<input type="hidden" name="email_create" value="1" />
		{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
	</form>
</div>
