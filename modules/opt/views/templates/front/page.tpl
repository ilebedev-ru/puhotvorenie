<div class="alert alert-success">Ваша страница</div>
{if isset($list)}
    <h2>Список</h2>
    <ul>
        {foreach from=$list item=elem}
            <li>{$elem.name}</li>
        {/foreach}
    </ul>
{/if}