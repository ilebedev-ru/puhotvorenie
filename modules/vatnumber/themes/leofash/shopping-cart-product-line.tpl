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

<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="cart_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} alternate_item{/if} address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}">
	<td class="cart_product ajax_block_product">
		<a class="product" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.name|escape:'htmlall':'UTF-8'}" {if isset($smallSize)} {*width="{$smallSize.width}" height="{$smallSize.height}"*} {/if} />
			<p class="s_title_block">{$product.name|escape:'htmlall':'UTF-8'}</p>
        </a>
        <p>{if $product.reference}{$product.reference|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
	</td>
	{*<td class="cart_description">*}
		{*<p class="s_title_block"><span class="line_ttl">Название:</span><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></p>*}
		{*{if isset($product.attributes) && $product.attributes}<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$product.attributes|escape:'htmlall':'UTF-8'}</a>{/if}*}
	{*</td>*}
	{*<td class="cart_ref"><span class="line_ttl">Модель:</span>{if $product.reference}{$product.reference|escape:'htmlall':'UTF-8'}{else}--{/if}</td>*}
	<td class="cart_unit">
		<span class="line_ttl">Цена:</span>
		<span class="price" id="product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
				{if isset($product.is_discounted) && $product.is_discounted}
					<span style="text-decoration:line-through;">{convertPrice price=$product.price_without_specific_price}</span><br />
				{/if}
				{if !$priceDisplay}
					{convertPrice price=$product.price_wt}
				{else}
					{convertPrice price=$product.price}
				{/if}
			{/if}
		</span>
	</td>
	<td class="cart_quantity text-center" data-title="{l s='Quantity'}">
        {if (isset($cannotModify) && $cannotModify == 1)}
			<span>
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                    {$product.customizationQuantityTotal}
                {else}
                    {$product.cart_quantity-$quantityDisplayed}
                {/if}
			</span>
        {else}
            {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
				<span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
            {/if}
            {if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}

				<input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}_hidden" />
				<input size="2" type="text" autocomplete="off" class="cart_quantity_input form-control grey" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" />
				<div class="cart_quantity_button clearfix">
					<a rel="nofollow" class="cart_quantity_up btn btn-default button-plus" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Add'}"><span><i class="icon-plus"></i></span></a>
                    {if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
						<a rel="nofollow" class="cart_quantity_down btn btn-default button-minus" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Subtract'}">
							<span><i class="icon-minus"></i></span>
						</a>
                    {else}
						<a class="cart_quantity_down btn btn-default button-minus disabled" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
							<span><i class="icon-minus"></i></span>
						</a>
                    {/if}
				</div>
            {/if}
        {/if}

        {if !isset($noDeleteButton) || !$noDeleteButton}
			<div class="cart_delete">
				{if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0) && empty($product.gift)}
					<div>
						<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html'}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
					</div>
				{/if}
			</div>
		{/if}
	</td>
	<td class="cart_total">
		<span class="price" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
				{else}
					{if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
				{/if}
			{/if}
		</span>
	</td>
	{*{if !isset($noDeleteButton) || !$noDeleteButton}
		<td class="cart_delete">
		{if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0) && empty($product.gift)}
			<div>
				<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html'}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
			</div>
		{/if}
		</td>
	{/if}*}
</tr>
