
<div id="homecontent-{$hook}" class="col-md-9">
	{foreach from=$leomanagewidgets_datas item=datas}
		{if $datas.task == 'custom'}
			<div class="leo-{$datas.task} clearfix">								
				{if $datas.configs.display_title}<div class="box-line h3"><div>{$datas.title}</div></div>{/if}
				{$datas.contents}
				
			</div>
		{elseif $datas.task == 'carousel'}
			<div class="leo-{$datas.task} clearfix">
				{foreach from=$datas.contents item=content}
					<div id="blockleohighlightcarousel-{$content.id}" class="block products_block exclusive blockleohighlightcarousel">
					<div class="block_content">	
						<div class="highlight-carousel">
							<div class="highlight-image">
								{if $datas.configs.display_title}
									<h3>{$datas.title}</h3>
								{/if}
							</div>
							<div class="row">
								<div class="col-xs-12">
									{if !empty($content.products )}
										{$mproducts=$content.products}{$tabname = $content.id}
										{include file="{$carousel_product_tpl}"}
									{/if}
								</div>
							</div>
						</div>
					</div>
					{if $datas.description}<div class="highlight-info">{$datas.description}</div>{/if}
					</div>
				{/foreach}
			</div>
		{elseif $datas.task == 'tab'}
			<div class="leo-{$datas.task} block products_block exclusive blockleoproducttabs clearfix">
				{if $datas.configs.display_title}
					<h3>{$datas.title}</h3>
				{/if}
				<div class="block_content">	
					<ul id="productTabs-{$datas.id_leomanagewidgets}" class="nav nav-tabs">
					{foreach from=$datas.contents item=content}
						{if $content.products}
							<li><a href="#leotab-{$datas.id_leomanagewidgets}-{$content.id}" data-toggle="tab">{if $content.image}<img src="{$content.image}" alt=""/>{/if}{$content.title}</a></li>
						{/if}
					{/foreach}
					</ul>
				</div>
				<div id="productTabsContent-{$datas.id_leomanagewidgets}" class="tab-content">
					{foreach from=$datas.contents item=content}
						{if $content.products}
							<div class="tab-pane" id="leotab-{$datas.id_leomanagewidgets}-{$content.id}">
								{$mproducts=$content.products}{$tabname = $content.id}
								{include file="{$tab_product_tpl}"}
							</div>
						{/if}
					{/foreach}
				</div>
			</div>
		{/if}
	{/foreach}
</div>
<script>
$(document).ready(function() {
    $('#homecontent-{$hook}').each(function(){
        $(this).carousel({
            pause: true,
            interval: false
        });
    });
	$(".blockleoproducttabs").each( function(){
		$(".nav-tabs li", this).first().addClass("active");
		$(".tab-content .tab-pane", this).first().addClass("active");
	} );
});
</script>