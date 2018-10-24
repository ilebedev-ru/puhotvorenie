{if $showbutton}
<div id="pwbackcall" class="pw-phone pw-green pw-show pw-static draggable backcall-button">
    <div class="pw-ph-circle"></div>
    <div class="pw-ph-circle-fill"></div>
    <div class="pw-ph-img-circle"></div>
</div>
{/if}
<script>
// <![CDATA[
var backcallMessage = '{$backcallMessage}';
var backquestMessage = '{$backquestMessage}';
{if $backcalljs}
function PwBackCallJs(){
    {$backcalljs}
    return;
}
{/if}
//]]>
</script>

<form method="POST" action="{$pwbackcall.link}" id="uipw-form_call_modal">
    <h4>{$pwbackcall.caption}</h4>
    <div class="error"></div>
    <div class="success"></div>
    <div class="uipw-modal_form_fields">
        <input type="hidden" name="action" value="call"/>
        {if $pwbackcall.extended}
        <div>
            <label for="call_name">Имя<sup>*</sup></label>
            <input name="name" id="call_name" value="" type="text" tabindex="1">
        </div>
        {/if}
        <div>
            <label for="call_phone">Телефон<sup>*</sup></label>
            <input name="phone" id="call_phone" value="" type="tel" tabindex="2">
        </div>
        {if $pwbackcall.extended}
        <!--<div>
            <label for="call_email">E-mail<sup>*</sup></label>
            <input name="email" id="call_email" value="" type="email" tabindex="3">
        </div>-->
        {/if}

        <input class="button exclusive" type="submit" value="Отправить &rarr;" tabindex="4">
    </div>
</form>
