{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<p class="buttons_bottom_block">
	<a id="wishlist_button">
		<i class="fa fa-heart-o" aria-hidden="true"></i>
		{if $isLogged}
			{if !$isCustomerFavoriteProduct}
				<span id="pwfavoriteAdd" class="add">
					{*{l s='В список желаемого' mod='favoriteproducts'}*}
				</span>
			{else}
				<span id="pwfavoriteRemove" class="add">
				{*{l s='В список желаемого' mod='favoriteproducts'}*}
				</span>
			{/if}
		{/if}
		{*{if $isCustomerFavoriteProduct AND $isLogged}*}
		{*<span id="favoriteproducts_block_extra_remove">*}
			{*{l s='Убрать из списка желаемого' mod='favoriteproducts'}*}
		{*</span>*}
		{*{/if}*}
		{*<span id="favoriteproducts_block_extra_added" style="display:none;">*}
		{*{l s='Убрать из списка желаемого' mod='favoriteproducts'}*}
		{*</span>*}
		{*<span id="favoriteproducts_block_extra_removed" style="display:none;">*}
			{*{l s='В список желаемого' mod='favoriteproducts'}*}
		{*</span>*}
	</a>
</p>

