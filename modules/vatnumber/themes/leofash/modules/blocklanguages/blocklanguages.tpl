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

<!-- Block languages module -->
{if count($languages) > 1}
<div id="languages_block_top" class="dropdown pull-left">
	{* @todo fix display current languages, removing the first foreach loop *}
	{foreach from=$languages key=k item=language name="languages"}
		{if $language.iso_code == $lang_iso}
			<p class="dropdown-toggle" data-toggle="dropdown">
				<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />&nbsp;<span>{$language.name}</span>
				<span class="caret"></span>
			</p>
		{/if}
	{/foreach}
		<ul id="first-languages" class="dropdown-menu">
		{foreach from=$languages key=k item=language name="languages"}
			{if $language.iso_code != $lang_iso}
				<li>
					{if $language.iso_code != $lang_iso}
						{assign var=indice_lang value=$language.id_lang}
						{if isset($lang_rewrite_urls.$indice_lang)}
							<a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
						{else}
							<a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">

						{/if}
					{/if}
							<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />&nbsp;<span>{$language.name}</span>
						</a>
				</li>
			{/if}
		{/foreach}
		</ul>
</div>

{/if}
<!-- /Block languages module -->
