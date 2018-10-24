<div class="panel">
    <h3>{l s='Редактирование товара'}</h3>
    <label for="id_label">Шильдик</label>
    <select name="id_label" id="id_label">
        <option value="0" {if !$obj->id_label}selected="selected"{/if}>Нет</option>
        <option value="1" {if $obj->id_label == 1}selected="selected"{/if}>Скидка</option>
        <option value="2" {if $obj->id_label == 2}selected="selected"{/if}>Новинка</option>
        <option value="3" {if $obj->id_label == 3}selected="selected"{/if}>Хит</option>
        <option value="4" {if $obj->id_label == 4}selected="selected"{/if}>Лучшее</option>
        <option value="5" {if $obj->id_label == 5}selected="selected"{/if}>Товар дня</option>
        <option value="6" {if $obj->id_label == 6}selected="selected"{/if}>+ подарок</option>
    </select>
    <div class="clear clearfix"></div>
    <div class="panel-footer">
        <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i
                    class="process-icon-cancel"></i> {l s='Отмена'}</a>
        <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i
                    class="process-icon-save"></i> {l s='Сохранить'}</button>
        <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i
                    class="process-icon-save"></i> {l s='Сохранить и остаться'}</button>
    </div>
</div>