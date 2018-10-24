<form method="POST">
    <label>Скопировать корзину: </label>
    <select name="id_cart">
        <option value="0">-</option>
        {foreach from=$carts item=cart}
        <option value="{$cart.id_cart}">{$cart.id_cart} ({$cart.lastname} {$cart.firstname})</option>
        {/foreach}
    </select><br />
    <label>Скопировать корзину из заказа: </label>
    <select name="id_order">
        <option value="0">-</option>
        {foreach from=$orders item=order}
        <option value="{$order.id_cart}">{$order.id_order} ({$order.lastname} {$order.firstname}) ({convertPrice price=$order.total_paid currency=$order.id_currency})</option>
        {/foreach}
    </select><br />
    <input type="checkbox" name="forceCustomer" id="forceCustomer" /><label for="forceCustomer">Зайти за покупателя</label>
    <input type="submit" name="submitCart" value="OK">
</form>