{if isset($links) AND $links}
    <ul class="header__top-nav">
        {foreach from=$links item=link}
          <li><a href="{$link.url}">{$link.name}</a></li>
        {/foreach}
    </ul>   
{/if}