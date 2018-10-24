{if $categoryName}
	{capture name=path}{$categoryName}{/capture}
{else}
	{capture name=path}{l s='Новости' mod='newscore'}{/capture}
{/if}
{include file="$tpl_dir/breadcrumb.tpl"}	

<div class="news">
	<h1>{if $categoryName}{$categoryName}{else}{l s='Новости' mod='newscore'}{/if}</h1>
{if $news|@count}
	{foreach from=$news item=row}
		<div class="item">
			{if $row.image}<a href="{$row.link}" class="img"><img width="100" src="{$row.image}" alt="{$bloglink.name}"></a>{/if}
			<a href="{$row.link}" class="hd">{if $row.categoryName}{$row.categoryName} » {/if}{if $row.name}{$row.name}{else}{$row.meta_title}{/if}</a>
			<div class="blog-row nuclear">
				{$row.description_short}
			</div>
		</div>
	{/foreach}
	{include file="$tpl_dir/pagination.tpl"}
{/if}
</div>
