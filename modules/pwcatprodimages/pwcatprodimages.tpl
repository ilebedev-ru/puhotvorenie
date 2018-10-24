{*Add it to product-list.tpl after cover img

In my template:

div.product-container
	div.image
		a.product_image
			img.img
			span.product-additional
			{code}
		...
	...
...	
*}

{foreach from=$product.images item=img}
<img class="img catprodimg" src="{$link->getImageLink($product.link_rewrite, $img, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" />
{/foreach}