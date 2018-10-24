{*
* 2007-2016 PrestaShop
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
* @author    SeoSA <885588@bk.ru>
* @copyright 2012-2017 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
    var cdek_carriers = {$cdek_carriers|json_encode nofilter};
    var cdek_dir = "{$cdek_dir|escape:'quotes':'UTF-8'}";
    var cdek_address_parameter = {$cdek_address_parameter|intval};
    var hourText = "{l s='h' mod='cdek'}";
    var cdek_order_info = {
        date: "{if $cdek_order_info->delivery_date && $cdek_order_info->delivery_date != '0000-00-00 00:00:00' && $cdek_order_info->delivery_date != '0000-00-00'}{date('d-m-Y', strtotime($cdek_order_info->delivery_date))|escape:'quotes':'UTF-8'}{else}00-00-0000{/if}",
        time_begin: "{$cdek_order_info->delivery_time_begin|escape:'quotes':'UTF-8'}",
        time_end: "{$cdek_order_info->delivery_time_end|escape:'quotes':'UTF-8'}"
    };
</script>
<script type="text/html" id="cdek_address">
    <div class="cdek_address">
        {*<div class="form-group clearfix">*}
            {*<label class="control-label col-lg-3">{l s='Street' mod='cdek'}</label>*}
            {*<div class="col-lg-9">*}
                {*<input data-cdek-address class="cdek_street form-control">*}
            {*</div>*}
        {*</div>*}
        {*<div class="form-group clearfix">*}
            {*<label class="control-label col-lg-3">{l s='House' mod='cdek'}</label>*}
            {*<div class="col-lg-9">*}
                {*<input data-cdek-address class="cdek_house form-control">*}
            {*</div>*}
        {*</div>*}
        {*<div class="form-group clearfix">*}
            {*<label class="control-label col-lg-3">{l s='Flat' mod='cdek'}</label>*}
            {*<div class="col-lg-9">*}
                {*<input data-cdek-address class="cdek_flat form-control">*}
            {*</div>*}
        {*</div>*}
        {include file="./delivery_time.tpl" time_option=true}
    </div>
</script>

<script type="text/html" id="cdek_pvz_list">
    <div class="cdek_pvz_list">
        <div class="cdek_selected">
            %selected%
        </div>
        <div class="cdek_list">
            %list%
        </div>
    </div>
</script>

<script type="text/html" id="cdek_pvz_list_item">
    <div class="cdek_pvz_list_item %active%" data-delivery-point="%city%, %address%" data-code="%code%" daat>
        <div class="cdek_info col-xs-12">
            <div class="cdek_item_address">{l s='Address' mod='cdek'}: %city%, %address%</div>
            <div class="cdek_item_phone"><b>{l s='Phone' mod='cdek'}: %phone%</b></div>
            <div>{l s='Work time' mod='cdek'}:<br> %work_time%</div>
            <div class="click_for_select">
                {l s='Select' mod='cdek'}
            </div>
        </div>
        {include file="./delivery_time.tpl" time_option=true}
    </div>
</script>