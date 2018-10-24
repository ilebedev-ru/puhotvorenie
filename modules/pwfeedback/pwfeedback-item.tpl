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

{*<li class="com-item col-xs-6 col-sm-6">*}
<div class="cnt">
    <div class="person-photo">
        <img class="avatar" src="{$feedback.image|escape:'htmlall':'UTF-8'}"
             alt="{$feedback.name|escape:'htmlall':'UTF-8'}">
    </div>
    <div class="person-info">
        <p class="person-name">{$feedback.name|escape:'htmlall':'UTF-8'}</p>
        <p class="person-comment">{$feedback.feedback|escape:'htmlall':'UTF-8'}</p>
    </div>
    {*<div class="right-part">*}
    {*<div class="angle">&nbsp;</div>*}
    {*<div class="txt-box2">*}
    {*<div class="hd-top">*}
    {*<span class="name">{$feedback.name|escape:'htmlall':'UTF-8'}</span>*}
    {*{if isset($feedback.rating)}*}
    {*<div class="rating-static rating{$feedback.rating|escape:'htmlall':'UTF-8'}"></div>{/if}*}
    {*                <div class="soc">*}
    {*{if $feedback.fb}<a href="{$feedback.fb|escape:'htmlall':'UTF-8'}"*}
    {*class="fb">{l s='Facebook' mod='pwfeedback'}</a>{/if}*}
    {*{if $feedback.vk}<a href="{$feedback.vk|escape:'htmlall':'UTF-8'}"*}
    {*class="vk">{l s='Vkontakte' mod='pwfeedback'}</a>{/if}*}
    {*{if $feedback.twitter}<a href="{$feedback.twitter|escape:'htmlall':'UTF-8'}"*}
    {*class="twitter">{l s='Twitter' mod='pwfeedback'}</a>{/if}*}
    {*{if $feedback.odk}<a href="{$feedback.odk|escape:'htmlall':'UTF-8'}"*}
    {*class="odk">{l s='Odnoklassniki.ru' mod='pwfeedback'}</a>{/if}*}
    {*{if $feedback.youtube}<a href="{$feedback.youtube|escape:'htmlall':'UTF-8'}"*}
    {*class="youtube">{l s='Youtube' mod='pwfeedback'}</a>{/if}*}
    {*</div>*}
    {*</div>*}
    {*<div class="inner">*}
    {*{$feedback.feedback|escape:'htmlall':'UTF-8'}*}
    {*</div>*}
    {*</div>*}
    {*</div>*}
</div>
{if $feedback.answer && isset($answerConnect)}
    <div class="cnt-answer">
        <div class="person-info">
            <p class="person-name">{l s='Answer from' mod='pwfeedback'} {$shop_name|escape:'htmlall':'UTF-8'}<</p>
            <p class="person-comment">{$feedback.answer|escape:'htmlall':'UTF-8'}</p>
        </div>
        <div class="person-photo">
            <img class="avatar" src="{$this_path|escape:'htmlall':'UTF-8'}/img/shop.png"
                 alt="{$feedback.name|escape:'htmlall':'UTF-8'}">
        </div>
    </div>
{/if}
{*</li>*}