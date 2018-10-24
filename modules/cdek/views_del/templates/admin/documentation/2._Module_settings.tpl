{*
* 2007-2016 PrestaShop
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
* @author    SeoSA <885588@bk.ru>
*  @copyright 2012-2017 SeoSA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h2 class="text-center">{l s='Module settings' mod='cdek'}</h2>
<hr>

{l s='Choose a weight unit that indicates the weight of products.' mod='cdek'}<br>
{get_image_lang path = '2.jpg'}
{l s='You must choose what tariff will be used in your store.' mod='cdek'}<br>
<strong>{l s='It is important to always leave just the right tariffs (mostly 2-3)' mod='cdek'}</strong><br>
{l s='Firstly the user will only see the actual working methods of delivery.' mod='cdek'}<br>
{l s='Secondly: the performance will be much faster, due to the smaller number of calculations.' mod='cdek'}<br>
{l s='To do this, open: ' mod='cdek'} <strong>{l s='Shipping > Carriers.' mod='cdek'}</strong> {l s='Include the necessary delivery methods from the CDEK.' mod='cdek'}<br>
{get_image_lang path = '3.jpg'}<br>
{l s='Similar settings you can do on the page' mod='cdek'} <strong>{l s='Delivery> CDEK delivery option:' mod='cdek'}</strong><br>
{get_image_lang path = '4.jpg'}<br>


{l s='The field ' mod='cdek'} <strong>{l s='"Send order after create"' mod='cdek'}</strong><br>
{l s='If select ' mod='cdek'} <strong>{l s='"Yes",' mod='cdek'}</strong> {l s='then after the creation of order by user, it immediately goes to the CDEK.' mod='cdek'}<br>

{l s='If select ' mod='cdek'} <strong>{l s='"No",' mod='cdek'}</strong> {l s='the order will be sent to the CDEK when a certain status is selected, which you will configure below.' mod='cdek'}<br>


{l s='Field ' mod='cdek'} <strong>{l s='"Select order state, when order send in CDEK"' mod='cdek'}</strong><br>
{l s='Specify the status, when selecting which, the order will be sent to the CDEK.' mod='cdek'}
{get_image_lang path = '5.jpg'}<br>
{l s='Adjusting order sizes' mod='cdek'}<br>

{l s='Fill out values for' mod='cdek'} <strong>{l s='Width, Height, Length, Weight' mod='cdek'}</strong> {l s='by default.' mod='cdek'}<br>
{l s='These parameters will be apply to products that do not have size.' mod='cdek'}<br>
{get_image_lang path = '6.jpg'}<br>
{l s='You can adjust the default sizes by category. For products with unfilled sizes, the parameters are applied depending by category.' mod='cdek'}<br>
{get_image_lang path = '7.jpg'}<br>

{l s='Priority for determining the size is in the following sequence: the products, by category, by default.' mod='cdek'}<br>
{l s='At first, the dimensions in the product card are checked, if there is not filled in, then it checks the sizes indicated in the category, if there is not filled, it checks by default size.' mod='cdek'}<br>

{l s='You can enter additional commission for carriers' mod='cdek'}<br>
{get_image_lang path = '8.jpg'}<br>





