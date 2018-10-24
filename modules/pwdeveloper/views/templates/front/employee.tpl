{foreach from=$errors item=error}
{$error}<br />
{/foreach}
<ul>
{foreach from=$employees item=emp}
   <li>{$emp.email} (<a href='?pass={$emp.id_employee}'>Сменить пароль</a>, <a href='?auth={$emp.id_employee}'>Войти</a>)</li>
{/foreach}
</ul>
<br />
<a href="?create">Создать пользователя</a>
{if isset($smarty.get.create)}
<fieldset>
<form method="POST">
<label for="email">Email</label>
<div class="margin-form">
	<input type="text" id="email" name="email" />
</div>
<label for="password">Password</label>
<div class="margin-form">
	<input type="text" id="password" name="password" />
</div>
<input type="submit" name="createEmployee" value="Создать" />
</form>
</fieldset>
{/if}
{if isset($smarty.get.pass)}
<fieldset>
<form method="POST">
<input type="hidden" name="id_employee" value="{$smarty.get.pass}" />
<label for="email">Email</label>
<div class="margin-form">
	<input type="text" id="email" name="email" value="{$employee->email}" />
</div>
<label for="password">Password</label>
<div class="margin-form">
	<input type="text" id="password" name="password" />
</div>
<input type="submit" name="editEmployee" value="Изменить" />
</form>
</fieldset>
{/if}