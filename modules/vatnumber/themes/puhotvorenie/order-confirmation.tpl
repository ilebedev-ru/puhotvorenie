{capture name=path}{l s='Order confirmation'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Order confirmation'}</h1>


{include file="$tpl_dir./errors.tpl"}

<p class="success">Ваш заказ принят!</p>
<p>В ближайшее время наш менеджер свяжется с Вами по указанному телефону.</p>
<br /><br />
<p>Вступайте в нашу группу и следите за новинками и скидками!</p>
{literal}
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 2, width: "520", height: "400"}, 35989702);
</script>
{/literal}

<script type="text/javascript">
{literal}var yaParams = {{/literal}
  order_id: "{$order->id}",
  order_price: {$order->total_paid}, 
  currency: "RUR",
  exchange_rate: 1,
  goods: 
     [
	 {foreach from=$order->products item=product name=orderlist}
       {literal} {{/literal}
          id: "{$product.product_id}", 
          name: "{$product.product_name}", 
          price: {$product.product_price},
          quantity: {$product.product_quantity}
        {literal} } {/literal}{if !$smarty.foreach.orderlist.last},{/if}
	{/foreach}
      ]
{literal}};{/literal}
</script>

{$HOOK_PAYMENT_RETURN}