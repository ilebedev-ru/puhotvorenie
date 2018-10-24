{*
* Social Network connect modules
* frsnconnect 0.16 by froZZen
*}

{if !empty($all_services)}
    <div class="auth-social-btns">
    <div class="amp-social">
        <div class="amp-social-tip">{l s='Связывайте учетную запись с соцсетью'}</div>
        <div class="amp-social-connect">
            {foreach from=$all_services item=v key=k}
                {if $v['sn_service_name'] == 'vk'}
                    {if !in_array($k, $connect_serv_names)}
                        <form method="post" id="frsnconnect_form" class="box vk_frs_form"
                              action="{$link->getModuleLink('frsnconnect', 'actions', ['process' => 'accAdd'], true)}">
                            <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden" value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}" />
                            <a class="amp-social-btn social-vk a_auth" data-auth_soc="{$v['sn_service_name']}">
                                <i class="icon icon-social-vk"></i>
                                <span>Вконтакте</span> <small>связать с соц.сетью</small>
                            </a>
                        </form>
                    {else}
                        <a href="#" class="amp-social-btn social-vk social-disconnect" rel="ajax_id_serv_{$v['id_sn_service']|escape:'htmlall':'UTF-8'}">
                            <i class="icon icon-social-vk"></i>
                            <span>Вконтакте</span><small>отменить связь</small>
                        </a>
                    {/if}
                {elseif $v['sn_service_name'] == 'fb'}
                    {if !in_array($k, $connect_serv_names)}
                        <form method="post" id="frsnconnect_form" class="box"
                              action="{$link->getModuleLink('frsnconnect', 'actions', ['process' => 'accAdd'], true)}">
                            <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden" value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}" />
                            <a class="amp-social-btn social-fb a_auth" data-auth_soc="{$v['sn_service_name']}">
                                <i class="icon icon-social-fb"></i>
                                <span>Facebook</span> <small>связать с соц.сетью</small>
                            </a>
                        </form>
                    {else}
                        <a href="#" class="amp-social-btn social-fb social-disconnect" rel="ajax_id_serv_{$v['id_sn_service']|escape:'htmlall':'UTF-8'}">
                            <i class="icon icon-social-fb"></i>
                            <span>Facebook</span><small>отменить связь</small>
                        </a>
                    {/if}
                {/if}
            {/foreach}
        </div>
    </div>
  </div>
{/if}


<script type="text/javascript">
    {literal}
    $('document').ready(function() {
        $('a[rel^=ajax_id_serv_]').click(function(e) {
            e.preventDefault();
            var idSrv =  $(this).attr('rel').replace('ajax_id_serv_', '');

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
                        location.reload();
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