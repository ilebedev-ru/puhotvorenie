<span class="ttl">Обратите внимание</span>
{if isset($categoryProducts) AND $categoryProducts}
<div class="catalog nuclear">
	{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
	<div class="item">
		<span class="img-wrp"><a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" title="{$categoryProduct.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$categoryProduct.name|escape:html:'UTF-8'}" /></a></span>
		<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="name">{$categoryProduct.name|escape:'htmlall':'UTF-8'}</a>
		<div class="btm nuclear">
			<span class="prise">{convertPrice price=$categoryProduct.price}</span>
			{*{if $categoryProduct.quantity > 0}*}
			<a class="buy ajax_add_to_cart_button" rel="nofollow ajax_id_product_{$categoryProduct.id_product}" href="{$link->getPageLink('cart.php', true)}?qty=1&amp;id_product={$categoryProduct.id_product}&amp;token={$static_token}&amp;add">Купить</a>
			{*{else}<span class="buy disabled" title="Нет в наличии">Купить</span>{/if}*}
		</div>
	</div>
	{/foreach}
</div>
{/if}
