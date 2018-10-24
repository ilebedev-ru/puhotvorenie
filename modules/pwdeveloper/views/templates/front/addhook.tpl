<h1>Привязать модуль к хуку</h1>
{foreach from=$errors item=error}
<p>{$error}</p>
{/foreach}
<form method="POST">
<label>Модуль</label>
<div class="margin-form">
	<select name="mod">
	{foreach from=$modules item=module}
	<option value="{$module.name}">{$module.name}</option>
	{/foreach}
	<select>
</div>
<label>Хук</label>
<div class="margin-form"><input type="text" name="hooktext" /></div>
<div class="margin-form">
	<select name="hook">
	<option value="0">--</option>
	{foreach from=$hooks item=hook}
	<option value="{$hook.name}">{$hook.name}</option>
	{/foreach}
	<select>
</div>
<input type="submit" name="addHook" value="Добавить" />
</form>