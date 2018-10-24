{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}(specific_currency ? {$product->specificPrice.reduction} : {$product->specificPrice.reduction} * currencyRate){else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};
var ipa_default = {if isset($ipa_default)}{$ipa_default}{else}0{/if};

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
	{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
	customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
	{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
		combinationImages[{$combinationId}] = new Array();
		{foreach from=$combination item='image' name='f_combinationImage'}
			combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
		{/foreach}
	{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
	{foreach from=$images item='image' name='f_defaultImages'}
		combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
	{/foreach}
{/if}

// Translations
var doesntExist = '{l s='The product does not exist in this model. Please choose another one' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please wait...' js=1}';
var fieldRequired = '{l s='Please fill in all required fields, then save your customization.' js=1}';

{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity});
	{/foreach}
	// Colors
	{if $colors|@count > 0}
		{if $product->id_color_default}var id_color_default = {$product->id_color_default|intval};{/if}
	{/if}
{/if}
//]]>
</script>

{include file="$tpl_dir./breadcrumb.tpl"}
{if isset($adminActionDisplay) && $adminActionDisplay}
	<div id="admin-action">
		<p>{l s='This product is not visible to your customers.'}
		<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
		<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0)"/>
		<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1)"/>
		</p>
		<div class="clear" ></div>
		<p id="admin-action-result"></p>
		</p>
	</div>
	{/if}

	{if isset($confirmation) && $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
	{/if}
<div class="goods-block nuclear" itemscope itemtype="http://schema.org/Product">
<img class="light_banner" src="/img/lt.png" alt="легкое и теплое">
 <h1 class="name-product"><span itemprop="name">{$product->name|escape:'htmlall':'UTF-8'}</span></h1>
  <div class="view-part">
	<div class="pig-pic">
		<img id="bigpic" itemprop="image" src="{if $have_image}{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large')}{else}{$img_prod_dir}{$lang_iso}-default-large.jpg{/if}"{if $jqZoomEnabled && $have_image} class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, '')}"{else} alt="{$cover.legend|escape:'htmlall':'UTF-8'}"{/if} title="{$cover.legend|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}"/>
		{if $product->on_sale}<div class="hit">&nbsp;</div>{/if}
	</div>
	{if count($images)>0}
		<div class="thumbs nuclear{if count($images)==1} hidden{/if}">
		{if isset($images)}
			{foreach from=$images item=image name=thumbnails}
				{assign var=imageIds value="`$product->id`-`$image.id_image`"}
				<a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox')}" rel="other-views" class="thickbox {if (isset($image.cover) AND $image.cover == 1) OR (!isset($image.cover) AND $smarty.foreach.thumbnails.first)}shown{/if}" title="{$image.legend|htmlspecialchars}">
					<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium')}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" />
				</a>
			{/foreach}
		{/if}
		</div>
	{/if}
	<div class="socially"><noindex>{literal}
		<script type="text/javascript">(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
    var h=d[g]('body')[0];
    h.appendChild(s);
  }})();</script>
<div class="pluso" data-background="transparent" data-options="medium,square,line,horizontal,counter,theme=01" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir"></div>
</noindex>{/literal}
	</div>
  </div>  
  <!-- add to cart form-->
	<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart.php', true)}" method="post">

			<!-- hidden datas -->
	<p class="hidden">
		<input type="hidden" name="token" value="{$static_token}" />
		<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
		<input type="hidden" name="add" value="1" />
		<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
	</p>

			<!-- prices -->
  <div class="descript-block">
	<div class="dess-goo" itemprop="description">
		{*<span class="prod_text_size">Доступность: {if $product->quantity > 0}<span style="color:#88b334; font-weight:bold">Есть{else}<span style="color:#b33434; font-weight:bold">Нет{/if} в наличии</span></span>*}<span style="background: green; color: #fff">Есть в наличии</span>
		{if $product->width && $product->height}
			<div class="sizes">
				<label for="">Размеры:</label>
				<div class="width">{$product->width} см</div>
				<div class="height">{$product->height} см</div>
			</div>
		{/if}
		{if $product->reference}<div class="reference t-row">Артикул: <span>{$product->reference}</span></div>{/if}
		{if $product->description_short}
			{$product->description_short}
		{/if}
	<div>
		
	<div class="top nuclear{if (!$allow_oosp && $product->quantity <= 0)} warning{/if}">
	{if !$priceDisplay || $priceDisplay == 2}
		{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
		{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
	{elseif $priceDisplay == 1}
		{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
		{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
	{/if}
	{if $productPrice < $productPriceWithoutRedution}
		<span class="prise-old">{convertPrice price=$productPriceWithoutRedution}</span>
	{/if}
	<div class="nuclear"></div>
		<div class="prise" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			{$rightPrice = {convertPrice price=$productPrice}}
			{if $priceDisplay >= 0 && $priceDisplay <= 2}
				{if $currencyFormat == 1 || $currencyFormat == 3}
					<meta itemprop="priceCurrency" content="{$currency->iso_code}" /> {$currencySign} <span id="our_price_display" itemprop="price">{$rightPrice|regex_replace:"/[\\{$currencySign}]/":""}</span>
				{else}
					<span id="our_price_display" itemprop="price">{$rightPrice|regex_replace:"/[\\{$currencySign}]/":""}</span> <meta itemprop="priceCurrency" content="{$currency->iso_code}" /> {$currencySign}
				{/if}  
			{/if}
		</div>
	  <p{if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE} style="display: none;"{/if} id="add_to_cart" class="buttons_bottom_block"><input type="submit" value="Купить" class="buy" /></p>
		{if (!$allow_oosp && $product->quantity <= 0)}<span class="empty-stock">Товара нет в наличии</span>{/if}
	</div>	
	<input type="submit" value="Купить в 1 клик" class="button2 buy_fast">
  </div>
  </form>
  {*
  <script type="text/javascript" src="/js/banner.js"></script>
	<div class="banner-box">
      <div id="mask"></div>
      <div class="banner">
        <div class="banner-in">
          <div class="buble">
            <span class="hdr">Мы дарим вам скидку.</span>
            <p>Мы раздаем подарки самым решительным!<br />Купон на скидку <b>202</b> рубля!</p>
          </div>
          <div class="code new">Ваш код <b>MEGASKIDKA</b> <input type="submit" value="Активировать" class="sbm" /></div>
		 
          <form method="post" id="coupon_form" class="subscribe">
			<input type="hidden" value="MEGASKIDKA" name="activate" class="email" />
			<input type="hidden" value="MEGASKIDKA" name="coupon" class="email" />
            <input type="text" value="" name="email" class="email" />
          </form>
		  <p>Или просто позвоните и скажите "<b>МЕГАСКИДКА!</b>"</p>
        </div>
      </div>
    </div> 
	*}
</div>
</div>
</div>
<div class="socially-block">
    <div id="vk_comments"></div>
	<script type="text/javascript">
	{literal}VK.Widgets.Comments("vk_comments", {limit: 10, width: "700", attach: "*"});{/literal}
	</script>
</div>    
   
{if $product->description}
	<div id="description" class="rte">{$product->description}</div>
{/if}

{$HOOK_PRODUCT_FOOTER}

{/if}

