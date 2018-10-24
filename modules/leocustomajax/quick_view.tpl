<script type="text/javascript">
    $(document).ready(function(){ldelim}
        $("body").addClass("quickview-wrapper");
        $("a, form").attr('target', '_parent');
        $("#short_description_block .buttons_bottom_block").remove();
        $("#usefull_link_block").remove();
        if($("#relatedproducts").length) $("#relatedproducts").remove();
        
        {if $on_ajaxcart}$('p#add_to_cart input').unbind('click').click(function(e){
                e.stopImmediatePropagation();
                return parent.quickViewAddToCart( $('#product_page_product_id').val(), $('#idCombination').val(), $('#quantity_wanted').val());
        });
        {else}
        $('p#add_to_cart input').unbind('click').click(function(e){
                e.stopImmediatePropagation();
                return true;
        });
        {/if}
    });
</script>