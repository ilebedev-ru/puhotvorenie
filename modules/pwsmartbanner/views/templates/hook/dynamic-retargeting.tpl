{if $page_name == 'product'}
    <script>
        window.dataLayer = window.dataLayer || [];
        {literal}
        window.dataLayer.push({
            "ecommerce":{
                "detail":{
                    "products": [
                        {
                            "id": "{/literal}{$productRetarget->id}{literal}",
                            "name": "{/literal}{$productRetarget->name|escape:'html'}{literal}",
                            "price": "{/literal}{$productRetarget->price}{literal}",
                            "brand": "{/literal}{$productRetarget->manufacturer_name|escape:'html'}{literal}",
                            "category": "{/literal}{$productRetarget->category_name|escape:'html'}{literal}"
                        }
                    ]
                }
            }
        });
        {/literal}
    </script>
{/if}