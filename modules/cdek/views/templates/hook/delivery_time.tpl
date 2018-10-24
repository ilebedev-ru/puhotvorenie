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

<div class="cdek_delivery_date_block clearfix">

    <div class="col-md-6 col-lg-3 clearfix">
        <label>
            {l s='Desired delivery time' mod='cdek'}
        </label>
    </div>

    <div class="col-md-6 col-lg-3 clearfix">
        <div>
            {l s='Date' mod='cdek'}
        </div>
        <div>
            <input readonly type="text" value="0000-00-00" class="cdek_delivery_date form-control" >
        </div>
    </div>

    {if isset($time_option) && $time_option}

        <div class="col-md-6 col-lg-3 clearfix">
            <div>
                {l s='Time begin' mod='cdek'}
            </div>
            <div class="clockpicker input-group time_begin_clockpicker" data-align="top" data-autoclose="true">
                <input type="text" class="cdek_delivery_time_begin form-control" >
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                </span>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 clearfix">
            <div>
                {l s='Time end' mod='cdek'}
            </div>
            <div class="clockpicker input-group time_end_clockpicker" data-autoclose="true">
                <input type="text" class="cdek_delivery_time_end form-control" >
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                </span>
            </div>
        </div>

    {/if}

</div>
