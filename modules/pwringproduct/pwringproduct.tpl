<!-- Модуль кольцевой перелинковки pwringproducts -->
{if $categoryProducts}
 <div class="pwringproduct">
	 <div class="block_content box-line h3">
		 <div>
			 <ul id="productTabs-6" class="nav nav-tabs">
				 <li class="active"><a>Похожие товары</a></li>
			 </ul>
		 </div>
	 </div>
	 {include file="$tpl_dir./product-list.tpl" products=$categoryProducts}
	<div class="clear"></div>
 </div>
 {/if}