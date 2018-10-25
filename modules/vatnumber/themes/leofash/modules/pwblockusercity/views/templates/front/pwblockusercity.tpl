<!--pwblockusercity (overrided)-->
<div class="header-change-city">
	<div class="header-change-city-widget">
		<div>Ваш регион:</div>
		<a href="#" class="header-change-city-link" onClick="yaCounter22742683.reachGoal('cityChange');return true;">
			<span>{$city}</span>
		</a>

	</div>
	{hook h='displayDeliveryDuration'}
	{if $deliveryShow}
		<div class="delivery_condition">
			<span class="get-info" onClick="yaCounter22742683.reachGoal('cityDelivery');return true;">Условия доставки</span>
			<div class="pwpopup" id="pwpopup">
				<i class="tail"></i>
				<div class="condition-info pwpopup-content"><noindex>
					{$currentDelivery}
					</noindex>
				</div>
			</div>
		</div>
	{/if}
</div>
<div class="city_overlay" style="display:none;"></div>
<div id="city_popup" class="city_popup animated col-md-6 col-md-6 col-sm-6 col-xs-12">
	<div class="close"></div>
	<div class="content">
		<div class="titles">
			<p class="title">{l s='Выберите ваш город'}</p>
			<p class="subtitle">{l s='От вашего выбора зависит стоимость и способы доставки'}</p>
		</div>
		<div class="fields row">
			<div class="col-xs-9 col-md-6"><input type="text" value="{$city}" id="input_city"></div>
			<div class="col-xs-3 col-md-6"><input class="btn btn-default" type="button" value="{l s='Ok'}" id="submit_city"></div>
            <div class="clearfix"></div>
		</div>
		<div class="cities">
			<span>{l s='Популярные города'}</span>
			<div class="pop_cities clearfix">
					{assign var="counter" value=1}
					{foreach from=$pop_cities item=c}
						{if $counter == 1}<ul>{/if}
	    					<li>
								<span>{$c}</span>
							</li>
	    				{if $counter == 4}</ul>{assign var=counter value=0}{/if}  
	    				{assign var=counter value=$counter+1}
					{/foreach}
			</div>
			<div class="more_cities">
				<span>{l s='Еще города'}</span>
			</div>
			<div class="more_cities_box">
				<div class="pop_cities clearfix">
					{assign var="counter" value=1}
					{foreach from=$other_cities item=c}
						{if $counter == 1}<ul>{/if}
	    					<li>
								<span>{$c}</span>
							</li>
	    				{if $counter == 4}</ul>{assign var=counter value=0}{/if}  
	    				{assign var=counter value=$counter+1}
					{/foreach}
			</div>
			</div>
		</div>
	</div>
</div>
<!--/pwblockusercity-->