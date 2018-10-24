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
<div class="pwfeedback comments block row">
    <div class="block_content box-line h3">
        <div>
            <ul id="productTabs-6" class="nav nav-tabs">
                <li class="active"><a>Отзывы</a></li>
            </ul>
        </div>
    </div>
    {*<div class="title_block">{l s='Customers feedbacks' mod='pwfeedback'}</div>*}
    <div class="comment-blocks-switcher"><i class="fa fa-angle-left prev-comment-block" aria-hidden="true"></i>&nbsp;&nbsp;<span class="current"></span><span>/</span><span class="all-length"></span>&nbsp;&nbsp;<i class="fa fa-angle-right next-comment-block" aria-hidden="true"></i></div>
    {if count($feedbacks)}
        <div class="list-div clearfix nuclear">
            {foreach from=$feedbacks item=feedback name=feedbacks}
                {if $smarty.foreach.feedbacks.iteration%2==1}
                    <div class="comment-blocks disabled-div">
                {/if}
                {include file="{$this_path}pwfeedback-item.tpl" feedback=$feedback}
                {if $smarty.foreach.feedbacks.iteration%2==0 || $smarty.foreach.feedbacks.last}
                    </div>
                {/if}
            {/foreach}
        </div>
    {else}
        <p class="warning">{l s='No feedbacks available.' mod='pwfeedback'}</p>
    {/if}
    <div class="more nuclear">
        <a class="view" href="{$link->getModuleLink('pwfeedback', 'view')|escape:'htmlall':'UTF-8'}">Посмотреть все
            отзывы</a><i class="fa fa-circle" aria-hidden="true"></i><a class="sign" href="#">Опубликовать отзыв</a>
    </div>
    <div class="sign_pwfeedback">
        {include file="{$this_path}addform.tpl"}
    </div>
</div>
