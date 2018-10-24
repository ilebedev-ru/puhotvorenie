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
*  @author    SeoSA <885588@bk.ru>
*  @copyright 2012-2017 SeoSA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}

{block name='input'}
    {if $input.type == 'category'}
        {assign var='name' value=$input.name}
        {assign var='value' value=$fields_value[$name]}
        <table class="table table_full_width">
            <thead>
                <th>{l s='Category' mod='cdek'}</th>
                <th style="text-align: center">{l s='Width' mod='cdek'}</th>
                <th style="text-align: center">{l s='Height' mod='cdek'}</th>
                <th style="text-align: center">{l s='Length' mod='cdek'}</th>
                <th style="text-align: center">{l s='Weight' mod='cdek'}</th>
            </thead>
            <tbody>
            {if is_array($input.categories) && count($input.categories)}
                {foreach from=$input.categories item=category}
                    <tr>
                        <td>
                            {$category.id_category|escape:'quotes':'UTF-8'}|{$category.name|escape:'quotes':'UTF-8'}
                        </td>
                        <td align="center">
                            <input type="text" name="{$name|escape:'quotes':'UTF-8'}[{$category.id_category|intval}][width]" value="{$value[$category.id_category]['width']|escape:'quotes':'UTF-8'}">
                        </td>
                        <td align="center">
                            <input type="text" name="{$name|escape:'quotes':'UTF-8'}[{$category.id_category|intval}][height]" value="{$value[$category.id_category]['height']|escape:'quotes':'UTF-8'}">
                        </td>
                        <td align="center">
                            <input type="text" name="{$name|escape:'quotes':'UTF-8'}[{$category.id_category|intval}][length]" value="{$value[$category.id_category]['length']|escape:'quotes':'UTF-8'}">
                        </td>
                        <td align="center">
                            <input type="text" name="{$name|escape:'quotes':'UTF-8'}[{$category.id_category|intval}][weight]" value="{$value[$category.id_category]['weight']|escape:'quotes':'UTF-8'}">
                        </td>
                    </tr>
                {/foreach}
            {/if}
            </tbody>
        </table>
    {elseif $input.type == 'carrier'}
        {assign var='name' value=$input.name}
        {assign var='value' value=$fields_value[$name]}
        <table class="table table_full_width">
            <thead>
            <th>{l s='Carriers' mod='cdek'}</th>
            <th style="text-align: center">{l s='Commission' mod='cdek'}</th>
            </thead>
            <tbody>
                {if is_array($input.carriers) && count($input.carriers)}
                    {foreach from=$input.carriers item=carrier}
                        <tr>
                            <td>
                                {$carrier.id_reference|escape:'quotes':'UTF-8'}|{$carrier.name|escape:'quotes':'UTF-8'}
                            </td>
                            <td align="center">
                                <input type="text" name="{$name|escape:'quotes':'UTF-8'}[{$carrier.id_reference|intval}][commission]" value="{$value[$carrier.id_reference]['commission']|escape:'quotes':'UTF-8'}">
                            </td>
                        </tr>
                    {/foreach}
                {/if}
            </tbody>
        </table>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}