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
{if isset($products)}
	<!-- Products list -->
	<div id="product_list" class="clearfix row">
	{foreach from=$products item=product name=products}
		<div class="product_block {if $page_name == "index"}col-md-3{elseif $page_name != "index"}col-md-4{/if} col-sm-6 col-xs-12" {if $page_name == "cms"}style="width: 24%" {/if}{if isset($widthProductBlock)} style="width: {$widthProductBlock}%"{/if}>
			<div class="product-container">
				<div class="image">
					{if $product.id_label > 0}
						<span class="product-label product-label-{$product.id_label}"></span>
					{/if}
					<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image">
						<img class="img" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" />
						{if isset($product.new) && $product.new == 1}<span class="new"><span>{l s='New' mod='leomanagewidgets'}</span></span>{/if}
						<span class="product-additional" rel="{$product.id_product}"></span>
						{if $page_name == 'category' && Module::isInstalled('pwcatprodimages')}
						{foreach from=$product.images item=img}
						<img class="img catprodimg" src="{$link->getImageLink($product.link_rewrite, $img, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" />
						{/foreach}
						{/if}
					</a>
				</div>
				<div class="product-meta">
					<p class="name"><a href="{$product.link}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></p>
					<div class="description">
						<a href="{$product.link}" title="{l s='More' mod='leomanagewidgets'}">{$product.description_short|strip_tags|truncate:65:'...'}</a>
					</div>
					{if isset($content.task) && $content.task == 'special'}
						{if !$PS_CATALOG_MODE}
							{if $product.specific_prices}
								{assign var='specific_prices' value=$product.specific_prices}
								{if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
									<span class="reduction"><span>-{$specific_prices.reduction*100|floatval}%</span></span>
								{/if}
							{/if}
						{/if}
					{/if}


					{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
						<div class="content_price">
								<span class="price">
									{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
										<span class="old-price product-price">{displayWtPrice p=$product.price_without_reduction}</span>
										<span class="new-price-light">
									{/if}
									{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
									{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}</span>{/if}
								</span>
						</div>
					{/if}
				</div>
				<div class="product_bottom">
					{*<a class="quick-view h6" title="{l s='Quick View' mod='leomanagewidgets'}" href="{if $product.link|strpos:"?"}{$product.link|cat:'&content_only=1'|escape:'htmlall':'UTF-8'}{else}{$product.link|cat:'?content_only=1'|escape:'htmlall':'UTF-8'}{/if}*}
					<a class="quick-view h6 button exclusive" title="{l s='Quick View' mod='leomanagewidgets'}" href="{$product.link}">
						ПОСМОТРЕТЬ</a>
                    {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
                        {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                            {capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
							<a class="ajax_add_to_cart_button btn btn-default btn_primary{if !$product.available_for_order} disabled{/if}" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" title="{l s='Add to cart'}" rel="nofollow" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}"><div><i class="icon-basket icon-0x icon_btn icon-mar-lr2"></i><span>{l s='Add to cart'}</span></div></a>
                            {if isset($use_view_more_instead) && $use_view_more_instead==2}
								<a class="view_button btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View more'}"><div><i></i><span>{l s='View more'}</span></div></a>
                                {if !$st_display_add_to_cart}{assign var="fly_i" value=$fly_i+1}{/if}
                            {/if}
                        {/if}
                    {/if}
				</div>
			</div>
		</div>
	{/foreach}
	</div>
	<!-- /Products list -->
{/if}
