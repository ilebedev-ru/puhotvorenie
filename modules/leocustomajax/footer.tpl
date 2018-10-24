<script type="text/javascript">
	var leoOption = {
		productNumber:{if $leo_customajax_pn}{$leo_customajax_pn}{else}0{/if},
		productRating:{if $leo_customajax_rt}{$leo_customajax_rt}{else}0{/if},
		productInfo:{if $leo_customajax_img}{$leo_customajax_img}{else}0{/if},
		productTran:{if $leo_customajax_tran}{$leo_customajax_tran}{else}0{/if},
		productQV:{if $leo_customajax_qv}{$leo_customajax_qv}{else}0{/if}
	}
    $(document).ready(function(){	
		var leoCustomAjax = new $.LeoCustomAjax();
        leoCustomAjax.processAjax();
    });
</script>
