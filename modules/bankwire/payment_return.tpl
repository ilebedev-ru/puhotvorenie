{if $status == 'ok'}
	<p>{l s='Your order on' mod='bankwire'} <span class="bold">{$shop_name}</span> {l s='оформлен.' mod='bankwire'}
		<br /><br />
		<a href="/content/5-dostavka-i-oplata" title=""><img src="{$base_dir}modules/bankform/logo.gif" alt="" class="icon" />Инструкция оплаты через QIWI</a>
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our customer support team.' mod='bankwire'} 
		<a href="{$link->getPageLink('contact-form.php', true)}">{l s='customer support' mod='bankwire'}</a>.
	</p>
{/if}
