{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
    var PWFeedbackURL = '{$link->getModuleLink('pwfeedback', 'view')|escape:'htmlall':'UTF-8'}';
    var PWFeedback_success = '{l s='Thank you for you feedback! It will be able soon.' mod='pwfeedback'}';
</script>
<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std com-form" id="addPWFeedback">
    <div class="hd">{l s='Put feedback' mod='pwfeedback'}</div>
    <div class="alert alert-danger errors"></div>
    <fieldset>
        <div class="form-group text required">
            <label for="name">{l s='Your name' mod='pwfeedback'} <sup>*</sup></label>
            <input class="form-control" type="text" id="name" size="50" name="name"
                   value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'htmlall':'UTF-8'|stripslashes}{elseif isset($customer) && $customer->firstname}{$customer->firstname|escape:'htmlall':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group">
            <label>{l s='Your grade for our shop' mod='pwfeedback'}</label>
            <span class="rating">
                {foreach from=$pwconfig.rating item=rating}
                    <label for="rating-input-1-{$rating.value|escape:'htmlall':'UTF-8'}" data-value="{$rating.value|escape:'htmlall':'UTF-8'}" class="rating-star"></label>
                {/foreach}
            </span>
            <input type="hidden" name="rating" value="0"/>
        </div>
        {if isset($pwconfig.vk)}
            <div class="form-group soc vk text">
                <span>&nbsp;</span>
                <label for="vk">{l s='Link to profile' mod='pwfeedback'} {l s='Vkontakte' mod='pwfeedback'}:</label>
                <input class="form-control" type="text" id="vk" size="50" name="vk"
                       value="{if isset($smarty.post.vk)}{$smarty.post.vk|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
            </div>
        {/if}
        {if isset($pwconfig.fb)}
            <div class="form-group soc fb text">
                <span>&nbsp;</span>
                <label for="fb">{l s='Link to profile' mod='pwfeedback'} {l s='Facebook' mod='pwfeedback'}:</label>
                <input class="form-control" type="text" id="fb" size="50" name="fb"
                       value="{if isset($smarty.post.fb)}{$smarty.post.fb|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
            </div>
        {/if}
        {if isset($pwconfig.twitter)}
            <div class="form-group soc twitter text">
                <span>&nbsp;</span>
                <label for="twitter">{l s='Link to profile' mod='pwfeedback'} {l s='Twitter' mod='pwfeedback'}:</label>
                <input class="form-control" type="text" id="twitter" size="50" name="twitter"
                       value="{if isset($smarty.post.twitter)}{$smarty.post.twitter|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
            </div>
        {/if}
        {if isset($pwconfig.odk)}
            <div class="form-group soc odk text">
                <span>&nbsp;</span>
                <label for="odk">{l s='Link to profile' mod='pwfeedback'} {l s='Odnoklassniki.ru' mod='pwfeedback'}
                    :</label>
                <input class="form-control" type="text" id="odk" size="50" name="odk"
                       value="{if isset($smarty.post.odk)}{$smarty.post.odk|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
            </div>
        {/if}
        {if isset($pwconfig.youtube)}
            <div class="form-group soc youtube text">
                <span>&nbsp;</span>
                <label for="youtube">{l s='Link to profile' mod='pwfeedback'} {l s='Youtube' mod='pwfeedback'}:</label>
                <input class="form-control" type="text" id="youtube" size="50" name="youtube"
                       value="{if isset($smarty.post.youtube)}{$smarty.post.youtube|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
            </div>
        {/if}
        <div class="form-group text">
            <label for="email">{l s='E-mail(only for administration)' mod='pwfeedback'}</label>
            <input class="form-control" type="text" id="email" size="50" name="email"
                   value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{elseif isset($customer) && $customer->email}{$customer->email|escape:'htmlall':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group textarea required">
            <label for="feedback">{l s='Your feedback' mod='pwfeedback'}<sup>*</sup></label>
            <textarea class="form-control" id="feedback" name="feedback" rows="4"
                      cols="47">{if isset($smarty.post.feedback)}{$smarty.post.feedback|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
        </div>
    </fieldset>
    <div class="submit">
        <input type="submit" class="btn btn-primary" name="SubmitPWFeedback"
               value="{l s='Send feedback' mod='pwfeedback'}"/>
    </div>
</form>