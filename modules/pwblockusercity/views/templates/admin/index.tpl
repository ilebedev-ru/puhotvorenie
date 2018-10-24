{if !empty($errors)}
    {foreach from=$errors item=error}
       <div class="alert error">{$error}</div>
    {/foreach}
{/if}
{if !empty($rules)}
    <fieldset><legend><img title="" alt="" src="/modules/{$module_name}/logo.gif">Настройки правил для городов</legend>
        <table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
            <thead>
                <th>ID</th>
                <th>Город</th>
                <th>Правило</th>
                <th></th>
            </thead>
            <tbody>
                {foreach from=$rules item=rule}
                    <tr>
                        <td>{$rule.id_city_rule}</td>
                        <td>{$rule.city}</td>
                        <td>{$rule.description|strip_tags|truncate:100:'...'}</td>
                        <td>
                            <a href="{$uri_start}&editRule={$rule.id_city_rule}"><img title="Редактировать" alt="" src="../img/admin/edit.gif"></a>
                            <a href="{$uri_start}&deleteRule={$rule.id_city_rule}"><img title="Удалить" alt="" src="../img/admin/delete.gif"></a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </fieldset> <br/>
    {else}
    <p class="warn">Правила для городов еще не созданы.</p>
{/if}
    <fieldset> <legend><img title="" alt="" src="/modules/{$module_name}/logo.gif">{if isset($smarty.post.id_city_rule)}Редактировать{else}Добавить{/if} правило для города</legend>
        <form method="POST" action="{$uri_start}">
            <label>Город</label>
            <div class="margin-form">
                <input name="city" value="{if !empty($smarty.post.city)}{$smarty.post.city}{/if}" type="text">
                <p class="clear">Укажите с большой буквы без ошибок</p>
            </div>
            <label>Правило</label>
            <div class="margin-form">
                <textarea class="rte" name="description" cols="70" rows="10">{if isset($smarty.post.description)}{$smarty.post.description}{/if}</textarea>
            </div>
            <div class="clear"></div>
            {if !empty($smarty.post.id_city_rule)}<input type="hidden" name="id_city_rule" value="{$smarty.post.id_city_rule}">{/if}
            <center><input name="submitEditRule" value="Сохранить" class="button" type="submit"></center>
        </form>
    </fieldset>
    <br/>
    <fieldset><legend><img title="" alt="" src="/modules/{$module_name}/logo.gif">Город по умолчанию и правило по умолчанию</legend>
        <form method="POST" action="{$uri_start}">
            <label>Включить условия доставки</label>
            <div class="margin-form">
                <input type="radio" {if $turn_on} checked="checked"{/if} value="1" id="turn_on" name="turn">
                <label for="turn_on" class="t"> <img title="Включено" alt="Включено" src="../img/admin/enabled.gif"></label>
                <input type="radio" {if !$turn_on} checked="checked"{/if} value="0" id="turn_off" name="turn">
                <label for="turn_off" class="t"> <img title="Выключено" alt="Выключено" src="../img/admin/disabled.gif"></label>
                <p class="clear">Включить показ условий доставки или нет</p>
            </div>
            <label>Город по умолчанию</label>
            <div class="margin-form">
                <input name="default_city" value="{$default_city}" type="text">
                <p class="clear">Укажите с большой буквы без ошибок</p>
            </div>

            <label>Правило по умолчанию</label>
            <div class="margin-form">
                <textarea class="rte" name="default_rule" cols="70" rows="10">{$default_rule}</textarea>
            </div>
            <div class="clear"></div>
            <center><input name="submitConfig" value="Сохранить" class="button" type="submit"></center>
        </form>
    </fieldset>
