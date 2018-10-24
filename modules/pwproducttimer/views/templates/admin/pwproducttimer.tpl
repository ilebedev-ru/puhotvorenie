<div class="form-group">
    <div class="container">
        <div class="row">
            <label class="control-label col-lg-1" for="product_id">ID Товаров</label>

            <div class="col-lg-11">
                <form id="pwproducttimer_form" class="form-horizontal col-lg-10 col-md-9" action="index.php?controller=AdminModules&token={$AdminTokenLite}&configure=pwproducttimer&tab_module=others&module_name=pwproducttimer" name="pwproducttimer_form" method="post">
                    <script type="text/javascript">
                        {literal}
                            $().ready(function () {
                                var input_id = 'product_id';
                                $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: 'Добавить ID товара'});
                                $('#pwproducttimer_form').submit( function() {
                                    $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                });
                            });
                        {/literal}
                    </script>
                    <input type="text" id="product_id" class="tagify updateCurrentText" name="product_id" value="{implode(',', $product_ids)}" style="display: none;">
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-default pull-right" name="submitPWPRODUCTTIMER"><i class="process-icon-save"></i> {l s='Save' mod='pwproducttimer'}</button>
                    </div>

                    <script type="text/javascript">
                        {*{literal}*}
                            {*$().ready(function () {*}
                                {*$('.tagify-container span a').on('click', function () {*}
                                    {*var id = $('.tagify-container span');*}
                                    {*id.remove();*}
                                {*});*}
                            {*});*}
                        {*{/literal}*}
                    </script>
                </form>
            </div>
        </div>
    </div>
</div>