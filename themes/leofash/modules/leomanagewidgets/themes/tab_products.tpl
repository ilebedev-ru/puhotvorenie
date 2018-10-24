{if !empty($mproducts)}
{$itemsperpage=$datas.configs.itemspage}
{$columnspage=$datas.configs.columns}
<div class=" carousel slide" id="leotab-{$datas.id_leomanagewidgets}-{$tabname}carousel">
	 {if count($mproducts) > 1}	
	<a class="carousel-control left" href="#leotab-{$datas.id_leomanagewidgets}-{$tabname}carousel" data-slide="prev"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>
	<a class="carousel-control right" href="#leotab-{$datas.id_leomanagewidgets}-{$tabname}carousel" data-slide="next"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
	{/if}
	<div class="carousel-inner">
	{foreach from=$mproducts item=products name=mypLoop}
		<div class="item {if $smarty.foreach.mypLoop.first}active{/if}">
            {include file="$tpl_dir./product-list.tpl" products=$products}
		</div>		
	{/foreach}
	</div>
</div>
{/if}