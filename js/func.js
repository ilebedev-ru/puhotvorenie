$(document).ready(function(){   
    jQuery('input[placeholder], textarea[placeholder]').placeholder();

  	$('.catalog .item:nth-child(4n)').addClass('item-last').after('<div class="clear"></div>');
  	$('.comments .item:nth-child(2n)').after('<div class="clear"></div>');
  	$('.articles .item:nth-child(2n)').after('<div class="clear"></div>');
	
	$('.fancybox').fancybox();
 });