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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"> <![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}">
	<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
                {if $LEO_RESPONSIVE}
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
                {/if}    
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{/if}
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta http-equiv="content-language" content="{$meta_language}" />
		<meta name="cmsmagazine" content="062e98078d95c97815d6bf2cb1b8975a" />
		<meta name="ktoprodvinul" content="17ac3fdcdfbfdbe9" />
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<link rel="icon" type="image/x-icon" href="{$favicon_url}" />
		<link rel="shortcut icon" href=​"{$favicon_url}" type="image/x-icon">
		<script type="text/javascript">
			var baseDir = '{$content_dir|addslashes}';
			var baseUri = '{$base_uri|addslashes}';
			var static_token = '{$static_token|addslashes}';
			var token = '{$token|addslashes}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>
                <link rel="stylesheet" type="text/css" href="{$BOOTSTRAP_CSS_URI}"/> 
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
		{hook h="displayNav"}
{if $LEO_SKIN_DEFAULT &&  $LEO_SKIN_DEFAULT !="default"}
	<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$LEO_THEMENAME}/skins/{$LEO_SKIN_DEFAULT}/css/skin.css"/>
{/if}
	<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$LEO_THEMENAME}/css/theme-responsive.css"/>


{if isset($js_files)}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri}"></script>
	{/foreach}
{/if}
{$LEO_CUSTOMWIDTH}
{if !$LEO_CUSTOMFONT}
	<link href='//fonts.googleapis.com/css?family=Noticia+Text:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Lily+Script+One' rel='stylesheet' type='text/css'>
{/if}
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$LEO_THEMENAME}/css/font-awesome.min.css"/>
<!-- @pw удалил <script defer type="text/javascript" src="{$js_uri}/jquery-ui-1.12.0.custom.min.js"></script> -->
<script defer type="text/javascript" src="{$content_dir}themes/{$LEO_THEMENAME}/js/jquery.ui.touch-punch.min.js"></script>
<script defer type="text/javascript" src="{$content_dir}themes/{$LEO_THEMENAME}/js/custom.js"></script>
<script type="text/javascript" src="{$content_dir}themes/{$LEO_THEMENAME}/js/jquery.cookie.js"></script>
{if $hide_left_column||in_array($page_name,array('checkout','index','order','address','my-account'))}{$HOOK_LEFT_COLUMN=null}{/if}
{if $hide_right_column|| in_array($page_name,array('checkout','index','order','address','addresses','authentication'))}{$HOOK_RIGHT_COLUMN=null}{/if}


<!--[if lt IE 9]>
<script src="{$content_dir}themes/{$LEO_THEMENAME}/js/html5.js"></script>
<script src="{$content_dir}themes/{$LEO_THEMENAME}/js/respond.min.js"></script>
<![endif]-->
	{$HOOK_HEADER}	
	</head>

	<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="{$LEO_BGPATTERN} fs{$FONT_SIZE} {if isset($page_name)}{$page_name|escape:'htmlall':'UTF-8'}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if $content_only} content_only{/if}">
	{*<p style="display: block;" id="back-top"> <a href="#top"><span></span></a> </p>*}
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}
		<section id="page" class="clearfix leo-wrapper">
			
			<!-- Header -->
			<header id="header" class="block clearfix">
				<section id="topbar">
					<div class="container"> 
						<div class="top-wrap">
							{hook h='displayTop'}
						</div>
						<div class="top_half"><div class="regions-select">{hook h='regionSelect' mod='pwblockusercity'}</div></div>
						<div class="search_button"><i class="fa fa-search" aria-hidden="true"></i></div>
					</div>
				</section>
				<section id="header-main">
					<div class="container" >
						<div id="header_right">
							{hook h='displayHeaderLeft'}
						</div>
						<div class="regions-select">{hook h='regionSelect' mod='pwblockusercity'}</div>
						<div class="header-logo"> 
								<a id="header_logo" href="https://puhotvorenie.ru" title="{$shop_name|escape:'htmlall':'UTF-8'}">
									<img class="logo" src="{$logo_url}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" />
									<p style="color: #572664; text-align: right;">Самый большой выбор пуховых платков</p>
								</a> 
						</div>
						<div id="header_right">							
							{hook h='displayHeaderRight'}
						</div>
					</div>
				</section>

				{if !empty($HOOK_TOPNAVIGATION) }
				<section id="leo-mainnav" class="clearfix">
					<div class="container"> 
						{$HOOK_TOPNAVIGATION}
					</div>
				</section>
				{/if}
			</header>		
			{if !in_array($page_name,array('index'))}					
				<section id="breadcrumb" class="clearfix">
					<div class="container"> 
						{include file="$tpl_dir./breadcrumb.tpl"} 
					</div>
				</section>					
			{/if}
			<section id="columns" class="clearfix">
				<div class="container">
					<div class="row">
						{include file="$tpl_dir./layout/{$LEO_LAYOUT_DIRECTION}/header.tpl" hide_left_column=$hide_left_column hide_right_column=$hide_right_column }
	{/if}
