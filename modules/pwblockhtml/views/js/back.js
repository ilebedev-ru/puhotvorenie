/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(function () {
    initHooks();
    initEditors();
    watchRadioButtons();
});

function initHooks() {
    var hooks = $('#hooks');
    var current_hooks = hooks.data('current-hooks');

    var hooks_multi_select = hooks.select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });

    hooks_multi_select.val(current_hooks).trigger('change');
}

function initEditors() {
    var editors_data = $('#editors-data');
    var elements = ['html', 'css', 'javascript'];
    elements.forEach(function(element) {
        enableACE(element);
        changeEditor(element, editors_data.data(element+'_editor'));
    });
}

function watchRadioButtons() {
    $('input[name=html_editor], input[name=css_editor], input[name=js_editor]').change(function() {
        changeEditor($(this).data('editor_for'), $(this).val());
    });

    $('input[name=need_css], input[name=need_js]').change(function() {
        var form_group = $(this).data('form_group');
        $('#'+form_group).toggle($(this).val());
        changeEditor(form_group.replace('-form-group',''),$('input[name=html_editor]').val());
    });
}

function changeEditor(editor_for, editor) {
    var element = editor_for;

    if (editor == 'ace') {
        showACE(element);
    }
    else if (editor == 'tinymce') {
        enableTinyMCE(element);
    }
    else if (editor == 'plain') {
        hideACE(element);
        disableTinyMCE(element);
    }
}

function enableTinyMCE(element) {
    hideACE(element);

    var input = $('#'+element+'-input');
    if (!input.hasClass('rte')) {
        input.addClass('rte');
    }

    tinySetup(element);
}

function disableTinyMCE(element) {
    var input = $('#'+element+'-input');
    if (input.hasClass('rte')) {
        input.removeClass('rte');
    }

    for (editor_id in tinyMCE.editors) {
        var editor = tinyMCE.editors[editor_id];
        if (editor.settings.id == element+'-input') {
            tinymce.EditorManager.execCommand('mceRemoveControl', true, editor_id);
            tinymce.EditorManager.execCommand('mceRemoveEditor', true, editor_id);
        }
    }
}

function enableACE(element) {
    var input = $('#'+element+'-input');
    if (!input.length) return;
    var editor = ace.edit(element);

    editor.setTheme("ace/theme/chrome");
    editor.session.setMode("ace/mode/"+element);
    editor.setOptions({
        maxLines: 20,
        minLines: 20,
        enableEmmet: true
    });
    editor.setValue(input.val(), 1);
    editor.getSession().on('change', function () {
        input.val(editor.getSession().getValue());
    });

    input.on('change', function () {
        editor.setValue(input.val(), 1);
    })
}

function hideACE(element) {
    $('#'+element).hide('fast');
    $('#'+element+'-input').show('fast');
}

function showACE(element) {
    disableTinyMCE(element);
    $('#'+element+'-input').hide('fast');
    $('#'+element).show('fast');
}

function tinySetup(element)
{
    var input = $('#'+element+'-input');

    var tinymce_data_div = $('#tinymce-data');
    var ad = tinymce_data_div.data('ad');
    var iso = tinymce_data_div.data('iso');

    var config = {
        selector: '#'+element+'-input',
        height : "320px",
        plugins: "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor",
        toolbar1: "code,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,cleanup,|,media,image",
        toolbar2: "",
        external_filemanager_path: ad + "/filemanager/",
        filemanager_title: "File manager",
        external_plugins: {"filemanager": ad + "/filemanager/plugin.min.js"},
        language: iso,
        skin: "prestashop",
        statusbar: false,
        relative_urls: false,
        extended_valid_elements: "em[class|name|id]",
        menu: {
            edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            insert: {title: 'Insert', items: 'media image link | pagebreak'},
            view: {title: 'View', items: 'visualaid'},
            format: {
                title: 'Format',
                items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'
            },
            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
            tools: {title: 'Tools', items: 'code'}
        },
        setup: function(ed) {
            ed.on('NodeChange', function(e) {
                input.val(ed.getContent());
                input.trigger('change');
            });
        }
    };

    tinyMCE.init(config);
}
