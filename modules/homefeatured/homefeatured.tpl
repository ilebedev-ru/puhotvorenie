<!-- MODULE Home Featured Products -->
<span class="ttl">Популярные товары</span>
{if isset($products) AND $products}
<div class="catalog nuclear">
	{foreach from=$products item=product name=homeFeaturedProducts}
	<div class="item">
		<span class="img-wrp">
		<a{if isset($product.link)} href="{$product.link}"{/if} title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
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
<!-- /MODULE Home Featured Products -->
