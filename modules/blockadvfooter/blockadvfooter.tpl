<div id="advand{$pos}" class="adv-footer-wrap">
	{foreach from=$lofpositions item=blocks key=key_pos}
		<div id="lofadva-pos-{$key_pos + 1}" class="blocks" style="width:100%">
			<div class="blocks-wrap">
			{foreach from=$blocks item=block key=key_block}
				<div class="block" style="width:{$block.width}%; float:left;">
					<div class="block-wrap">
						{if $block.show_title}
							<p class="title_block">{$block.title}</p>
						{/if}
						<div class="block_content">
							<ul class="widget-items">
							{foreach from=$block.items item=item key=key_item}
								{if $item.type == 'link'}
									<li class="link"><a href="{$item.link_item}" title="{$item.title}" target="{$item.target}" {if $item.target == '_newwithout'}onclick='javascript: window.open("{$item.link_item}", "", "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes"); return false;'{/if}>{if $item.show_title}{$item.title}{/if}</a></li>
								{elseif $item.type == 'module'}
									<li class="widget-module">
										{if $item.show_title}<h2>{$item.title}</h2>{/if}
										{$item.module}
									</li>
								{elseif $item.type == 'custom_html'}
									<li class="widget-text">
										{if $item.show_title}<h2>{$item.title}</h2>{/if}
										{$item.text}
									</li>
								{elseif $item.type == 'gmap'}
									<li class="widget-gmap">
										{if $item.show_title}<h2>{$item.title}</h2>{/if}
										{literal}
										<script type="text/javascript">
										  function initialize() {
											  var myOptions = {
												zoom: 8,
												center: new google.maps.LatLng({/literal}{$item.latitude}, {$item.longitude}{literal}),
												mapTypeId: google.maps.MapTypeId.ROADMAP
											  }
											  var map = new google.maps.Map(document.getElementById("lofmap_canvas{/literal}{$item.id_loffc_block_item}{literal}"), myOptions);
											}
											function loadScript() {
											  var script = document.createElement("script");
											  script.type = "text/javascript";
											  script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyCgnzQHpG4TAVJJAFgEp6u1arCQ3dlwVB8&sensor=true&callback=initialize";
											  document.body.appendChild(script);
											}
											window.onload = loadScript;
										</script>
										{/literal}
										<div id="lofmap_canvas{$item.id_loffc_block_item}" class="lofmap_canvas"></div>
									</li>
								{elseif $item.type == 'addthis'}
									<li class="widget-addthis">
										{if $item.show_title}<h2>{$item.title}</h2>{/if}
										<div class="addthis_toolbox addthis_default_style ">
											<a class="addthis_button_preferred_1"></a>
											<a class="addthis_button_preferred_2"></a>
											<a class="addthis_button_preferred_3"></a>
											<a class="addthis_button_preferred_4"></a>
											<a class="addthis_button_compact"></a>
											<a class="addthis_counter addthis_bubble_style"></a>
										</div>
										{literal}
										<script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
										<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4fc893c046e9bd1b"></script>
										{/literal}
									</li>
								{/if}
							{/foreach}
							</ul>
						</div>	
					</div>
				</div>
			{/foreach}
			</div>
		</div>
	{/foreach}
</div>