<form action="{$action}" id="configuration_form" method="post" enctype="multipart/form-data" class="form-horizontal">

    <div class="panel " id="configuration_fieldset_general">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Настройка' mod='pwoneclick'}
        </div>

        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Type of submit' mod='pwoneclick'}</label>
                <div class="col-lg-9">
                    <div class="radio">
                        <label><input type="radio" name="option_of_submit" value="1"{if ($config.option_of_submit == 1)}checked{/if}>{l s="AJAX" mod="pwoneclick"}</label>
                    </div>
                    <div class="radio">
                      <label><input type="radio" name="option_of_submit" value="2" {if ($config.option_of_submit == 2)}checked{/if}>{l s="Redirect" mod="pwoneclick"}</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Text after the order' mod='pwoneclick'}</label>
                <div class="col-lg-9">
                    <textarea class="textarea-autosize" rows="5" cols="40" name="success_message">{$config.text}</textarea>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button class="btn btn-default pull-right" name="submitpwoneclick" type="submit">
                <i class="process-icon-save"></i>
                {l s='Сохранить' mod='pwoneclick'}
            </button>
        </div>
    </div>
</form>