<div class="recentorders">
	<span class="ttl">Последние заказы</span>
	<ul>
	{foreach from=$result item=item}
		<li class="ajax_product">
			<a class="hd" href="{$item.product_link}">{$item.product_name|escape:'htmlall':'UTF-8'}</a>
			<a href="{$item.product_link}" title="{$item.product_name|escape:'htmlall':'UTF-8'}" class="product_image">
				<img src="{$link->getImageLink($item.link_rewrite, $item.id_image, 'home')}" alt="{$item.product_name|escape:'htmlall':'UTF-8'}" />
			</a>
			<div class="date">{$item.date}</div>
			<div class="delivery">{$item.address}</div>
		</li>
	{/foreach}
	</ul>
</div>

