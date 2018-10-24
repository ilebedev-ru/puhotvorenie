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

{include file="$tpl_dir./errors.tpl"}

{if !isset($errors) OR !sizeof($errors)}
	<h1>{l s='List of products by supplier:'}&nbsp;{$supplier->name|escape:'htmlall':'UTF-8'}</h1>
	{if !empty($supplier->description)}
		<div class="description_box">
			<p>{$supplier->description}</p>
		</div>
	{/if}

	{if $products}
		<div class="content_sortPagiBar">
			<div class="sortPagiBar row">
				<div class="col-xs-4 hidden-phone">
{*					<div class="inner">
					  <div class="btn-group" id="productsview">
						<span>{l s='Display:'}</span>
							<a title="{l s='Grid'}" href="#" rel="view-grid"><i class="fa fa-th-large" ></i></a>
							<a title="{l s='List'}" href="#"  rel="view-list"><i class="fa fa-list-ul"></i></a>
					  </div>
					</div>*}
				</div>
				<div class="col-xs-8">
					<div class="inner ">
						{include file="./product-compare.tpl"}
						{include file="./product-sort.tpl"}
					</div>
				</div>
			</div>
		</div>
		{include file="./product-list.tpl" products=$products}
		
		<div class="sortPagiBar">  
					
				{include file="./pagination.tpl" paginationId='bottom'}
					
			</div>
	{else}
	<p class="warning">{l s='No products for this supplier.'}</p>
	{/if} 
{/if}
