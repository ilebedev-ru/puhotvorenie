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
{l s='To do this, check boxes necessary delivery methods' mod='cdek'}<br>
{get_image_lang path = '3.jpg'}<br>
<strong>{l s='Field "Write logo"' mod='cdek'}</strong><br>
{l s='If select "YES", then at the bottom of page there will be block with CDEK logs ' mod='cdek'}<br>
{get_image_lang path = '4.jpg'}<br>
<strong>{l s='Adjusting order sizes' mod='cdek'}</strong><br>
{l s='Fill out values for Width, Height, Length, and Weight by default.' mod='cdek'}<br>
{l s='These parameters will be apply to products that do not have size.' mod='cdek'}<br>
{get_image_lang path = '5.jpg'}<br>

{l s='You can adjust default sizes by category. For products with unfilled sizes, parameters will be apply depending by category.' mod='cdek'}<br>
{get_image_lang path = '6.jpg'}<br>
{l s='Priority for determining the size is in the following sequence: the products, by category, by default.' mod='cdek'}<br>
{l s='At first, the dimensions in the product card are checked, if there is not filled in, then it checks the sizes indicated in the category, if there is not filled, it checks by default size.' mod='cdek'}<br>

{l s='You can enter additional commission for carriers' mod='cdek'}><br>
{get_image_lang path = '7.jpg'}<br>
{l s='You can make delivery methods free for customers. For this, check boxes required methods.' mod='cdek'}<br>
{get_image_lang path = '8.jpg'}<br>

<strong>{l s='Configuring order statuses' mod='cdek'}></strong><br>
<strong>{l s='Send order to CDEK' mod='cdek'}</strong><br>
{l s='Specify status, selecting which, the order will be send to CDEK. You can select several statuses' mod='cdek'}<br>
{get_image_lang path = '9.jpg'}<br>

<strong>{l s='Cancel order in CDEK' mod='cdek'}</strong><br>
{l s='Specify the status of order, selected that will be send message to the CDEK that the order has been cancel. You can select several statuses.' mod='cdek'}<br>
{get_image_lang path = '10.jpg'}<br>

<strong>{l s='"Paid shipping" and "Paid full"' mod='cdek'}</strong><br>
{l s='Mark the statuses for online payments when the customer pays order before receiving it. Mark the statuses for which t user has already paid for delivery and / or order. In this case, a zero value will be transfer to the invoice, because customer has already paid. If the customer has fully paid for the order, tick the "Delivery paid" and for "Order paid"' mod='cdek'}<br>

{l s='You can mark only those statuses that are marked for the column "Send to CDEK"' mod='cdek'}<br>
{get_image_lang path = '11.jpg'}<br>

<strong>{l s='Block "Logs of requests to CDEK delivery service"' mod='cdek'}</strong><br>
{l s='It displays the logs of requests and responses of the CDEK. If you have an error using the delivery from CDEK, you can track this. You can copy "request", "answer" fields and send to the SDEC to solve problem.' mod='cdek'}<br>
{l s='You can use the filter by date to search' mod='cdek'}<br>
{get_image_lang path = '12.jpg'}<br>