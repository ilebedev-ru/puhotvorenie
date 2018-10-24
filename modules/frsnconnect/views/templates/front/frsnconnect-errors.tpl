{*
* Social Network connect modules
* frsnconnect 0.16 by froZZen
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">
    {l s='My account' mod='frscconnect'}
</a>
<span class="navigation-pipe">{$navigationPipe}</span>{l s='My Socials' mod='frsnconnect'}
{/capture}
{*include file="$tpl_dir./breadcrumb.tpl"*}
<div>
    <h1 class="page-heading">{l s='My Socials' mod='frsnconnect'}</h1>
    {include file="$tpl_dir./errors.tpl"}

<ul class="footer_links clearfix">
    <li>
	<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span><i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='frsnconnect'}</span>
	</a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}">
            <span><i class="icon-chevron-left"></i> {l s='Home'}</span>
	</a>
    </li>
</ul>
        
</div>