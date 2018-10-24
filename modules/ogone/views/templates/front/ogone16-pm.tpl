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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="row">
  <div class="col-xs-12">
    <p class="payment_module" onclick="document.forms['ogone_form_{$pm_obj->id|escape:'htmlall':'UTF-8'}'].submit(); return false;" >
      <a class="ogone"  href="#" title="{$pm_obj->name|escape:'htmlall':'UTF-8'}">
        <img src="{$pm_obj->logo_url|escape:'htmlall':'UTF-8'}"/>
        {$pm_obj->name|escape:'htmlall':'UTF-8'}{if $pm_obj->description}<br /><span>{$pm_obj->description|escape:'htmlall':'UTF-8'}</span>{/if}
      </a>
    </p>
  </div>
  <form name="ogone_form_{$pm_obj->id|escape:'htmlall':'UTF-8'}" action="{$ogone_url|escape:'htmlall':'UTF-8'}" method="post">
    {foreach from=$ogone_params key=ogone_key item=ogone_value}
      <input type="hidden" name="{$ogone_key|escape:'htmlall':'UTF-8'}" value="{$ogone_value|escape:'htmlall':$form_encoding}" />
    {/foreach}
  </form>
</div>