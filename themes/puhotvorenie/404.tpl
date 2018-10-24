
<h1>{l s='Page not available'}</h1>

<p class="error">
	<img src="{$img_dir}icon/error.gif" alt="{l s='Error'}" class="middle" />
	{l s='We\'re sorry, but the Web address you entered is no longer available'}
</p>

<h3>{l s='To find a product, please type its name in the field below'}</h3>

<form action="{$link->getPageLink('search.php')}" method="get" class="std">
	<fieldset>
		<p>
			<label for="search">{l s='Search our product catalog:'}</label>
			<input id="search_query" class="page404_input" name="search_query" type="text" />
			<input type="submit" name="Submit" value="{l s='Search'}" class="page404_input button_small" />
		</p>
	</fieldset>
	<div class="clear"></div>
</form>

<p><a href="{$base_dir}" title="{l s='Home'}"><img src="{$img_dir}icon/home.gif" alt="{l s='Home'}" class="icon" /></a><a href="{$base_dir}" title="{l s='Home'}">{l s='Home'}</a></p>
