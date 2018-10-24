<div class="panel">
    <h3>{l s='Редактирование товара'}</h3>

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