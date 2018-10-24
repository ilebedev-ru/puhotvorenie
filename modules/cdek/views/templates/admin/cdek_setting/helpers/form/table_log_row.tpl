<tr>
    <td>{$row.id_cdek_logger|escape:'quotes':'UTF-8'}</td>
    <td>{$row.method|escape:'quotes':'UTF-8'}</td>
    <td>{$row.message|escape:'quotes':'UTF-8'}</td>
    <td>
        <label class="data_api" for="show_request_{$row.id_cdek_logger|escape:'quotes':'UTF-8'}">
                                            <span class="btn btn-default">
                                                <i class="icon-list"></i>
                                            </span>
            <input type="checkbox" id="show_request_{$row.id_cdek_logger|escape:'quotes':'UTF-8'}">
            <section class="request_code">
                <code class="json">
                    {$row.request|escape:'htmlall':'UTF-8'}
                </code>
                <button data-copy-field class="btn btn-default" type="button">
                    <i class="icon-copy"></i>
                    {l s='Copy' mod='cdek'}
                </button>
            </section>
        </label>
    </td>
    <td>
        <label class="data_api" for="show_response_{$row.id_cdek_logger|escape:'quotes':'UTF-8'}">
                                            <span type="button" class="btn btn-default">
                                                <i class="icon-list"></i>
                                            </span>
            <input type="checkbox" id="show_response_{$row.id_cdek_logger|escape:'quotes':'UTF-8'}">
            <section class="request_code">
                <code class="json">
                    {$row.response|escape:'html':'UTF-8'}
                </code>
                <button data-copy-field class="btn btn-default float-right" type="button">
                    <i class="icon-copy"></i>
                    {l s='Copy' mod='cdek'}
                </button>
            </section>
        </label>
    </td>
    <td>{date('H:i:s d-m-Y', strtotime($row.date_add))|escape:'quotes':'UTF-8'}</td>
</tr>