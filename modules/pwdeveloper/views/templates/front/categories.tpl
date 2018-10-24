<h1>Создание категорий</h1>
<form method="POST" class="pure-form pure-form-stacked">
	<label>Название категорий, чтобы указать подкатегории, пишите -</label>
	<textarea name="catlist" cols="70" rows="10">Категория
Категория2
Категория3
-ПодКатегория1
--ПодПодКатегория1</textarea>
	<input type="submit" name="submitCatList" class="pure-button pure-button-primary"value="Сохранить и добавить">
</form>

<h2>Создание посадочных страниц с выборками товаров</h2>
<form method="POST" class="pure-form pure-form-stacked">
	<label>Название категорий</label>
	<textarea name="catlist" cols="70" rows="10" placeholder="Категория 1">{if isset($smarty.post.catlist)}{$smarty.post.catlist}{/if}</textarea>
	<br />
	<label for="">ID категории откуда брать товар</label>
	<input type="text" name="id_from" value="{if isset($smarty.post.id_from)}{$smarty.post.id_from}{/if}">
	<br />
	<label for="">ID родителя(оставьте пустым, если нужно создать в корне)</label>
	<input type="text" name="id_parent" value="{if isset($smarty.post.id_parent)}{$smarty.post.id_parent}{/if}">
	<br />
	<input type="submit" name="submitCategoryReplicate" class="pure-button pure-button-primary" value="Сохранить и добавить">
</form>
