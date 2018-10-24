/*
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
*/

$('document').ready(function(){
	reloadProductComparison();
});

reloadProductComparison = function() {
	$('a.cmp_remove').click(function(){

		var idProduct = $(this).attr('rel').replace('ajax_id_product_', '');
		
		var html = '<div id="page_notification" class="notification alert alert-success" style="display:none"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="not-content"></div><div>';
		if (!$("#page_notification").length) $("body").append( html );
		
		$.ajax({
  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
 			async: false,
 			cache: false,
  			success: function(){
				return true;
			}
		});	
	});

	$('input:checkbox.comparator').click(function(){
	
		var idProduct = $(this).attr('value').replace('comparator_item_', '');
		var checkbox = $(this);
		//add message for add to compare
		var html = '<div id="page_notification" class="notification alert alert-success" style="display:none"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="not-content"></div><div>';
		if (!$("#page_notification").length) $("body").append( html );
		
		if(checkbox.is(':checked'))
{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=add&id_product=' + idProduct,
	 			async: true,
	 			cache: false,
	  			success: function(data){
	  				if (data === '0')
	  				{
	  					checkbox.attr('checked', false);
						$("#page_notification .not-content").html(max_item);
					}else{
						$("#page_notification .not-content").html(add_compare);
					}
					
					$("#page_notification").show().delay(2000).fadeOut(600);
	  			},
	    		error: function(){
	    			checkbox.attr('checked', false);
	    		}
			});	
		}
		else
		{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
	 			async: true,
	 			cache: false,
	  			success: function(data){
					if (data === '0'){
						checkbox.attr('checked', true);
						$("#page_notification .not-content").html(err_remove_compare);
	  				}
					else{
						$("#page_notification .not-content").html(remove_compare);
					}
					
					$("#page_notification").show().delay(2000).fadeOut(600);
	    		},
	    		error: function(){
	    			checkbox.attr('checked', true);
	    		}
			});	
		}
	});

	//code for click on tag
	$('a.comparator').click(function(){
	
		var idProduct = $(this).attr('value').replace('comparator_item_', '');
		var html = '<div id="page_notification" class="notification alert alert-success" style="display:none"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="not-content"></div><div>';
		if (!$("#page_notification").length) $("body").append( html );
		if($(this).find("i").hasClass("icon-check-empty")){
			
			$.ajax({
					url: 'index.php?controller=products-comparison&ajax=1&action=add&id_product=' + idProduct,
					async: true,
					cache: false,
					success: function(data){
						
						if (data === '0')
						{
							$("#page_notification .not-content").html(max_item);
						}
						else{
							$("#page_notification .not-content").html(add_compare);
							$("#comparator_item_"+idProduct).find("i").removeClass("icon-check-empty");
							$("#comparator_item_"+idProduct).find("i").addClass("icon-check");
						}
						
						$("#page_notification").show().delay(2000).fadeOut(600);
					},
				error: function(){ 
				}
				});
		}else{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
	 			async: true,
	 			cache: false,
	  			success: function(data){
	  				if (data === '0'){
						$("#page_notification .not-content").html(err_remove_compare);
	  				}
					else{
						$("#page_notification .not-content").html(remove_compare);
						$("#comparator_item_"+idProduct).find("i").removeClass("icon-check");
						$("#comparator_item_"+idProduct).find("i").addClass("icon-check-empty");
						
					}
					
					$("#page_notification").show().delay(2000).fadeOut(600);
	    		},
	    		error: function(){
	    			//checkbox.attr('checked', true);
	    		}
			});
		}
			
			 
		});	
}