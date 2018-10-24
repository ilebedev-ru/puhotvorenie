{if !empty($products)}
<div class=" carousel slide" id="{$tabname}">
	 {if count($products)>$itemsperpage}	
	<a class="carousel-control left" href="#{$tabname}"   data-slide="prev">{l s='prev' mod='blockrelatedproducts'}</a>
	<a class="carousel-control right" href="#{$tabname}"  data-slide="next">{l s='next' mod='blockrelatedproducts'}</a>
	{/if}
	<div class="carousel-inner">
	{$mproducts=array_chunk($products,$itemsperpage)}
	{foreach from=$mproducts item=products name=mypLoop}
		<div class="item {if $smarty.foreach.mypLoop.first}active{/if}">
			{foreach from=$products item=product name=products}
				{if $product@iteration%$columnspage==1&&$columnspage>1}
				  <div class="clearfix product-block row">
				{/if}
					<div class="col-sm-6 col-xs-12 col-md-{$scolumn} ajax_block_product">
						<div class="product-container">
							<div class="image">
								<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_img_link"><img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
							</div>
							<div class="product-meta">
								<h3><a href="{$product.link}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h3>
								{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
									<div class="content_price">
										<span class="price">
											{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
										</span>
									</div>
								{/if}
							</div>
						</div>
					</div>			
				{if ($product@iteration%$columnspage==0||$smarty.foreach.products.last)&&$columnspage>1}
					</div>
				{/if}				
			{/foreach}
		</div>		
	{/foreach}
	</div>
</div>
{/if}