<h3>Сгенерировать данные</h3>
{foreach from=$errors item=error}
<p class="error">{$error}</p>
{/foreach}
<form method="POST"><input type="submit" name="addCustomer" value="Создать покупателя" /></form>
<form method="POST"><input type="submit" name="addProduct" value="Создать продукт" /></form>
{if $cookie->isLogged()}<form method="POST"><input type="submit" name="addCart" value="Добавить тестовую корзину" /></form>{/if}
{if Module::isInstalled('webmoney')}<a href="/module/webmoney/redirect?id_cart=">Перейти к оплате Webmoney</a>{/if}
{if Module::isInstalled('yamoney')}<a href="/module/webmoney/redirect?id_cart=">Перейти к оплате Yandex</a>{/if}
{if Module::isInstalled('robokassa')}<a href="/module/robokassa/redirect?id_cart=">Перейти к оплате Robokassa</a>{/if}