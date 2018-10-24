{capture name=path}Отзывы{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

{if count($feedbacks)}
<script type="text/javascript">
$(document).ready(function(){
	$('.comments .photo').fancybox();
})</script>
<div class="comments">
  <div class="headline nuclear">
	<h2>Отзывы</h2>
	<a class="add more" href="{$link->getPageLink('signfeedback.php')}">Опубликовать отзыв</a>
  </div>
  <div class="cnt-row nuclear">
  {foreach from=$feedbacks item=fdb name=fdbi}
	<div class="item{if $smarty.foreach.fdbi.iteration%2==0} item-rht{/if}">
	  <div class="text">
		  <p>{$fdb.feedback|truncate:300:'...'}</p>
		  {if $fdb.photo}
			<a class="photo" href="{$fdb.photo}" class="fancybox"><img src="{$fdb.photo_small}"></a>
		  {/if}
		</div>
	  <span class="name">{$fdb.name}{if $fdb.youtitle}, {$fdb.youtitle}{/if}</span>
	  {if $fdb.answer}
		<div class="answer">
			<div class="answer-in">
				<p>{$fdb.answer}</p>
				<span class="autor">Команда {$shop_name}</span>
			</div>
		</div>
	  {/if}
	</div>
	{/foreach}
  </div>
</div>
{/if}
