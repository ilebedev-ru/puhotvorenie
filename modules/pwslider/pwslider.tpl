{if $page_name == "index"}
{literal}
<script>
$( document ).ready(function() {
$('.slider').fractionSlider({
        'controls': 			false,
		'timeout' : 5000,
		'pauseOnHover' : true,
        'pager': 				true
    });

});
	</script> 
{/literal}
<div class="slider-wrp pwslider">
    <div class="slider">
        {foreach from=$xml item=home_link name=links}
        <div class="slide slide{if $smarty.foreach.links.iteration == 1} backg1{elseif $smarty.foreach.links.iteration == 2} backg2{else} backg3{/if}">
            {if $home_link->vip}<img src="{$link->getPageLink('index.php')}modules/pwslider/vip.png" data-in="top" alt="Vip продукция" class="vip" data-position="140,0">{/if}
            <a href="{$home_link->url}"><img alt="{$home_link->field1}"  src="{$link->getPageLink('index.php')}modules/pwslider/{$home_link->img}" data-delay="200" data-in="left" data-position="55,50" /></a>
            <div class="hd" data-position="30,400">{$home_link->field1}</div>
            {if $home_link->field2}<p data-time="2000" class="txt" data-ease-in="easeOutBounce" data-position="110,400">{$home_link->field2}</p>{/if}
            <div class="prise" data-delay="500" data-position="200,400">
                {if $home_link->field3}<span class="last">{$home_link->field3}</span>{/if}
                {if $home_link->field4}<span class="new">{$home_link->field4}</span>{/if}
            </div>
            <a href="{$home_link->url}" data-position="180,600" class="more butt">Купить</a>
        </div>
        {/foreach}
    </div>
</div>
{/if}