<noindex>
<div id="pwseo">
	<div class="headline">
		<div class="col1 col">Тип страницы: <b>{$idEntity}</b></div>
		{* <div class="col2 col"><a href="{$pwseo.admin_edit_url}">Редактировать в админке</a></div>*}
		<div class="col3 col"></div>
		<div class="col col4">Для быстрого вызова нажми <b>Alt + C</b></div>
	</div>

</div>
    {if $page_name == 'product' && $product->id_category_default}
        {assign var='redirect_link' value=$link->getCategoryLink($product->id_category_default, $product->category)|escape:'html':'UTF-8'}
    {elseif $page_name == 'category'}
        {if $category->id_parent > 2}
            {assign var='redirect_link' value=$link->getCategoryLink($category->id_parent)|escape:'html':'UTF-8'}
        {else}
            {assign var='redirect_link' value=$base_dir|escape:'html':'UTF-8'}
        {/if}
    {else}
        {assign var='redirect_link' value=$base_dir|escape:'html':'UTF-8'}
    {/if}
<div id="pwseo_container" class="js-cart-open">
    <a id="pwseo_editlink" href="#">{l s='Редактировать' mod='pwseo'}</a>
    <div id="pwseo_menu" style="display:none">
        <div class="pwseo_wrapper">
            <a href="#pwseo_meta_popup" id="pwseo_meta_edit">{l s='Редактировать seo' mod='pwseo'} (Alt+v)</a>
            {*{if !empty($fieldsFormDescription)}*}
            {if $fieldsFormDescription|strpos:'textarea' !== false}<a href="#pwseo_description_popup" id="pwseo_desc_edit">{l s='Редактировать описание' mod='pwseo'} (Alt+b)</a>{/if}
            {if $editor->getStatus() != "не определено"}<a id="pwseo_status_edit" {if $editor->getStatus() == "Включить"}data-seoredirect="{$smarty.server.REQUEST_URI}"{elseif $redirect_link}data-seoredirect="{$redirect_link}"{/if} class="edit-active" href="#">{$editor->getStatus()} {if $page_name == 'category'}категорию{elseif $page_name == 'product'}товар{/if}</a>{/if}
            {*{if $page_name == 'product'}<a href="{$link->getAdminLink('AdminProducts', true)|escape:'html':'UTF-8'}&id_product={$product->id}">{l s='Редактировать товар' mod='pwseo'}</a>{/if}*}
        </div>
    </div>
</div>

<div id="pwseo_meta_popup" style="display:none;">
    <div class="pwseo_title">Редактирование метаданных</div>
    <div class="edit">
        {*<a href="#" class="edit-description button">Редактировать оипсание</a>*}
        <form class="seoform">
            <input type="hidden" name="action" value="send"/>
            <input type="hidden" name="id_entity" value="{$idEntity}"/>
            <input type="hidden" name="id_item" value="{$idItem}"/>
            <input type="hidden" name="editor_name" value="{$editorName}"/>
            {$fieldsForm}
            <input type="submit" value="Сохранить изменения" class="button"/>
            <a class="button" href="javascript:window.location.reload('true')">Перезагрузить страницу</a>
        </form>
    </div>
</div>
<div id="pwseo_description_popup"  style="display:none;">
    <div class="pwseo_title">Редактирование описания</div>
    <div class="edit">
        {*<a href="#" class="edit-description button">Редактировать оипсание</a>*}
        <form class="seoform">
            <input type="hidden" name="action" value="send"/>
            <input type="hidden" name="id_entity" value="{$idEntity}"/>
            <input type="hidden" name="id_item" value="{$idItem}"/>
            <input type="hidden" name="editor_name" value="{$editorName}"/>
            {$fieldsFormDescription}
            <input type="submit" value="Сохранить изменения" class="button"/>
            <a class="button" href="javascript:window.location.reload('true')">Перезагрузить страницу</a>
        </form>
    </div>
</div>
</noindex>