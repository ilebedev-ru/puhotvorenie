{*
* Social Network connect modules
* frsnconnect 0.16 by froZZen
*}
<script type="text/javascript">
    {literal}
    $('document').ready(function() {
    $('a[rel^=ajax_id_serv_]').click(function(e) {
        e.preventDefault();
        var idSrv =  $(this).attr('rel').replace('ajax_id_serv_', '');
        var parent = $(this).parent().parent();
        $.ajax({
    {/literal}
            url: "{$link->getModuleLink('frsnconnect', 'actions', ['process' => 'remove'], true)}",
    {literal}
            type: "POST",
            dataType: "json",
            async: true,
            cache: false,
            data: {'id_sn_service': idSrv, 'ajax': true},
            success: function(data) {
                if (!data.hasError) {
                    $("form#frsnconnect_form").slideUp("slow", function() {
                        $("form#frsnconnect_form").html(data.form);
                    });
                    $("form#frsnconnect_form").slideDown("slow");

                    parent.fadeOut("normal", function() {
                        parent.remove();
                    });
                }
            },
            error: function (data, status, e) {
                alert(e);
            }
        });

    });
    });
    {/literal}
</script>

{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='My account' mod='frsnconnect'}</a>
    <span class="navigation-pipe">{$navigationPipe}</span>{l s='My Socials' mod='frsnconnect'}
{/capture}
{*include file="$tpl_dir./breadcrumb.tpl"*}

{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='My Socials' mod='frsnconnect'}</h1>
<p class="info-title">{l s='You can connect your account to your accounts in social networks and use them to login to our website.' mod='frsnconnect'}</p>

    <form method="post" id="frsnconnect_form" class="box" action="{$link->getModuleLink('frsnconnect', 'actions', ['process' => 'accAdd'], true)}">
        {include file="$tpl_path"}
    </form>
    {if $connect_serv}
    <div class="row">
        {foreach from=$connect_serv  item=service  key=lkey }
            <div class="col-xs-6 col-sm-4">
	<div class="frsnconnect-myaccount box">
            <a href="#" class="serv_img_link {$lkey}_48" title="{$service.sn_service_name_full|escape:'htmlall':'UTF-8'}"></a>
            <h3 class="page-subheading">{$service.id|escape:'htmlall':'UTF-8'}</h3>

            <a title="{l s='Delete'}" href="#" rel="ajax_id_serv_{$service.id_sn_service|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small remove">
                <span>{l s='Delete'}<i class="icon-remove right"></i></span>
            </a>
	</div>
            </div>
	{/foreach}
    </div>
    {else}
	<p class="alert alert-warning">{l s='You have not yet connected any service.' mod='frsnconnect'}</p>

    {/if}
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