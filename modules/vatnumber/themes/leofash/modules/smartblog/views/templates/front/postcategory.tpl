{capture name=path}<a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='All Blog News' mod='smartblog'}</a>
    {*include file="$tpl_dir./breadcrumb.tpl"*}
    {if $title_category != ''}
        <span class="navigation-pipe">{$navigationPipe}</span>{$title_category}{/if}{/capture}
{if $postcategory == ''}
    {if $title_category != ''}
        <p class="error">{l s='No Post in Category' mod='smartblog'}</p>
    {else}
        <p class="error">{l s='No Post in Blog' mod='smartblog'}</p>
    {/if}
{else}
    {if $smartdisablecatimg == '1'}
        {assign var="activeimgincat" value='0'}
        {$activeimgincat = $smartshownoimg}
        {if $title_category != ''}
            <h1>{$title_category}</h1>
            {foreach from=$categoryinfo item=category}
                <div id="sdsblogCategory">
                    {if ($cat_image != "no" && $activeimgincat == 0) || $activeimgincat == 1}
                        <img alt="{$category.meta_title}"
                             src="{$modules_dir}/smartblog/images/category/{$cat_image}-home-default.jpg"
                             class="imageFeatured">
                    {/if}
                    {$category.description}
                </div>
            {/foreach}
        {else}
            <h1 id="article-list-title" >{l s='Cтатьи' mod='smartblog'}</h1>
        {/if}
    {/if}
    <div id="article-list-block">
        {foreach from=$postcategory item=post}
            {include file="./category_loop.tpl" postcategory=$postcategory}
        {/foreach}
    </div>
    {if !empty($pagenums)}
        <ul id="article-list-pagination">
            {for $k=0 to $pagenums}
                {if $title_category != ''}
                    {assign var="options" value=null}
                    {$options.page = $k+1}
                    {$options.id_category = $id_category}
                    {$options.slug = $cat_link_rewrite}
                {else}
                    {assign var="options" value=null}
                    {$options.page = $k+1}
                {/if}
                {if ($k+1) == $c}
                    <li><span class="article-list-active">{$k+1}</span></li>
                {else}
                    {if $title_category != ''}
                        {if !$k}
                            {assign var="options" value=null}
                            {$options.id_category = $id_category}
                            {$options.slug = $cat_link_rewrite}
                            {$link = smartblog::GetSmartBlogLink('smartblog_category',$options)}
                        {else}
                            {$link = smartblog::GetSmartBlogLink('smartblog_category_pagination',$options)}
                        {/if}
                        <li><a class="article-list-link" href="{$link}">{$k+1}</a></li>
                    {else}
                        {if !$k}
                            {$link = smartblog::GetSmartBlogLink('smartblog')}
                        {else}
                            {$link = smartblog::GetSmartBlogLink('smartblog_list_pagination',$options)}
                        {/if}
                        <li><a class="article-list-link" href="{$link}">{$k+1}</a></li>
                    {/if}
                {/if}
            {/for}
        </ul>
    {/if}
{/if}
{if isset($smartcustomcss)}
    <style>
        {$smartcustomcss}
    </style>
{/if}