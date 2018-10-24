<form method="POST" action="{$action}" id="{$selector}">
    <div class="uipw-form_layout">
        <h4>{$caption}</h4>
        <h5>{$text}</h5>
        <div class="uipw-modal_form_fields">
            <div>
                <label for="question_phone">{$fields.phone.label}<sup>*</sup></label>
                <input id="question_phone" value="{if isset($customer.phone)}{$customer.phone}{/if}" type="tel" tabindex="1" name="{$fields.phone.field}"/>
                <div class="{$fields.phone.field}"></div>
            </div>
            <!--<div>
                <label for="questionemail">{$fields.email.label}<sup>*</sup></label>
                <input id="questionemail" value="{if isset($customer.email)}{$customer.email}{/if}" type="email" tabindex="2" name="{$fields.email.field}"/>
                <div class="{$fields.email.field}"></div>
            </div>-->
            <label for="question_message">{$fields.message.label}<sup>*</sup></label>
            <textarea id="question_message" tabindex="3" name="{$fields.message.field}"></textarea>
            <div class="{$fields.message.field}"></div>
            <input class="button exclusive" type="submit" value="{l s="Отправить"} &rarr;" tabindex="4">
        </div>
    </div>
    <section class="uipw-form_success alert alert-success"></section>
</form>

<script>
    var pw_selector = '{$selector}';
    {if $formjs}
    // <![CDATA[
    function PWBackFormJs(){
        {$formjs}
        return;
    }
    //]]>
    {/if}
</script>