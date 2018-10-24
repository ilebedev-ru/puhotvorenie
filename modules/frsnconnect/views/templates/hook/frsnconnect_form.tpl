{*
* Social Network connect modules
* frsnconnect 0.16 by froZZen
*}
{if $not_services}
    <div class="auth-social">
        <div class="form_content clearfix auth-social-btns">
            {if $auth}<p>{l s='You can register or login to the site using your account for some services' mod='frsnconnect'}.</p>{/if}
            <div id="frsnconnect" class="submit">
                {foreach from=$not_services item=v key=k}
                    {if $v['sn_service_name'] == 'vk'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
                        <label for="SubmitCreate_{$k}" class="auth-vk a_auth"><i class="fa fa-vk"></i><span>Вконтакте</span></label>
                    {elseif $v['sn_service_name'] == 'fb'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
						<label class="auth-fb a_auth" for="SubmitCreate_{$k}"><i class="fa fa-facebook" aria-hidden="true"></i><span>Facebook</span></label>
                    {elseif $v['sn_service_name'] == 'tw'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
						<label class="auth-tw a_auth" for="SubmitCreate_{$k}"><i class="fa fa-twitter"></i><span>Twitter</span></label>
                    {elseif $v['sn_service_name'] == 'gl'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
						<label class="auth-gl a_auth" for="SubmitCreate_{$k}"><i class="fa fa-google"></i><span>Google+</span></label>
                    {elseif $v['sn_service_name'] == 'ok'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
						<label class="auth-ok a_auth" for="SubmitCreate_{$k}"><i class="fa fa-odnoklassniki"></i><span>Одноклассники</span></label>
                    {elseif $v['sn_service_name'] == 'ld'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
						<label class="auth-ld a_auth" for="SubmitCreate_{$k}"><i class="fa fa-linkedin"></i><span>LinkedIn</span></label>
                    {elseif $v['sn_service_name'] == 'mr'}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden"
                               value="" title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
						<label class="auth-ld a_auth" for="SubmitCreate_{$k}"><i class="fa fa-at"></i><span>Мой Мир</span></label>
                    {else}
                        <input type="submit" id="SubmitCreate_{$k}" name="snLogin_{$k}" class="submit_{$k} hidden" value=""
                               title="{$v['sn_service_name_full']}" data-auth_soc="{$v['sn_service_name']}"/>
                        <label class="auth-{$v['sn_service_name']} a_auth" for="SubmitCreate_{$k}"><i class="fa fa"></i><span>{$v['sn_service_name_full']}</span></label>
                    {/if}
                {/foreach}
				<input type="hidden" name="process" value="accAuth" class="hidden" style="display:none;" />
            </div>
        </div>
    </div>
{/if}