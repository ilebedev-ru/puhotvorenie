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
<style>
	div{
		font-family: Arial;
		color: #333;
		font-weight: bold;
		font-size: 13px;
		padding: 5px;
		border-radius: 3px;
		background-color: #7AD47A;
		border: 1px solid #C35555;
	}
</style>
<div>
<p>{l s='Alias enregistered successfully' mod='ogone'}</p>
<p>{l s='This page will refresh soon. You can also refresh it manually.' mod='ogone'}</p>
<noscript>
	<p>{l s='To see new alias, please reload the page' mod='ogone'}</p>
</noscript>
</div>
{literal}
<script>
	if (parent) {
		parent.location.href=parent.location.href
	}
</script>
{/literal}