<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}">
	<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{/if}
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,follow" />
		<meta name="format-detection" content="telephone=no" />
        {if $force_ssl}
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
        {/if}
		<link href='//fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&amp;subset=latin,cyrillic-ext' rel='stylesheet' type='text/css' />
		<link href='//fonts.googleapis.com/css?family=Lobster&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$img_ps_dir}favicon.ico?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$img_ps_dir}favicon.ico?{$img_update_time}" />
		<script type="text/javascript">
			var baseDir = '{$content_dir}';
			var static_token = '{$static_token}';
			var token = '{$token}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
{if isset($js_files)}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri}"></script>
	{/foreach}
{/if}
<script type="text/javascript" src="{$theme}js/openstat_decode.js"></script>
<script type="text/javascript" src="//vk.com/js/api/openapi.js?98"></script>
<script type="text/javascript">
  VK.init({
    apiId: 3820706,
    onlyWidgets: true
  });
  </script>
		{$HOOK_HEADER}
		
	{literal}
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-58683427-1', 'auto');
	  ga('send', 'pageview');
	</script>
	{/literal}	
		
	</head>
	
	<body {if $page_name}id="{if $page_name == '404'}p{/if}{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="{if $wide}order_page{/if}{if $content_only} content_only{/if}">
{if !$content_only}
<div style="display:none">
	<div class="oneclickbuy">
	  <span class="hd">Купить в 1 клик: <span class="name">товар</span></span>
	  <div class="text">Внимание!<br />
	  Покупая в один клик, Вы отправляете заявку на один товар.
	  Если вы хотите купить несколько товаров, пожалуйста, положите их в <a href="/order">корзину</a>.</div>
	  <div class="form">
		<form action="/modules/expressorder/makeorder.php" method="POST" class="std">
		  <input type="hidden" name="id_product" value="0">
		  <p class="text required">
			<label>Ваше имя</label>
			<input type="text" name="firstname" value="{if $customer->firstname}{$customer->firstname}{/if}">
			<sup>*</sup>
		  </p>
		  <p class="text required">
			<label>Ваш телефон</label>
			<input type="text" name="phone" value="{if $phone}{$phone}{else}+7{/if}">
			<sup>*</sup>
		  </p>
		  <p class="text">
			<label>&nbsp;</label>
			<input type="submit" name="submitAccount" class="button2" value="Оформить">
		  </p>
		</form>
	  </div>
	</div>	
</div>
<div class="header">
    <div class="hed-top">
    	<div class="page nuclear">
			<div class="logo">
				<a id="header_logo" href="{$link->getPageLink('index.php')}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
						<img src="{$img_ps_dir}logo.jpg?{$img_update_time}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" />
				</a>
				<span class="thebest">
					{if $config.info}{$config.info}{/if}
				</span>
				<div class="deliv">
					<img src="/img/l/6.jpg" alt="Доставка по всей России">Доставка по всей России
				</div>
			</div>
			<div class="utp">
				<ul>
					<li class="li-1">Оплата при получении</li>
					<li class="li-2">Натуральный козий пух</li>
					<li class="li-3">Гарантия качества</li>
				</ul>
			</div>
			<div class="middle">
				<div class="phone">
					<div class="row nuclear">
						{if $config.phone}<span class="number">{$config.phone}</span>{/if}
						{include file="$tpl_dir./backcall.tpl"}
					</div>
					<div class="row nuclear">
						{if $config.phone2}<span class="number">{$config.phone2}</span>{/if}
						{include file="$tpl_dir./backquest.tpl"}
					</div>
					<div class="row nuclear last">
						<a href="{$link->getPageLink('contact-form.php')}" class="question">Задать вопрос</a>
						<a href="{$link->getPageLink('contact-form.php')}" class="call-me">Заказать звонок</a>
					</div>
				</div>
			</div>
			
    		<a class="cart {if $cart_qties > 0} cart_with_products{/if}" href="/order">
    		{*<a class="cart" href="/order">*}
    			<span class="hd">Корзина:</span>
				<span class="txt ajax_empty{if $cart_qties > 0} hidden{/if}">товары пока не добавлены</span>
    			<span class="txt ajax_fully{if $cart_qties == 0} hidden{/if}" {if $cart_qties != 0} style="display: inline-block;"{/if}><span class="link ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span> шт.</span>
    			<span class="txt ajax_fully{if $cart_qties == 0} hidden{/if}" {if $cart_qties != 0} style="display: inline-block;"{/if}>на сумму: <span class="link ajax_cart_total{if $cart_qties == 0} hidden{/if}">{if $cart_qties > 0}
				{if $priceDisplay == 1}
					{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
					{convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
				{else}
					{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
					{convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
				{/if}
			{else}
				{convertPrice price=0}
			{/if}</span></span>
			<span class="exclusive ajax_fully{if $cart_qties == 0} hidden{/if}">Оформить заказ →</span>
    		</a>
    	</div>
    </div>
    <div class="hed-btm">
    	<div class="page nuclear">
    		<ul class="hed-mnu">
    			<li><a href="/content/2-o-nas">О нас</a></li>
    			{*<li><a href="/1-home">Каталог</a></li>*}
    			<li><a href="{$link->getPageLink('prices-drop.php')}">Спецпредложения</a></li>
    			<li><a href="/content/5-dostavka-i-oplata">Доставка и оплата</a></li>
    			<li><a href="/content/6-uhod-za-izdelijami">Уход за изделиями</a></li>
    			<li><a href="/content/7-sotrudnichestvo">Сотрудничество</a></li>
				<li><a href="{$link->getPageLink('opt.php')}">Оптом</a></li>
    			<li><a href="/content/4-kontakty">Контакты</a></li>
    		</ul>
    		<div class="search">
    			<form method="get" action="{$link->getPageLink('search.php')}" id="searchbox">
    				<input type="text" placeholder="Поиск по сайту" class="search_query inp" type="text" id="search_query_top" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
    				<input type="submit" value="" name="submit_search" class="but" />
    			</form>
    		</div>
    	</div>
    </div>
    {*<div class="christmas" style="margin: 10px 0 0;">Распродажа! Всем на все 20%!</div>*}
  </div>      
  <!--/ header -->
  <div class="cnt-wrp">
  	<div class="page nuclear{if $wide} wide{/if}"> 
	{$HOOK_TOP}	
		{*{if $page_name == "index"}<div class="christmas" style="margin-top: 10px;">
			<img alt="Новогодняя акция" src="/img/bell.png">
				<span style="font-weight:bold">Акция!</span> До нового года доставка по СПб бесплатно, по России экспресс-доставка курьером по предоплате!
			<img alt="Новогодняя акция" src="/img/bell.png">
		</div>{/if}*}
	<!-- sidebar -->
		{if !$wide}
  		<div class="sidebar" id="left_column">
  			{$HOOK_LEFT_COLUMN}
  		</div>
		{/if}
  		<!--/ sidebar -->
  		<!-- content -->
  		<div class="content" id="center_column">  
{/if}
