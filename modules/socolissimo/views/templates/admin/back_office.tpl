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
*  @author    PrestaShop SA <contact@prestashop.com> Quadra Informatique <modules@quadra-informatique.fr>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($supcostbelg)}{assign var=supcostbelgttc value=$supcostbelg*(1+($taxrate/100))}{/if}
<div class="warn">  <p>{l s='Warning, usage of this module in opc mobile theme is not recommended in production mode for your website.' mod='socolissimo'}</p></div>
<form action="{$smarty.server.REQUEST_URI|escape:'htmlall'}" method="post" class="form">
    <input type="hidden" value={if isset($taxrate)}{$taxrate}{else}0{/if} class="taxrate" name="taxrate" />
    <fieldset><legend><img src="{$moduleDir|escape:'htmlall'}/logo.gif" alt="" />{l s='About Socolissimo Simplicité' mod='socolissimo'}</legend>
        {l s='Colissimo Simplicité is a service offered by La Poste, which allows you to offer your customers multiple modes of delivery' mod='socolissimo'} :
        <ul>
            <li style="font-weight:bold;">{l s='Colissimo at home' mod='socolissimo'} :</li>
            <ul>
                <li>{l s='With signing' mod='socolissimo'}</li>
                <li>{l s='Unsigned' mod='socolissimo'}</li>
            </ul>
            <li style="font-weight:bold;">{l s='Colissimo at a withdrawal point' mod='socolissimo'} :</li>
            <ul>
                <li>{l s='At the post office' mod='socolissimo'}</li>
                <li>{l s='In a Pickup Station' mod='socolissimo'}</li>
                <li>{l s='In one of the 18 000 Pickup Relays available in France' mod='socolissimo'}</li>
            </ul>
        </ul>
    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir|escape:'htmlall'}/logo.gif" alt="" />{l s='Subscribe to Colissimo Simplicité' mod='socolissimo'}</legend>
        {l s='To open your Colissimo account, please contact' mod='socolissimo'} <b>{l s='La Poste' mod='socolissimo'}</b> :
        <ul>
            <li>{l s='By phone : Call' mod='socolissimo'}<b> 3634 </b>{l s='(French phone number)' mod='socolissimo'}</li>
            <li><a href="https://www.colissimo.entreprise.laposte.fr/contact" target="_blank">{l s='By message' mod='socolissimo'}</a></li>
        </ul>
    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir|escape:'htmlall'}/logo.gif" alt="" />{l s='Vendor manual' mod='socolissimo'}</legend>
            {l s='Don\'t hesitate to read the' mod='socolissimo'} 
        <b><a href="{$moduleDir|escape:'htmlall'}/readme_fr.pdf" target="_blank">{l s='Vendor manual' mod='socolissimo'} </a></b> 
        {l s='to help you to configure the module' mod='socolissimo'} 

    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir|escape:'htmlall'}/logo.gif" alt="" />{l s='Colissimo Simplicité Settings' mod='socolissimo'}</legend>
        <label>{l s='Encryption key' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="text" name="key" value="{if isset($key)}{$key}{/if}" />
            <p>{l s='Available in your' mod='socolissimo'}&nbsp;
                <a href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >{l s='Colissimo Box' mod='socolissimo'}</a></p>
        </div>
        <label>{l s='Front Offic Identifier' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="text" name="id_user" value="{if isset($id_user)}{$id_user}{/if}" />
            <p>{l s='Available in your' mod='socolissimo'}&nbsp;
                <a href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >{l s='Colissimo Box' mod='socolissimo'}</a></p>
        </div>
        <label>{l s='Order Preparation time' mod='socolissimo'}: </label>
        <div class="margin-form">
            <input type="text" size="5" name="dypreparationtime" value="{if isset($dypreparationtime)}{$dypreparationtime}{else}0{/if}" />{l s='Day(s)' mod='socolissimo'}
            <p>{l s='Business days from Monday to Friday' mod='socolissimo'} <br><span style="color:red">
                    {l s='Must be the same paramter as in your' mod='socolissimo'}&nbsp;
                    <a style="color:red" href="https://www.colissimo.entreprise.laposte.fr" target="_blank" >{l s='Colissimo Box' mod='socolissimo'}</a></span></p>
        </div>
    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir}/logo.gif" alt="" />{l s='Return URL' mod='socolissimo'}</legend>
        <div class="margin-form"> 
            {l s='Please fill in these two addresses in your' mod='socolissimo'}&nbsp;
            <a href="https://www.colissimo.entreprise.laposte.fr/" target="_blank">{l s='Colissimo Box' mod='socolissimo'}</a>,
            {l s='in the "Simplicity – Delivery options selection page"' mod='socolissimo'} 
            {l s='and in the "Simplicity – Delivery options selection page (mobile version)" configuration pages' mod='socolissimo'}<br/>
        </div>
        <label>{l s='When the customer has successfully selected the delivery method (Validation)' mod='socolissimo'} : </label>
        <div class="margin-form">
            <p>{if isset($validation_url)}{$validation_url}{/if}</p>
        </div>
        <div class="clear">&nbsp;</div>
        <label>{l s='When the client could not select the delivery method (Failed)' mod='socolissimo'} : </label>
        <div class="margin-form">
            <p>{if isset($return_url)}{$return_url}{/if}</p>
        </div>
    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir|escape:'htmlall'}/logo.gif" alt="" />{l s='Colissimo Simplicité System Settings' mod='socolissimo'}</legend>
        <div class="margin-form" style="color:red;font-weight:bold;"> 
            {l s='Be VERY CAREFUL with these settings, any changes may cause the module to malfunction.' mod='socolissimo'}<br/><br/>
        </div>
        <label>{l s='Url So' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="text" size="45" name="url_so" value="{if isset($url_so)}{$url_so|escape:'htmlall':'UTF-8'}{/if}" />
            <p>{l s='Url of back office Colissimo.' mod='socolissimo'}<br/></p>
        </div>
        <label>{l s='Url So Mobile' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="text" size="45" name="url_so_mobile" value="{if isset($url_so_mobile)}{$url_so_mobile|escape:'htmlall':'UTF-8'}{/if}" />
            <p>{l s='Url of back office Colissimo Mobile. Customers with smartphones or ipad will be redirect there. Warning, this url do not allow delivery in belgium' mod='socolissimo'}
                <br/></p>
        </div>
        <label>{l s='Supervision' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="radio" name="sup_active" id="active_on" value="1" {if isset($sup_active) && $sup_active}checked="checked" {/if}/>
            <label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" /></label>
            <input type="radio" name="sup_active" id="active_off" value="0" {if isset($sup_active) && !$sup_active}checked="checked"{/if}/>
            <label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" /></label>
            <p>{l s='Enable or disable the check availability  of Colissimo service.' mod='socolissimo'}</p>
        </div>
        <label>{l s='Url Supervision' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="text" size="45" name="url_sup" value="{if isset($url_sup)}{$url_sup|escape:'htmlall':'UTF-8'}{/if}" />
            <p>{l s='The monitor URL is to ensure the availability of the socolissimo service. We strongly recommend that you do not disable it' mod='socolissimo'}</p>
        </div>
    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir|escape:'htmlall'}/logo.gif" alt="" />{l s='Prestashop System Settings' mod='socolissimo'}</legend>
        <div class="margin-form" style="color:red;font-weight:bold;"> 
            {l s='Be VERY CAREFUL with these settings, any changes may cause the module to malfunction.' mod='socolissimo'}<br/><br/>
        </div>
        <label>{l s='Display Mode' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="radio" name="display_type" id="classic_on" value="0" {if isset($display_type) && !$display_type} checked="checked" {/if}/>
            <label class="t" for="classic_on"> Classic </label>
            <input type="radio" name="display_type" id="fancybox_on" value="1" {if isset($display_type) && $display_type == 1} checked="checked" {/if}/>
            <label class="t" for="fancybox_on"> Fancybox </label>
            <input type="radio" name="display_type" id="iframe_on" value="2" {if isset($display_type) && $display_type == 2} checked="checked"{/if}/>
            <label class="t" for="iframe_on"> iFrame </label>
            <p>{l s='Choose your display mode for windows Socolissimo' mod='socolissimo'}</p>
        </div>
        <label>{l s='Home carrier' mod='socolissimo'} : </label>
        <div class="margin-form">
            <select name="id_socolissimo_allocation">
                {foreach from=$carrier_socolissimo item=carrier}
                    {if $carrier.id_carrier == $id_socolissimo}
                        <option value="{$carrier.id_carrier|escape:'htmlall':'UTF-8'}" selected>{$carrier.id_carrier|escape:'htmlall':'UTF-8'} - {$carrier.name|escape:'htmlall':'UTF-8'}</option>
                    {else}
                        <option value="{$carrier.id_carrier|escape:'htmlall':'UTF-8'}">{$carrier.id_carrier|escape:'htmlall':'UTF-8'} - {$carrier.name|escape:'htmlall':'UTF-8'}</option>
                    {/if}
                {/foreach}
            </select>
            <p>{l s='Carrier used to get "Colissimo at home" cost' mod='socolissimo'}</p>
        </div>
        <label>{l s='Withdrawal point cost' mod='socolissimo'} : </label>
        <div class="margin-form">
            <input type="radio" name="costseller" id="sel_on" value="1" {if isset($costseller) && $costseller}checked="checked" {/if}'/>
            <label class="t" for="sel_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='socolissimo'}" title="{l s='Enabled' mod='socolissimo'}" /></label>
            <input type="radio" name="costseller" id="sel_off" value="0" {if  isset($costseller) && !$costseller} checked="checked" {/if}/>
            <label class="t" for="sel_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='socolissimo'}'" title="{l s='Disabled' mod='socolissimo'}" /></label>
            <p>{l s='This cost override the normal cost for seller delivery.' mod='socolissimo'}</p>
        </div> 
        <label>{l s='Withdrawal point carrier' mod='socolissimo'} : </label>
        <div class="margin-form">
            <select name="id_socolissimocc_allocation">
                {foreach from=$carrier_socolissimo_cc item=carrier}
                    {if $carrier.id_carrier == $id_socolissimo_cc}
                        <option value="{$carrier.id_carrier|escape:'htmlall':'UTF-8'}" selected>{$carrier.id_carrier|escape:'htmlall':'UTF-8'} - {$carrier.name|escape:'htmlall':'UTF-8'}</option>
                    {else}
                        <option value="{$carrier.id_carrier|escape:'htmlall':'UTF-8'}">{$carrier.id_carrier|escape:'htmlall':'UTF-8'} - {$carrier.name|escape:'htmlall':'UTF-8'}</option>
                    {/if}
                {/foreach}
            </select>
            <p>{l s='Carrier used to get "Colissimo at a withdrawal point" cost' mod='socolissimo'}</p>
        </div>
    </fieldset>
    <div class="clear">&nbsp;</div>
    <fieldset><legend><img src="{$moduleDir}/logo.gif" alt="" />{l s='Save configuration' mod='socolissimo'}</legend>
        <div class="margin-form"> 
            {l s='Don\'t hesitate to read the' mod='socolissimo'} 
            <b><a href="{$moduleDir|escape:'htmlall'}/readme_fr.pdf" target="_blank">{l s='Vendor manual' mod='socolissimo'} </a></b> 
            {l s='to help you to configure the module' mod='socolissimo'} 
        </div>
        <div class="margin-form">
            <input type="submit" value="{l s='Save' mod='socolissimo'}" name="submitSave" class="button" style="margin:10px 0px 0px 25px;" />
        </div>
    </fieldset>
</form>
{literal}
    <script type="text/javascript">
        $(document).ready(function () {
            $(".supcostbelg").change(function () {
                var ttc = $(".supcostbelg").val() * (1 + ($(".taxrate").val() / 100));
                ttc = Math.round(ttc * 100) / 100;
                $(".costbelgttc").val(ttc);
            });
        });
    </script>
{/literal}