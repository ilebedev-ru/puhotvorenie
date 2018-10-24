<a class="btn btn-default uipw-form_goods_modal" {if $page_name=='product'}id="pwoneclick"{/if} href="#uipw-form_goods_modal"
   data-pwoneclick-old-price="{$product.old_price}"
   data-pwoneclick-image="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')}"
   data-pwoneclick-name="{$product.name}"
   data-pwoneclick-id="{$product.id}"
   data-pwoneclick-price="{$product.price}">{l s='Купить в 1 клик' mod='pwoneclick'}</a>