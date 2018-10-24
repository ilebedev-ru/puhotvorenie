<!--pwexpressorder-->
<div style="display:none">
	<noindex>
	<div class="oneclickbuy">
	  <span class="title">Купить в 1 клик</span>
	  <div class="text">Внимание!<br />
	  Покупая в один клик, Вы отправляете заявку на один товар.<br>
	  Если вы хотите купить несколько товаров, <br>пожалуйста, положите их в <a href="/order">корзину</a>.</div>
<br>
        <div class="form">
		<form action="{$link->getModuleLink('pwexpressorder', 'display')}" method="POST" class="std">
		  <input type="hidden" name="id_product" value="0">
		  {if !empty($conf.eo_fname_show)}
		  <p class="text {if $conf.eo_fname_required}required{/if}">
			<label>Ваше имя</label>
			<input type="text" name="firstname" value="{if isset($customer->firstname)}{$customer->firstname}{/if}">
			{if $conf.eo_fname_required}<sup>*</sup>{/if}
		  </p>
		  {/if}
		  {if !empty($conf.eo_lname_show)}
		  <p class="text {if $conf.eo_lname_required}required{/if}">
			<label>Ваша фамилия</label>
			<input type="text" name="lastname" value="{if isset($customer->lastname)}{$customer->lastname}{/if}">
			{if $conf.eo_lname_required}<sup>*</sup>{/if}
		  </p>
		  {/if}
		  {if !empty($conf.eo_email_show)}
		  <p class="text {if $conf.eo_email_required}required{/if}">
			<label>Ваш e-mail</label>
			<input type="text" name="email" value="{if isset($customer->email)}{$customer->email}{/if}">
			{if $conf.eo_email_required}<sup>*</sup>{/if}
		  </p>
		  {/if}
		  {if !empty($conf.eo_password_show) && !$isLogged}
			<p class="required text">
					<label for="fname">Ваш пароль</label>
					<input type="password" class="text{if $conf.eo_password_required} required{/if}" id="password" name="password" value="{if isset($smarty.post.password)}{$smarty.post.password|escape}{/if}" />
					{if $conf.eo_password_required}<sup>*</sup>{/if}
			</p>
			{/if}
		  {if !empty($conf.eo_mobilephone_show)}
		  <p class="text {if $conf.eo_mobilephone_required}required{/if}">
			<label>Ваш телефон</label>
			<input type="text" name="phone" value="{if isset($phone)}{$phone}{else}+7{/if}">
			{if $conf.eo_mobilephone_required}<sup>*</sup>{/if}
		  </p>
		  {/if}
		  {if !empty($conf.eo_phone_show)}
			  <p class="text {if $conf.eo_phone_required}required{/if}">
				<label>Ваш телефон</label>
				<input type="text" name="phone" value="{if isset($phone)}{$phone}{else}+7{/if}">
				{if $conf.eo_phone_required}<sup>*</sup>{/if}
			  </p>
		   {/if}
            <br>
		  <p class="text">
			<input type="submit" name="submitPwExpressOrder" class="button" value="Оформить">
		  </p>
		</form>
	  </div>
	</div>	
	</noindex>
</div>
<!--/pwexpressorder-->