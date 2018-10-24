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
{capture name=path}
  <a title="{l s='My account' mod='ogone'}" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='ogone'}</a>
  <span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
  {l s='My cards' mod='ogone'}
{/capture}
  <h1 class="page-heading bottom-indent">{l s='My cards' mod='ogone'}</h1>

{if isset($errors) && $errors}
  {foreach $errors as $error}
    <p class="ogone-fo-message alert alert-success">{$error|escape:'html':'UTF-8'}</p>
  {/foreach}
{/if}
{if isset($messages) && $messages}
  {foreach $messages as $message}
    <p class="ogone-fo-error alert alert-danger">{$message|escape:'html':'UTF-8'}</p>
  {/foreach}
{/if}
{if $aliases}
  <p class="cards-title"><i class="icon-credit-card"></i> {l s='My enregistered payment means' mod='ogone'}</p>
  <p class="info-title">{l s='Your card details are securely stored on Ingenico ePayments servers' mod='ogone'}</p>
  <div class="ogone-aliases row clearfix">
  {foreach $aliases as $alias}
    <div class="card col-lg-4 col-md-6">
      <div class="inner-card">
        <img class="picto-card" src="{$alias.logo|escape:'htmlall':'UTF-8'}"/>
        <p class="ogone-alias-cardno">
          <span class="card-info-title">
            {l s='Card number' mod='ogone'} {$alias.brand|escape:'htmlall':'UTF-8'}
          </span>
          {$alias.cardno|escape:'html':'UTF-8'}
        </p>
        <p class="ogone-alias-cn">
          <span class="card-info-title">
            {l s='Card owner' mod='ogone'}
          </span>
          {$alias.cn|escape:'html':'UTF-8'}
        </p>
        <span class="ogone-alias-ed">
          <span class="card-info-title">
            {l s='Expiration date' mod='ogone'}
          </span>
          {$alias.expiry_date|escape:'html':'UTF-8'}
        </span>
        <span class="ogone-alias-delete"><a href="{$alias.delete_link|escape:'html':'UTF-8'}"><i class="icon-trash"></i></a></span>
      </div>
    </div>
  {/foreach}
  </div>
{/if}

<p class="new-card-title"><i class="icon-plus-square-o"></i> {l s='Add a new card' mod='ogone'}</p>
<p class="info-title">{l s='Your card details will be securely stored on Ingenico ePayments servers.' mod='ogone'}.
 {l s='No card details are stored on our servers.' mod='ogone'} <br />
 {l s='You will be asked to enter your CVV/CVC (Card verification code)' mod='ogone'}
 </p>
<iframe src="{$htp_url|escape:'quotes':'UTF-8'}" style="min-width: 400px; min-height: 500px"></iframe>