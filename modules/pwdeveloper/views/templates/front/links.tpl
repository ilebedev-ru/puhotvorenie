{if $generatedLink}
<a href="{$generatedLink}">{$generatedLink}</a>
{/if}

<form method="POST">
    <div>
        <label for="confirmationLink">{l s="Перейти на order confirmation"}</label><input placeholder="id_order" type="text" name="confirmationLink" id="confirmationLink" />
    </div>
    <div>
        <label for="id_category">{l s="Перейти в категорию"}</label><input placeholder="id_category" type="text" name="id_category" id="id_category" />
    </div>
    <div>
        <label for="id_product">{l s="Перейти на карточку товара"}</label><input placeholder="id_product" type="text" name="id_product" id="id_product" />
    </div>
    <div>
        <label for="module_name">{l s="Перейти на фронтконтоллер модуля"}:</label><br />
        <input placeholder="Имя модуля" type="text" name="module_name" id="module_name" /><br />
        <input placeholder="Имя контроллера" type="text" name="controller_name" id="controller_name" /><br />
        <input placeholder="Доп. параметры" type="text" name="addMod" id="addMod" />
    </div>
    <div><input type="submit" value="Перейти" /></div>
</form>