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

{if isset($category)}
	{if $category->id AND $category->active}
		<h1 class="list-title">
			{strip}
				{$category->name|escape:'htmlall':'UTF-8'}
				{if isset($categoryNameComplement)}
					{$categoryNameComplement|escape:'htmlall':'UTF-8'}
				{/if}
			{/strip}
{*			<span class="resumecat category-product-count">
				/ {include file="$tpl_dir./category-count.tpl"}
			</span>*}
		</h1>
		
		
{*
		{if $scenes || $category->description || $category->id_image}
		<div class="content_scene_cat">
			{if $scenes}
				<!-- Scenes -->
				{include file="$tpl_dir./scenes.tpl" scenes=$scenes}
			{else}
				<!-- Category image -->
				{if $category->id_image}
				<div class="align_center">
					<img class="img-responsive" src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html'}" alt="{$category->name|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" id="categoryImage"  />
				</div>
				{/if}
			{/if}

*}
			{*{if $category->description}*}
				{*<div class="cat_desc">*}
				{*{if strlen($category->description) > 120}*}
					{*<div id="category_description_short">{$category->description|strip_tags|truncate:120}</div>*}
					{*<div id="category_description_full" style="display:none">{$category->description}</div>*}
					{*<a href="#" onclick="$('#category_description_short').hide(); $('#category_description_full').show(); $(this).hide(); return false;" class="lnk_more">{l s='More'}</a>*}
				{*{else}*}
					{*<div>{$category->description}</div>*}
				{*{/if}*}
				{*</div>*}
			{*{/if}*}
{*

		</div>
		{/if}
*}
{*		{if isset($subcategories)}
		<!-- Subcategories -->
		<div id="subcategories">
			<h2>{l s='Subcategories'}</h2>
			<div class="inline_list block row">
			{foreach from=$subcategories item=subcategory}
				<div class="category-container col-md-4 col-sm-6">
					<a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}" class="img ">
						{if $subcategory.id_image}
							<img class="img-responsive" src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'subcat_default')|escape:'html'}" alt=""  />
						{else}
							<img class="img-responsive" src="{$img_cat_dir}default-subcat_default.jpg" alt=""  />
						{/if}
					</a>
					<h3 class="s_title_block"><a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" class="cat_name ">{$subcategory.name|escape:'htmlall':'UTF-8'}</a></h3>
					{if $subcategory.description}
						<div class="cat_desc">{$subcategory.description|strip_tags:'UTF-8'|truncate:60:'...':true}</div>
					{/if}
				</div>
			{/foreach}
			</div>
		</div>
		{/if}*}

		{if $products}
			{*<div class="content_sortPagiBar">*}
				{*<div class="sortPagiBar row">*}
{*
					<div class="col-sm-4 hidden-xs hidden-sm">
						<div class="inner">
						  <div class="btn-group" id="productsview">
						  	<span>{l s='Display:'}</span>
							<a title="{l s='Grid'}" href="#" rel="view-grid"><i class="fa fa-th-large" ></i></a>
							<a title="{l s='List'}" href="#"  rel="view-list"><i class="fa fa-list-ul"></i></a>
						  </div>
						</div>
					</div>
*}
					<div class="nav-sort">
						<div>
{*
							{include file="./product-compare.tpl"}
*}
							{include file="./product-sort.tpl"}
						</div>
					</div>
				{*</div>*}
			{*</div>*}
			{include file="./product-list.tpl" products=$products}

			<div class="sortPagiBar">
				{include file="./pagination.tpl" paginationId='bottom'}
			</div>

			{if $category->description && !$smarty.get.p}
					<div id="category_description_full">{$category->description}</div>
			{/if}
		{/if}
	{elseif $category->id}
		<p class="warning">{l s='This category is currently unavailable.'}</p>
	{/if}

{/if}
