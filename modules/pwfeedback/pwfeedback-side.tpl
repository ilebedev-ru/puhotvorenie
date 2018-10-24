<h2>Отзывы</h2>
<div class="comments nuclear side">
	{if count($feedbacks)}
		{foreach from=$feedbacks item=fdb name=fdbi}
		<div class="item{if $smarty.foreach.fdbi.iteration%2} item-rht{/if}">
			<div class="com-in">
				<div class="com-in-in">
					<span class="name">{$fdb.name}, {$fdb.date|date_format:"%d/%m/%Y"}</span>
					<p>{$fdb.feedback}</p>
				</div>
			</div>
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
	{/if}
	<br style="clear:both"><div class="links">
	<a class="add" href="{$link->getPageLink('signfeedback.php')}">Опубликовать отзыв</a> <a class="all" href="{$link->getPageLink('viewfeedback.php')}">Посмотреть все отзывы</a> 
	</div>
</div>
	