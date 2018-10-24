<!-- MODULE EXPRESSORDER -->
{if isset($html)}
{literal}
<script type="text/javascript">
  $('document').ready( function() {
    var form = $('#login_form'); 
    if (!form)
      return;
    var newForm = document.createElement('form');
    var headId = document.getElementsByTagName('head')[0];
    var cssNode = document.createElement('link');
{/literal}
    cssNode.type = 'text/css';
    cssNode.rel = 'stylesheet';
    cssNode.href = '{$base_dir_ssl}modules/expressorder/css/expressorder.css';
    headId.appendChild(cssNode);
    newForm.setAttribute('action', '{$base_dir_ssl}modules/expressorder/makeorder.php');
    newForm.setAttribute('method', 'post');
    newForm.setAttribute('id', 'expressorder');
    newForm.setAttribute('class', 'std');
    newForm.innerHTML = '{$html}';
{literal}
    $(newForm).insertBefore('#create-account_form');
  });
</script>
{/literal}
{else}
  <fieldset>
    <h3>{l s='Express Checkout' mod='expressorder'}</h3>
    <h4>{l s='Checkout without registering' mod='expressorder'}.</h4>
    <br/>
    <div style="message_info">{l s='If you want to monitor your orders please don\'t use this registration' mod='expressorder'}</div>
    <p>
      
      {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
      <input type="submit" id="SubmitExpressCheckout" name="SubmitExpressCheckout" class="button_large" value="Оформить быстро" />
  </p>
  </fieldset>
  <br style="clear:left">
{/if}
<!-- /MODULE EXPRESSORDER -->
