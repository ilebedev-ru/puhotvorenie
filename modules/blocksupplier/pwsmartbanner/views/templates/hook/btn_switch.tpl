<div id="yaExport" style="display:none;" class="form-group">
    <div class="col-lg-1">
        <span class="pull-right"></span>
    </div>
    <label class="control-label col-lg-2">Экспорт в Я.Маркет</label>
    <div class="col-lg-9">
        <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" name="yaExport" id="yaExport_on" value="1" {if $yaExport}checked="checked"{/if}>
            <label for="yaExport_on" class="radioCheck">Да</label>
            <input type="radio" name="yaExport" id="yaExport_off" value="0" {if !$yaExport}checked="checked"{/if}>
            <label for="yaExport_off" class="radioCheck">Нет</label>
            <a class="slide-button btn"></a>
        </span>
    </div>
</div>