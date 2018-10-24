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
                            "name": "{/literal}{$productRetarget->name}{literal}",
                            "price": "{/literal}{$productRetarget->price}{literal}",
                            "brand": "{/literal}{$productRetarget->manufacturer_name}{literal}",
                            "category": "{/literal}{$productRetarget->category_name}{literal}"
                        }
                    ]
                }
            }
        });
        {/literal}
    </script>
{/if}