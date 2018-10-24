<h1>Генератор модулей</h1>
<form method="post" action=""  class="pure-form pure-form-stacked">
    <p>
        <label for="">Имя модуля(транслит)</label>
        <input style="width:300px" name="name" value="{if isset($smarty.post.name)}{$smarty.post.name}{else}pw{/if}" type="text" />
    </p>

    <p>
        <label for="">Отображаемое имя</label>
        <input style="width:300px" name="displayName" value="{if isset($smarty.post.displayName)}{$smarty.post.displayName}{else}Модуль №1{/if}" type="text" />
    </p>
    <p>
        <label for="">Отображаемое описание</label>
        <input style="width:300px" name="displayDesc" value="{if isset($smarty.post.displayDesc)}{$smarty.post.displayDesc}{else}Модуль №1{/if}" type="text" />
    </p>

    <p class="checkbox">
        <label for="">Хуки</label>
    <div class="margin-row">
        {foreach from=$hooks item=hook}
            <label for="{$hook}">{$hook}</label><input id="{$hook}" type="checkbox" name="hooks[{$hook}]" value="1" {if isset($smarty.post.hooks[$hook])} checked="checked"{/if}/><br />
        {/foreach}
        <label for="myhooks">Свои хуки</label>
        <input type="text" size="100" id="myhooks" name="myhooks" value="{if isset($smarty.post.hooks.myhooks)}{$smarty.post.hooks.myhooks}{/if}" placeholder="Через запятую" /><br />
    </div>
    </p>
    <p><textarea name="code" id="" cols="30" rows="10" placeholder="Код для функций хуков"></textarea></p>
    <p><textarea name="tpl" id="" cols="30" rows="10">Новый сгенерированный хук</textarea></p>
    <p><label for="helpers">Хелперы + обработчик</label><input name="helpers" value="1" id="helpers" type="checkbox" {if isset($smarty.post.helpers)} checked="checked"{/if} /></p>
    <p><label for="makeController">Фронт контроллер(отдельная страница)</label><input name="makeController" value="1" id="makeController" type="checkbox" {if isset($smarty.post.makeController)} checked="checked"{/if} /></p>
    <p><label for="adminProductHook">Подключить хуки для редактирования в карточке товара</label><input name="adminProductHook" value="1" id="adminProductHook" type="checkbox" {if isset($smarty.post.adminProductHook)} checked="checked"{/if} /></p>
    <p><label for="makeClass">Создать класс и базу</label><input name="makeClass" value="1" id="makeClass" type="checkbox" {if isset($smarty.post.makeClass)} checked="checked"{/if} /></p>
    <p><label for="photo">Загрузка фото</label><input name="photo" value="1" id="photo" type="checkbox"/></p>
    <p><label for="installit">Сразу и установить</label><input name="installit" value="1" checked id="installit" type="checkbox"/></p>
    <p><label for=""></label><input class="pure-button pure-button-primary" type="submit" name="addModule" value="Генерировать" type="text"/></p>
</form>