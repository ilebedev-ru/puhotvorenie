{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Feedbacks' mod='pwfeedback'}{/capture}
{*{include file="$tpl_dir./breadcrumb.tpl"}*}
{if $pwconfig.PWFEEDBACK_DESIGN == 2}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.pwfeedback .list-div').bxSlider({
                mode: 'fade',
                controls: false
            });
        })
    </script>
{/if}
<div class="pwfeedback comments row">
	{if $feedbacks|@count>0}
		<h1>{l s='Our customer feedbacks' mod='pwfeedback'}</h1>
		<ul class="list-div clearfix nuclear">
			{foreach from=$feedbacks item=feedback}
				{include file="{$this_path_tpl}" feedback=$feedback answerConnect=true}
			{/foreach}
		</ul>
	{else}
		<p class="warning">{l s='No feedbacks available.' mod='pwfeedback'}</p>
	{/if}
	<div class="more nuclear">
		<a class="sign" href="#">{l s='Put feedback' mod='pwfeedback'}</a>
	</div>
    <div class="sign_pwfeedback">
        {include file=$addform}
    </div>
</div>
