<div id="pwcatseo_title" class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="Заменяет имя категории">Заголовок</span>
	</label>
	<div class="col-lg-9">
		<input type="text" size="48" name="pwcat2seo_title" value="{if isset($pwtitle)}{$pwtitle}{/if}" />
	</div>
</div>
<div id="pwcatseo_text" class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip" data-html="true" data-original-title="Отображается вверху страницы">Описание 2</span>
	</label>
	<div class="col-lg-9">
		<textarea class="rte autoload_rte" name="pwcatseo_text">{if isset($pwtext)}{$pwtext}{/if}</textarea>
	</div>
</div>