<h1>Создание товаров</h1>
<p class="alert alert-warning">При копировании у товаров проставляются хаотично характеристики из бд. Что удобно для проверки работы фильтра</p>
<form method="POST" action="" enctype="multipart/form-data" class="pure-form pure-form-stacked">
	<label>Название товара</label><br>
	<input type="text" name="name" value="">
	<label>Цена</label>
	От <input type="text" name="price1" value="1550"> до <input type="text" name="price2" value=""><br>
	<select name="id_category">
		{foreach from=$categories item=category}
		<option value="{$category.id_category}">{$category.name}</option>
		{/foreach}
	</select><br>
	<label>Изображение</label>
	<input type="file" name="image"><br>
	<label>Изображение 2</label>
	<input type="file" name="image2"><br>
	<label>Изображение 3</label>
	<input type="file" name="image3"><br>
	<label>Изображение 4</label>
	<input type="file" name="image4"><br>
	Описание
	<textarea name="description" cols="70" rows="10"></textarea>
	<br>
	<input type="submit" name="submitProduct" class="pure-button pure-button-primary" value="Сохранить и добавить">
</form>
<form method="POST" enctype="multipart/form-data"  class="pure-form pure-form-stacked">
	<h3>Сделать копию товара</h3>
	ID: <input type="text" name="id_copy_product" value="{if isset($smarty.post.id_copy_product) &&  $smarty.post.id_copy_product}{$smarty.post.id_copy_product}{/if}">
	<select name="count">
		<option {if isset($smarty.post.count) && $smarty.post.count == "2"} selected{/if}>2</option>
		<option {if isset($smarty.post.count) && $smarty.post.count == "4"} selected{/if}>4</option>
		<option {if isset($smarty.post.count) && $smarty.post.count == "10"} selected{/if}>10</option>
		<option {if isset($smarty.post.count) && $smarty.post.count == "20"} selected{/if}>20</option>
	</select>
	<input type="submit" class="pure-button pure-button-primary" name="submitProductCopy" value="Растиражировать">
</form>