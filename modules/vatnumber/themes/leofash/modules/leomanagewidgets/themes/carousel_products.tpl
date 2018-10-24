{if !empty($mproducts)}
{$itemsperpage=$datas.configs.itemspage}
{$columnspage=$datas.configs.columns}
<div class="carousel slide" id="{$tabname}-{$datas.id_leomanagewidgets}-carousel">
	 {if count($mproducts)>1}	
	<a class="carousel-control left" href="#{$tabname}-{$datas.id_leomanagewidgets}-carousel"   data-slide="prev">{l s='prev' mod='leomanagewidgets'}</a>
	<a class="carousel-control right" href="#{$tabname}-{$datas.id_leomanagewidgets}-carousel"  data-slide="next">{l s='next' mod='leomanagewidgets'}</a>
	{/if}
	<div class="carousel-inner">
	{foreach from=$mproducts item=products name=mypLoop}
		<div class="item {if $smarty.foreach.mypLoop.first}active{/if}">
				{foreach from=$products item=product name=products}
				{if $product@iteration%$columnspage==1&&$columnspage>1}
				  <div class="row product-block ">
				{/if}
				<div class="col-md-4 col-md-{$datas.scolumn} ajax_block_product">
					<div class="product-container">
						<div class="image">
							<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link " title="{$product.name|escape:'htmlall':'UTF-8'}">						
								<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" class="img-responsive img" />
								<span class="product-additional" rel="{$product.id_product}"></span>
							</a>
							{if isset($product.new) && $product.new == 1}<span class="new"><span>{l s='New' mod='leomanagewidgets'}</span></span>{/if}
							<div class="product-btn">
								<div class="cart">
									{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
										{if ($product.allow_oosp || $product.quantity > 0)}
											{if isset($static_token)}
												<a class=" h6 btn ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html'}" title="{l s='Add to cart' mod='leomanagewidgets'}">
												<i class="fa fa-shopping-cart"></i>{l s='Add to cart' mod='leomanagewidgets'}</a>
											{else}
												<a class=" h6 btn ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}", false)|escape:'html'}" title="{l s='Add to cart' mod='leomanagewidgets'}">
												<i class="fa fa-shopping-cart"></i>{l s='Add to cart' mod='leomanagewidgets'}</a>
											{/if}						 
										{/if}
									{/if}	
								</div>
								<div class="wishlist">
									{if $LEO_WISHLIST_ENABLE}
										 <a href="#" id="wishlist_button{$product.id_product}" title="{l s='Add to wishlist' mod='leomanagewidgets'}" class="btn btn-add-wishlist btn" onclick="LeoWishlistCart('wishlist_block_list', 'add', '{$product.id_product}', $('#idCombination').val(), 1 ); return false;"><i class="fa fa-heart"></i></a>
									 {/if}
								</div>
								<div class="view">
									<a class="btn lnk_more" href="{$product.link}" title="{l s='View' mod='leomanagewidgets'}"><i class=" fa fa-eye"></i></a>
								</div>
							</div>
						</div>
						<div class="product-meta">
							<p class="name">{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}<a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></p>
							<div class="description">{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}</div>
							
							{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								<div class="content_price">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									<span class="price">
										{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
									</span>
									{/if}	
								</div>

							{/if}
						</div>
						<div class="product_bottom hidden-xs">							
							<a class="quick-view h6" title="{l s='Quick View' mod='leomanagewidgets'}" href="{if $product.link|strpos:"?"}{$product.link|cat:'&content_only=1'|escape:'htmlall':'UTF-8'}{else}{$product.link|cat:'?content_only=1'|escape:'htmlall':'UTF-8'}{/if}">
								<i class="fa fa-plus"></i>
								{l s='Quick View' mod='leomanagewidgets'}</a>
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