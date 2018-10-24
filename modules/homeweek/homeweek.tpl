<div class="week">
	{*<span class="ttl"><img src="/img/flower.png" width="32" height="32">Весенняя распродажа продлена до 30 сентября, успейте купить!</span>*}
	{if isset($week_products) AND $week_products}
	<div class="catalog nuclear">
		{foreach from=$week_products item=product name=week}
		<div class="item{if $smarty.foreach.week.last} item-last{/if}">
			<span class="new">&nbsp;</span>
			<span class="img-wrp">
			<a{if isset($product.link)} href="{$product.link}"{/if} title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'week')}" height="{$weekSize.height}" width="{$weekSize.width}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
				{if $product.on_sale}<div class="hit">&nbsp;</div>{/if}
			</span>
			<a href="{$product.link}" class="name">{$product.name|escape:'htmlall':'UTF-8'}</a>
			<div class="btm nuclear">
				<span class="prise">{convertPrice price=$product.price}</span>
				{if ($product.allow_oosp || $product.quantity > 0)}<a class="buy ajax_add_to_cart_button" rel="nofollow ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart.php', true)}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add">Купить</a>
				{else}<span class="buy disabled" title="Нет в наличии">Купить</span>{/if}
			</div>
		</div>
		{/foreach}
	</div>
	{/if}
</div>
