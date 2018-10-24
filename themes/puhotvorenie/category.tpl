
{include file="$tpl_dir./breadcrumb.tpl"}
{include file="$tpl_dir./errors.tpl"}

{if isset($category)}
	{if $category->id AND $category->active}
		<img class="light_banner" src="/img/lt.png" alt="легкое и теплое">
		
		<h1>
			{strip}
				{$category->name|escape:'htmlall':'UTF-8'}
				{if isset($categoryNameComplement)}
					{$categoryNameComplement|escape:'htmlall':'UTF-8'}
				{/if}
			{/strip}
		</h1>

		{if $products}
				{include file="$tpl_dir./product-sort.tpl"}
				{include file="$tpl_dir./product-list.tpl" products=$products}
				{include file="$tpl_dir./pagination.tpl"}
		{elseif !isset($subcategories)}
			<p class="warning">{l s='There are no products in this category.'}</p>
		{/if}
		{if $category->description}
			<div class="cat_desc">{$category->description}</div>
		{/if}
	{elseif $category->id}
		<p class="warning">{l s='This category is currently unavailable.'}</p>
	{/if}
{/if}