{if $errors}
    {foreach from=$errors item=error}
       <div class="alert error">{$error}</div>
    {/foreach}
{/if}
<fieldset><legend><img title="" alt="" src="/modules/{$module_name}/logo.gif">Настройки модуля</legend>
    <form method="POST" action="{$uri_start}">
        <label>Работает ли модуль</label>
        <div class="margin-form">
            <input type="radio" {if $turn_on} checked="checked"{/if} value="1" id="turn_on" name="turn">
            <label for="turn_on" class="t"> <img title="Включено" alt="Включено" src="../img/admin/enabled.gif"></label>
            <input type="radio" {if !$turn_on} checked="checked"{/if} value="0" id="turn_off" name="turn">
            <label for="turn_off" class="t"> <img title="Выключено" alt="Выключено" src="../img/admin/disabled.gif"></label>
            <p class="clear">Включить показ условий доставки или нет</p>
        </div>
        <label>Выводить сообщение в окне</label>
        <div class="margin-form">
            <textarea class="rte" name="default_message" cols="70" rows="10">{$default_message}</textarea>
        </div>
        <div class="clear"></div>
        <center><input name="submitConfig" value="Сохранить" class="button" type="submit"></center>
    </form>
</fieldset>
