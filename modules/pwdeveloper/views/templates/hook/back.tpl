<div class="panel" id="fieldset_0">
    <div class="panel-heading"><i class="icon-cogs"></i> Настройки</div>
    <div class="form-wrapper">
        <form method="POST">
            {*
            <div class="form-group">
                <input id="submitUnistallModule" type="submit" class="hidden" name="submitUnistallModule" value="Очистить"/>
                <label for="submitUnistallModule" class="btn btn-default"><i class="icon-trash"></i> Удалить ненужные модули</label>
            </div>
*}
            {if (!$pwDeveloperOn && empty($smarty.post.submitOnDeveloper)) || isset($smarty.post.submitOffDeveloper)}
                <div class="form-group">
                    <input id="submitOnDeveloper" type="submit" class="hidden" name="submitOnDeveloper" value="Очистить"/>
                    <label for="submitOnDeveloper" class="btn btn-success"><i class="icon-plus"></i> Включить инструменты</label>
                </div>
            {else}
                <div class="form-group">
                    <input id="submitOffDeveloper" type="submit" class="hidden" name="submitOffDeveloper" value="Очистить"/>
                    <label for="submitOffDeveloper" class="btn btn-warning"><i class="icon-minus"></i> Выключить инструменты</label>
                </div>
            {/if}
            {if isset($modules)}
                <div class="form-group">
                    <input id="submitModuleLogOut" type="submit" class="hidden" name="submitModuleLogOut" value="Выйти из модулей<"/>
                    <label for="submitModuleLogOut" class="btn btn-warning"><i class="icon-minus"></i> Выйти из модулей</label>
                </div>
            {/if}
        </form>
    </div>
</div>
<div class="panel" id="fieldset_0">
    <div class="panel-heading"><i class="icon-cogs"></i>Ссылки на компоненты</div>
    <div class="form-wrapper">
        {foreach from=$controllers item=controller_name key=cntrll}
            <a href="{$uri}{$cntrll}" target="_blank">{$controller_name}</a> <br />
        {/foreach}
    </div>
</div>
{if isset($modules)}
<form id="module_form" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" novalidate="">
    <input type="hidden" name="submitPassword" value="1">
    <div class="panel" id="fieldset_0">
        <div class="panel-heading">
            <i class="icon-cogs"></i> Модули
        </div>
        <div class="form-wrapper">
            {foreach from=$modules item=module key=module_name}
                <div class="form-group"><label data-toggle="tooltip" data-html="true" data-original-title="{$module->fulldesc}" class="control-label col-lg-3 label-tooltip">{$module_name} <sub {if $module.old == true}style="color:red;font-weight:bold;"{/if}>({$module->version})</sub></label>
                    <div class="col-lg-9">
                        <div class="checkbox">
                            <label><input type="checkbox" name="modules[{$module_name}]" value="{$module_name}"
                                        {if isset($module.installed)} disabled{/if}
                                        {if isset($smarty.post.modules[$module_name])} checked="checked"{/if}
                                >{if isset($module.installed)}<span style="color:red;font-weight:bold;">Уже установлен</span> {/if} {$module->desc}
                            </label>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="module_form_submit_btn" name="submitModuleInstall" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> Установить
            </button>
        </div>
    </div>
</form>
{else}
    <form id="module_form" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" novalidate="">
        <input type="hidden" name="submitPassword" value="1">
        <div class="panel" id="fieldset_0">
            <div class="panel-heading">
                <i class="icon-cogs"></i> Пароль для секретного входа
            </div>
            <div class="form-wrapper">
                <div class="form-group">
                    <label class="control-label col-lg-3">Пароль</label>
                    <div class="col-lg-9">
                        <input type="text" name="modulePassword" id="PASSWORD" value="" class="">
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" value="1" id="module_form_submit_btn" name="submitPassword" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> Сохранить
                </button>
            </div>
        </div>
    </form>
{/if}


</form>