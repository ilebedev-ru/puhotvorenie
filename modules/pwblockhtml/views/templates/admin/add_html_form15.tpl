{if isset($success) AND $success}
    <div class="module_confirmation conf confirm">
        {l s='Сохранено' mod='pwblockhtml'}
    </div>
{/if}
<div class="toolbar-placeholder">
	<div class="toolbarBox toolbarHead" style="width: 1553px;">
        <ul class="cc_button">
            <li>
                <a class="toolbar_btn" href="index.php?controller=AdminPWBlockHTML{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}" title="{l s='Вернуться'}">
                    <span class="process-icon-back"></span>
                    <div>{l s='Вернуться'}</div>
                </a>
            </li>
            <li>
                <label style="width: auto;" for="pwblockhtml_form_submit_btn">
                    <a class="toolbar_btn" href="#" id="pwblockhtml_form_submit" title="{l s='Сохранить'}">
                        <span class="process-icon-save "></span>
                        <div>{l s='Сохранить'}</div>
                    </a>
                </label>
            </li>
        </ul>
		<div class="pageTitle">
            <h3>
				<span id="current_obj" style="font-weight: normal;">								
                    <span class="breadcrumb item-0 ">{l s='Блок HTML'}</span>
                </span>
			</h3>
		</div>
	</div>
</div>
<script>
$('document').ready(function(){
    $('#pwblockhtml_form_submit').click(function(e){
        e.preventDefault();
        $('#pwblockhtml_form_submit_btn').click();
    });
});
</script>
<fieldset>
    <legend>
        {l s='Инструкция' mod='pwblockhtml'}
    </legend>
    {l s='Этот блок с HTML кодом будет привязан ко всем хукам, которые вы укажите в поле "Хуки".' mod='pwblockhtml'} <br>
    {l s='Редактировать расположение модуля внутри хука можно на' mod='pwblockhtml'} <a href="{$link->getAdminLink('AdminModulesPositions')}" target="_blank">{l s='этой странице.' mod='pwblockhtml'}</a><br>
    {l s='В поле "Хуки" вы можете указать несуществующий хук, он будет создан.' mod='pwblockhtml'} <br>
    {l s='Чтобы отобразить на странице созданный хук нужно добавить в файле шаблона в нужном вам месте вызов этого хука. Например:' mod='pwblockhtml'} {literal}{hook h='myNewHook'}{/literal}
</fieldset>
<fieldset>
    <legend>
        {l s='Блок HTML' mod='pwblockhtml'}
    </legend>
    <form id="pwblockhtml_form" class="form-horizontal" method="post">
        <input type="hidden" name="submitAddPWBlockHTML" value="1">
        <input type="hidden" name="id_pwblockhtml" value="{if isset($block_html.id_pwblockhtml) AND $block_html.id_pwblockhtml}{$block_html.id_pwblockhtml}{else}0{/if}">
        <div id="tinymce-data" class="hidden" data-ad="{$ad}" data-iso="{$iso}"></div>
        <div id="editors-data" class="hidden"
             data-html_editor="{if isset($block_html.html_editor) AND $block_html.html_editor}{$block_html.html_editor}{else}ace{/if}"
             data-css_editor="{if isset($block_html.css_editor) AND $block_html.css_editor}{$block_html.css_editor}{else}ace{/if}"
             data-javascript_editor="{if isset($block_html.js_editor) AND $block_html.js_editor}{$block_html.js_editor}{else}ace{/if}"
        ></div>
            <label for="name" class="required">
                {l s='Название' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <input type="text" name="name" id="name" value="{if isset($block_html.name) AND $block_html.name}{$block_html.name}{/if}">
            </div>
            <label class="required" for="hooks">
                {l s='Хуки' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <select name="hooks[]" class="hooks" id="hooks" multiple="multiple" data-current-hooks="{$current_hooks|escape:'html':'UTF-8'}">
                    {if $hooks}
                        {foreach from=$hooks item=hook}
                            <option value="{$hook.name|strtolower}">{$hook.name}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <label class="" for="html-input">
                {l s='HTML' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <div id="html"></div>
                <textarea id="html-input" name="html" {if !isset($block_html.html_editor) OR $block_html.html_editor != 'ace'}style="display: none;"{/if}>{if isset($block_html.html) AND $block_html.html}{$block_html.html|escape:'html':'UTF-8'}{/if}</textarea>
                <div class="html-input-control">
                    <label class="radio-inline">{l s='Выберите редактор' mod='pwblockhtml'}</label>
                    <label class="radio-inline"><input type="radio" name="html_editor" value="ace" data-editor_for="html" {if !isset($block_html.html_editor) OR $block_html.html_editor == 'ace'}checked{/if}>{l s='ACE' mod='pwblockhtml'}</label>
                    <label class="radio-inline"><input type="radio" name="html_editor" value="tinymce" data-editor_for="html" {if isset($block_html.html_editor) AND $block_html.html_editor == 'tinymce'}checked{/if}>{l s='TinyMCE' mod='pwblockhtml'}</label>
                    <label class="radio-inline"><input type="radio" name="html_editor" value="plain" data-editor_for="html" {if isset($block_html.html_editor) AND $block_html.html_editor == 'plain'}checked{/if}>{l s='Без редактора' mod='pwblockhtml'}</label>
                </div>
            </div>
            <label class="">
                {l s='Нужен CSS' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="need_css" id="need_css_on" value="1" {if isset($block_html.need_css) AND $block_html.need_css}checked{/if} data-form_group="css-form-group">
                    <label for="need_css_on" class="radioCheck">{l s='Да' mod='pwblockhtml'}</label>
                    <input type="radio" name="need_css" id="need_css_off" value="0" {if !isset($block_html.need_css) OR !$block_html.need_css}checked{/if} data-form_group="css-form-group">
                    <label for="need_css_off" class="radioCheck">{l s='Нет' mod='pwblockhtml'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
            <div class="" id="css-form-group" {if !isset($block_html.need_css) OR !$block_html.need_css}style="display: none;"{/if}>
                <label class="" for="css-input">
                    {l s='CSS' mod='pwblockhtml'}
                </label>
                <div class="margin-form">
                    <div id="css"></div>
                    <textarea id="css-input" name="css" {if !isset($block_html.css_editor) OR $block_html.css_editor != 'ace'}style="display: none;"{/if}>{if isset($block_html.css) AND $block_html.css}{$block_html.css|escape:'html':'UTF-8'}{/if}</textarea>
                    <div class="html-input-control">
                        <label class="radio-inline">{l s='Выберите редактор' mod='pwblockhtml'}</label>
                        <label class="radio-inline"><input type="radio" name="css_editor" value="ace" data-editor_for="css" {if !isset($block_html.css_editor) OR $block_html.css_editor == 'ace'}checked{/if}>{l s='ACE' mod='pwblockhtml'}</label>
                        <label class="radio-inline"><input type="radio" name="css_editor" value="plain" data-editor_for="css" {if isset($block_html.css_editor) AND $block_html.css_editor == 'plain'}checked{/if}>{l s='Без редактора' mod='pwblockhtml'}</label>
                    </div>
                </div>
            </div>
            <label class="">
                {l s='Нужен JS' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="need_js" id="need_js_on" value="1" {if isset($block_html.need_js) AND $block_html.need_js}checked{/if} data-form_group="javascript-form-group">
                    <label for="need_js_on" class="radioCheck">{l s='Да' mod='pwblockhtml'}</label>
                    <input type="radio" name="need_js" id="need_js_off" value="0" {if !isset($block_html.need_js) OR !$block_html.need_js}checked{/if} data-form_group="javascript-form-group">
                    <label for="need_js_off" class="radioCheck">{l s='Нет' mod='pwblockhtml'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
            <div class="" id="javascript-form-group" {if !isset($block_html.need_js) OR !$block_html.need_js}style="display: none;"{/if}>
                <label class="" for="javascript-input">
                    {l s='JS' mod='pwblockhtml'}
                </label>
                <div class="margin-form">
                    <div id="javascript"></div>
                    <textarea id="javascript-input" name="js" {if !isset($block_html.js_editor) OR $block_html.js_editor != 'ace'}style="display: none;"{/if}>{if isset($block_html.js) AND $block_html.js}{$block_html.js|escape:'html':'UTF-8'}{/if}</textarea>
                    <div class="html-input-control">
                        <label class="radio-inline">{l s='Выберите редактор' mod='pwblockhtml'}</label>
                        <label class="radio-inline"><input type="radio" name="js_editor" value="ace" data-editor_for="javascript" {if !isset($block_html.js_editor) OR $block_html.js_editor == 'ace'}checked{/if}>{l s='ACE' mod='pwblockhtml'}</label>
                        <label class="radio-inline"><input type="radio" name="js_editor" value="plain" data-editor_for="javascript" {if isset($block_html.js_editor) AND $block_html.js_editor == 'plain'}checked{/if}>{l s='Без редактора' mod='pwblockhtml'}</label>
                    </div>
                </div>
            </div>
            <label class="" for="order">
                {l s='Порядок' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <input type="text" name="order" id="order" value="{if isset($block_html.order) AND $block_html.order}{$block_html.order}{else}0{/if}">
            </div>
            <label class="">
                {l s='Статус' mod='pwblockhtml'}
            </label>
            <div class="margin-form">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="active" id="active_on" value="1" {if !isset($block_html.active) OR $block_html.active}checked{/if}>
                    <label for="active_on" class="radioCheck">{l s='Вкл' mod='pwblockhtml'}</label>
                    <input type="radio" name="active" id="active_off" value="0" {if isset($block_html.active) AND !$block_html.active}checked{/if}>
                    <label for="active_off" class="radioCheck">{l s='Выкл' mod='pwblockhtml'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
            {$shopAsso}
        <input type="submit" value="1" id="pwblockhtml_form_submit_btn" name="submitAddpwblockhtmlAndStay" class="hidden" style="display:none;" />
    </form>
</fieldset>
