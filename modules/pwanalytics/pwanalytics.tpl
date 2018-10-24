<!-- pwanalytics -->
<script type="text/javascript">
    window.dataLayer = window.dataLayer || [];
    {if Configuration::get('PWANALYTICS_GOOGLE')}
        {literal}
        ga('require', 'ecommerce');
        ga('ecommerce:addTransaction', {
            'id': '{/literal}{$transaction.id}{literal}',                     // Transaction ID. Required.
            'affiliation': '{/literal}{$transaction.name|escape:'html'}{literal}',   // Affiliation or store name.
            'revenue': '{/literal}{$transaction.revenue}{literal}',               // Grand Total.
            'shipping': '{/literal}{$transaction.shipping}{literal}',                  // Shipping.
            'tax': '{/literal}{$transaction.tax}{literal}',
            'currency': '{/literal}{$transaction.currency}{literal}' // Tax.
        });
        {/literal}
        {foreach from=$order_products item=oproduct}
            {literal}
                    ga('ecommerce:addItem', {
                        'id': '{/literal}{$oproduct.id}{literal}',                     // Transaction ID. Required.
                        'name': '{/literal}{$oproduct.name|escape:'html'}{literal}',    // Product name. Required.
                        'sku': '{/literal}{$oproduct.sku|escape:'html'}{literal}',                 // SKU/code.
                        'category': '{/literal}{$oproduct.category|escape:'html'}{literal}',         // Category or variation.
                        'price': '{/literal}{$oproduct.price}{literal}',                 // Unit price.
                        'quantity': '{/literal}{$oproduct.quantity}{literal}'                   // Quantity.
                        });
                    {/literal}
        {/foreach}
        ga('ecommerce:send');
    {/if}
    {if Configuration::get('PWANALYTICS_YANDEX')}
        {literal}
        dataLayer.push({
            "ecommerce": {
                "purchase": {
                    "actionField": {
                        "id" : "{/literal}{$transaction.id}{literal}",
                        "goal_id" : "{/literal}{$transaction.goal_id}{literal}"
                    },
                    "products": [{/literal}
                        {foreach from=$order_products item=oproduct name=order_products}
                            {literal}{
                            "id": "{/literal}{$oproduct.id}{literal}",
                            "name": "{/literal}{$oproduct.name|escape:'html'}{literal}",
                            "price": "{/literal}{$oproduct.price}{literal}",
                            "quantity": "{/literal}{$oproduct.quantity}{literal}",
                            "brand": "{/literal}{$oproduct.brand|escape:'html'}{literal}",
                            "category": "{/literal}{$oproduct.category|escape:'html'}{literal}",
                            "variant": "{/literal}{$oproduct.variant|escape:'html'}{literal}"
                            }{/literal}{if !$smarty.foreach.order_products.last},{/if}
                        {/foreach}
                        {if !$smarty.foreach.orderproducts.last},{/if}{literal}
                    ]
                }
            }
        });
        {/literal}
    {/if}
</script>