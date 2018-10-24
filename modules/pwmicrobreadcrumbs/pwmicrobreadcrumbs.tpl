<!-- Breadcrumb -->

{if isset($smarty.capture.path)}{assign var='path' value=$smarty.capture.path}{/if}

<div class="breadcrumb clearfix">
    {assign var=path_lines value='<span class="navigation-pipe">></span>'|explode:$path}
    <ol itemscope itemtype="http://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope
            itemtype="http://schema.org/ListItem">
            <a itemprop="item" class="home" href="{$base_dir}" title="{l s='Return to Home'}">
                {l s='Главная'}
            </a>
            <meta itemprop="name" content="{l s='Return to Home'}" />
            <meta itemprop="position" content="1" />
        </li>
        {foreach from=$path_lines item=line name=path_lines}
            <li itemprop="itemListElement" itemscope
                itemtype="http://schema.org/ListItem">
                {$line}
                <meta itemprop="position" content="{$smarty.foreach.path_lines.iteration + 1}" />
                {*{if $smarty.foreach.path_lines.last}<meta itemprop="url" content="{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}">{/if}*}
            </li>
        {/foreach}
    </ol>
</div>

{if isset($smarty.get.search_query) && isset($smarty.get.results) && $smarty.get.results > 1 && isset($smarty.server.HTTP_REFERER)}
    <div class="pull-right">
        <strong>
            <a href="{$smarty.server.HTTP_REFERER|escape}" name="back">
                <i class="icon-chevron-left left"></i> {l s='Back to Search results for "%s" (%d other results)' sprintf=[$smarty.get.search_query,$smarty.get.results]}
            </a>
        </strong>
    </div>
{/if}
<!-- /Breadcrumb -->