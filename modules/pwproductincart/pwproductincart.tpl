<!-- MODULE Home Featured Products -->
<span class="ttl">Популярные товары</span>
{if isset($products) AND $products}
	<div class="catalog nuclear">
		{include file="./product-list.tpl" products=$products}
	</div>
{/if}
