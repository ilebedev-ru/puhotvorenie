<div id="pwproductincart_block_center" class="block products_block clearfix">
	{if isset($products) AND $products}
		<h1 class="page_heading">Не забудьте товары с максимальной скидкой</h1>
		<div class="block_content row" >
            {include file="$tpl_dir./product-list.tpl" products=$products}
		</div>
	{/if}
</div>
