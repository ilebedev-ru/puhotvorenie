
{capture name=path}{l s='Price drop'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Price drop'}</h1>
{if $text}
	<p class="text-price-drop">
		{$text}
	</p>
{/if}

{if $products}
	{include file="$tpl_dir./product-sort.tpl"}
	{include file="$tpl_dir./product-list.tpl" products=$products}
	{include file="$tpl_dir./pagination.tpl"}
{else}
	<p class="warning">{l s='No price drop.'}</p>
{/if}
