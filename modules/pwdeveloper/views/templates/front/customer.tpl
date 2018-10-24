<ul>
{foreach from=$customers item=customer}
<li><a href="?customer={$customer.id_customer}">{$customer.firstname} {$customer.lastname}</a> ({$customer.email}) <a href="?auth={$customer.id_customer}">Авторизоваться</a></li>
{/foreach}
</ul>
<a href="?add">Создать покупателя</a>
{if isset($smarty.get.add)}
<fieldset>
<form method="POST">
<label>Имя</label>
<div class="margin-form"><input type="text" name="firstname" /></div>
<label>Фамилия</label>
<div class="margin-form"><input type="text" name="lastname" /></div>
<label>Пароль</label>
<div class="margin-form"><input type="text" name="password" /></div>
<label>email</label>
<div class="margin-form"><input type="text" name="email" /></div>
<input type="submit" name="addCustomer" value="Добавить" >
</form>
</fieldset>
{/if}
{if isset($smarty.get.customer)}
<fieldset>
<form method="POST">
<input type="hidden" name="id_customer" value="{$smarty.get.customer}" />
<label>Имя</label>
<div class="margin-form"><input type="text" name="firstname" value="{$cust->firstname}" /></div>
<label>Фамилия</label>
<div class="margin-form"><input type="text" name="lastname" value="{$cust->lastname}" /></div>
<label>Пароль</label>
<div class="margin-form"><input type="text" name="password" value="" /></div>
<label>email</label>
<div class="margin-form"><input type="text" name="email" value="{$cust->email}" /></div>
<input type="submit" name="editCustomer" value="Изменить" >
</form>
</fieldset>
{/if}