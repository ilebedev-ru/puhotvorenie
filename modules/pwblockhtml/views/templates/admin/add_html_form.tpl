{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="row">
    <div class="col-lg-12">

        {if isset($success) AND $success}
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {l s='Сохранено' mod='pwblockhtml'}
            </div>
        {/if}

        <div class="panel">
            <div class="panel-heading">
                {l s='Инструкция' mod='pwblockhtml'}
            </div>
            {l s='Этот блок с HTML кодом будет привязан ко всем хукам, которые вы укажите в поле "Хуки".' mod='pwblockhtml'} <br>
            {l s='Редактировать расположение модуля внутри хука можно на' mod='pwblockhtml'} <a href="{$link->getAdminLink('AdminModulesPositions')}" target="_blank">{l s='этой странице.' mod='pwblockhtml'}</a><br>
            {l s='В поле "Хуки" вы можете указать несуществующий хук, он будет создан.' mod='pwblockhtml'} <br>
            {l s='Чтобы отобразить на странице созданный хук нужно добавить в файле шаблона в нужном вам месте вызов этого хука. Например:' mod='pwblockhtml'} {literal}{hook h='myNewHook'}{/literal}
        </div>

        <div class="panel">

            <div class="panel-heading">
                {l s='Блок HTML' mod='pwblockhtml'}
            </div>

            <form id="pwblockhtml_form" class="form-horizontal" method="post">

                <input type="hidden" name="submitAddPWBlockHTML" value="1">
                <input type="hidden" name="id_pwblockhtml" value="{if isset($block_html.id_pwblockhtml) AND $block_html.id_pwblockhtml}{$block_html.id_pwblockhtml}{else}0{/if}">
                <div id="tinymce-data" class="hidden" data-ad="{$ad}" data-iso="{$iso}"></div>
                <div id="editors-data" class="hidden"
                     data-html_editor="{if isset($block_html.html_editor) AND $block_html.html_editor}{$block_html.html_editor}{else}ace{/if}"
                     data-css_editor="{if isset($block_html.css_editor) AND $block_html.css_editor}{$block_html.css_editor}{else}ace{/if}"
                     data-javascript_editor="{if isset($block_html.js_editor) AND $block_html.js_editor}{$block_html.js_editor}{else}ace{/if}"
                ></div>

                <div class="form-wrapper">

                    <div class="form-group">
                        <label for="name" class="control-label col-lg-3 required">
                            {l s='Название' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="name" id="name" value="{if isset($block_html.name) AND $block_html.name}{$block_html.name}{/if}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3 required" for="hooks">
                            {l s='Хуки' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <select name="hooks[]" class="hooks" id="hooks" multiple="multiple" data-current-hooks="{$current_hooks|escape:'html':'UTF-8'}">
                                {if $hooks}
                                    {foreach from=$hooks item=hook}
                                        <option value="{$hook.name|strtolower}">{$hook.name}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="html-input">
                            {l s='HTML' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <div id="html"></div>
                            <textarea id="html-input" name="html" {if !isset($block_html.html_editor) OR $block_html.html_editor != 'ace'}style="display: none;"{/if}>{if isset($block_html.html) AND $block_html.html}{$block_html.html|escape:'html':'UTF-8'}{/if}</textarea>
                            <div class="html-input-control">
                                <label class="radio-inline">{l s='Выберите редактор' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="html_editor" value="ace" data-editor_for="html" {if !isset($block_html.html_editor) OR $block_html.html_editor == 'ace'}checked{/if}>{l s='ACE' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="html_editor" value="tinymce" data-editor_for="html" {if isset($block_html.html_editor) AND $block_html.html_editor == 'tinymce'}checked{/if}>{l s='TinyMCE' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="html_editor" value="plain" data-editor_for="html" {if isset($block_html.html_editor) AND $block_html.html_editor == 'plain'}checked{/if}>{l s='Без редактора' mod='pwblockhtml'}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            {l s='Нужен CSS' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="need_css" id="need_css_on" value="1" {if isset($block_html.need_css) AND $block_html.need_css}checked{/if} data-form_group="css-form-group">
                                <label for="need_css_on" class="radioCheck">{l s='Да' mod='pwblockhtml'}</label>
                                <input type="radio" name="need_css" id="need_css_off" value="0" {if !isset($block_html.need_css) OR !$block_html.need_css}checked{/if} data-form_group="css-form-group">
                                <label for="need_css_off" class="radioCheck">{l s='Нет' mod='pwblockhtml'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group" id="css-form-group" {if !isset($block_html.need_css) OR !$block_html.need_css}style="display: none;"{/if}>
                        <label class="control-label col-lg-3" for="html-input">
                            {l s='CSS' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <div id="css"></div>
                            <textarea id="css-input" name="css" {if !isset($block_html.css_editor) OR $block_html.css_editor != 'ace'}style="display: none;"{/if}>{if isset($block_html.css) AND $block_html.css}{$block_html.css|escape:'html':'UTF-8'}{/if}</textarea>
                            <div class="html-input-control">
                                <label class="radio-inline">{l s='Выберите редактор' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="css_editor" value="ace" data-editor_for="css" {if !isset($block_html.css_editor) OR $block_html.css_editor == 'ace'}checked{/if}>{l s='ACE' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="css_editor" value="plain" data-editor_for="css" {if isset($block_html.css_editor) AND $block_html.css_editor == 'plain'}checked{/if}>{l s='Без редактора' mod='pwblockhtml'}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            {l s='Нужен JS' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="need_js" id="need_js_on" value="1" {if isset($block_html.need_js) AND $block_html.need_js}checked{/if} data-form_group="js-form-group">
                                <label for="need_js_on" class="radioCheck">{l s='Да' mod='pwblockhtml'}</label>
                                <input type="radio" name="need_js" id="need_js_off" value="0" {if !isset($block_html.need_js) OR !$block_html.need_js}checked{/if} data-form_group="js-form-group">
                                <label for="need_js_off" class="radioCheck">{l s='Нет' mod='pwblockhtml'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group" id="js-form-group" {if !isset($block_html.need_js) OR !$block_html.need_js}style="display: none;"{/if}>
                        <label class="control-label col-lg-3" for="html-input">
                            {l s='JS' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <div id="javascript"></div>
                            <textarea id="javascript-input" name="js" {if !isset($block_html.js_editor) OR $block_html.js_editor != 'ace'}style="display: none;"{/if}>{if isset($block_html.js) AND $block_html.js}{$block_html.js|escape:'html':'UTF-8'}{/if}</textarea>
                            <div class="html-input-control">
                                <label class="radio-inline">{l s='Выберите редактор' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="js_editor" value="ace" data-editor_for="javascript" {if !isset($block_html.js_editor) OR $block_html.js_editor == 'ace'}checked{/if}>{l s='ACE' mod='pwblockhtml'}</label>
                                <label class="radio-inline"><input type="radio" name="js_editor" value="plain" data-editor_for="javascript" {if isset($block_html.js_editor) AND $block_html.js_editor == 'plain'}checked{/if}>{l s='Без редактора' mod='pwblockhtml'}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3" for="order">
                            {l s='Порядок' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="order" id="order" value="{if isset($block_html.order) AND $block_html.order}{$block_html.order}{else}0{/if}">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            {l s='Статус' mod='pwblockhtml'}
                        </label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="active" id="active_on" value="1" {if !isset($block_html.active) OR $block_html.active}checked{/if}>
                                <label for="active_on" class="radioCheck">{l s='Вкл' mod='pwblockhtml'}</label>
                                <input type="radio" name="active" id="active_off" value="0" {if isset($block_html.active) AND !$block_html.active}checked{/if}>
                                <label for="active_off" class="radioCheck">{l s='Выкл' mod='pwblockhtml'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    {$shopAsso}
                </div>

                <div class="panel-footer">
                    <button type="submit" value="1" id="pwblockhtml_form_submit_btn" name="submitAddpwblockhtmlAndStay" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Сохранить' mod='pwblockhtml'}
                    </button>
                    <a href="index.php?controller=AdminPWBlockHTML{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}"
                       class="btn btn-default pull-right" onclick="window.history.back();">
                        <i class="process-icon-cancel"></i> {l s='Отмена' mod='pwblockhtml'}
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
