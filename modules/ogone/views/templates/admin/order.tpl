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
<div class="panel" id="ingenicoOrder">
			<h3>{l s='Ingenico ePayments' mod='ogone'}</h3>
			{if $can_use_direct_link}
					{if $can_capture}
						<a  class="btn btn-primary" href="{$capture_link|escape:'htmlall':'UTF-8'}"  title="{$cc_title|escape:'htmlall':'UTF-8'}">{l s='Capture' mod='ogone'}</a>
					{else}
						<a  class="btn btn-disabled" href="#" title="{$cc_title|escape:'htmlall':'UTF-8'}" onclick='alert(this.title);'>{l s='Capture' mod='ogone'}</a>
					{/if}
			{else}
				<div class="bootstrap">
					<div class="alert alert-warning">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<ul class="list-unstyled">
								<li>{l s='In order to use advanced features you need to activate and configure DirectLink' mod='ogone'}</li>
						</ul>
					</div>
				</div>
			{/if}
</div>