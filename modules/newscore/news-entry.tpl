{if $categoryName}
{capture name=path}<a href="{$categoryLink}">{$categoryName}</a><span class="navigation-pipe">{$navigationPipe}</span>{if $entry.name}{$entry.name}{else}{$entry.meta_title}{/if}{/capture}
{else}
{capture name=path}<a href="{$link->getPageLink('news.php')}">Новости</a><span class="navigation-pipe">{$navigationPipe}</span>{if $entry.name}{$entry.name}{else}{$entry.meta_title}{/if}{/capture}
{/if}

{if !$content_only}
	{include file="$tpl_dir/breadcrumb.tpl"}
{/if}
{if isset($errors) AND $errors}
	{include file="$tpl_dir/errors.tpl"}
{/if}
{if $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
{/if}
{if $entry}
	<div class="news-entry clearfix{if $content_only} content_only{/if}">
		<h1>{if $entry.name}{$entry.name}{else}{$entry.meta_title}{/if}</h1>
		{$entry.content}
	</div>
	{$HOOK_NEWS_FOOTER_CONTENT}
	{if $entry.product_list}
		<div class="ttl">Рекомендуем:</div>	
		{include file="$tpl_dir/product-list.tpl" products=$entry.product_list}
	{/if}
{/if}
{literal}
<script type="text/javascript">(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
    var h=d[g]('body')[0];
    h.appendChild(s);
  }})();</script>
<div class="pluso" data-background="transparent" data-options="medium,square,line,horizontal,counter,theme=01" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir"></div>
{/literal}
{*
<br/>
{literal}
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?34"></script>

<script type="text/javascript">
  VK.init({apiId: 2320149, onlyWidgets: true});
</script>

<div id="vk_comments"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 10, width: "450", attach: "*"});
</script>{/literal}	*}
