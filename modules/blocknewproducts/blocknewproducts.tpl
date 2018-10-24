{if $new_products !== false}
	<span class="hd2">Новинки</span>
	<div class="catalog">
		{foreach from=$new_products item=newproduct name='newProducts'}
		<div class="item">
			<span class="img-wrp"><a class="product_image" href="{$newproduct.link}" title="{$newproduct.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($newproduct.link_rewrite, $newproduct.id_image, 'home')}" alt="{$newproduct.legend|escape:html:'UTF-8'}" /></a></span>
			<a href="{$newproduct.link}" class="name">{$newproduct.name|escape:html:'UTF-8'}</a>
			<div class="btm nuclear">
				<span class="prise">{displayPrice price=$newproduct.price}</span>
				<a class="buy ajax_add_to_cart_button" rel="nofollow ajax_id_product_{$newproduct.id_product}" href="{$link->getPageLink('cart.php', true)}?qty=1&amp;id_product={$newproduct.id_product}&amp;token={$static_token}&amp;add" title="Купить">Купить</a>
			</div>
		</div>
		{/foreach}
		<p><a href="{$link->getPageLink('new-products.php')}" title="{l s='Все новинки' mod='blocknewproducts'}" class="button">{l s='Все новинки' mod='blocknewproducts'}</a></p>
	</div>
{/if}
