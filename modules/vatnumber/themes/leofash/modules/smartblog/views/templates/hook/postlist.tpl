<div id="article-list-block">
        {foreach from=$postlist item=post}
            {include file="./postlist_loop.tpl" postcategory=$postlist}
        {/foreach}
    </div>
