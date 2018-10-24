<h1>Почистить данные в магазине</h1>
{if isset($errors) && $errors}
    <div class="alert alert-errors">
        {foreach from=$errors item=error}
            <p class="error">{$error}</p>
        {/foreach}
    </div>
{/if}
<p class="alert alert-warning">Внимание модуль удаляет все существующие товары и товары которые были удалены не корректно. Так же полностью очищает папку с картинками</p>
<form method="POST"><input type="submit" name="cleanProducts" value="Почистить товары" /></form>
