{capture name=path}Оставить отзыв{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h2>Оставить отзыв о нашем магазине</h2>
{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation)}
<p class="success">
	Спасибо вам за отзыв, после модерации он появиться на сайте!
</p>
{else}


<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std" id="feedback-single" enctype="multipart/form-data">
	<fieldset> 
		<p class="text">
		<label for="name">Ваше имя:</label>
		<input type="text" size="50" name="name" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/> 
		</p>
		<p class="text">
		<label for="email">E-mail:</label>
		<input type="text" size="50" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
		</p>
		<p class="text">
		<label for="youtitle">Ваш город:</label>
		<input type="text" size="50" id="youtitle" name="youtitle" value="{if isset($smarty.post.youtitle)}{$smarty.post.youtitle|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
		</p>
		<p class="textarea">
		<label for="feedback">Ваш отзыв:</label>
		<textarea id="feedback" name="feedback" rows="7" cols="40">{if isset($smarty.post.feedback)}{$smarty.post.feedback|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
		</p>
		<input type="hidden" name="process" value="1" />
	</fieldset>
	<p class="submit">
		<input type="submit" class="button_large" name="SubmitFeedback" value="Отправить отзыв" />
	</p>
	
</form>
{/if}
