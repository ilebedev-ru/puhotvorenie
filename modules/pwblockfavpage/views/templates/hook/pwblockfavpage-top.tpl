{if isset($links) AND $links}
    <div id="tpanel" class="tpanel">
        <div class="centering">
            <div class="centeringin clearfix">
                <div class="tpanel_menu clearfix mobile_hide">
                    {foreach from=$links item=link}
                      <a href="{$link.url}">{$link.name}</a>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
{/if}