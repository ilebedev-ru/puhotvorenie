{if $page_name == 'index'}
{literal}
<script>
 $(document).ready(function(){
  $('.bxslider').bxSlider({
  		mode: 'fade'
  });
});	
</script> 
{/literal}
<div class="slider">
  	<ul class="bxslider">
		{foreach from=$xml item=home_link name=links}
		<li>
		<a href='{$home_link->url}'>
            <img src="{$link->getPageLink('index.php')}modules/pwslider/{$home_link->img}" alt="{$home_link->field1}" />
        </a>
        </li>
        {/foreach}
	</ul>							
</div> 
{/if}