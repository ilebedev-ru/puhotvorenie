$(document).ready(function()
{
	//add 1-click buy button to products in category
	// $('.ajax_add_to_cart_button').each(function(){
		// var id = this.search.match('id_product=([0-9]*)')[1];
		// $(this).after('<a class="exclusive ajax_add_to_cart_button_fast" rel="nofollow ajax_id_product_'+id+'" href="#" title="Купить в 1 клик">Купить в 1 клик</a>');
	// });
	$('body#product .buy_fast').unbind('click').click(function(){
		var idProduct =  $('#product_page_product_id').val();
		var name_product =  $('.name-product').html();
		$.fancybox(
			'<div class="oneclickbuy" style="display:block">' + $(".oneclickbuy").html() +'</div>',
			{
				'autoDimensions'	: false,
				'width'         		: 400,
				'scroll'				: 'no',
				'height'        		: 'auto',
				'transitionIn'		: 'none',
				'transitionOut'		: 'none'
			}
		);
		$('.oneclickbuy input[name="id_product"]').val(idProduct);
		$('.oneclickbuy .name').html(name_product);
		return false;
	});
	$('.ajax_add_to_cart_button_fast').unbind('click').click(function(){
		var idProduct =  $(this).attr('rel').replace('nofollow ajax_id_product_', '');
		var name_product =  $(this).parent().parent().find('.hd a').html();
		$.fancybox(
			'<div class="oneclickbuy" style="display:block">' + $(".oneclickbuy").html() +'</div>',
			{
				'autoDimensions'	: false,
				'width'         		: 400,
				'scroll'				: 'no',
				'height'        		: 'auto',
				'transitionIn'		: 'none',
				'transitionOut'		: 'none'
			}
		);
		$('.oneclickbuy input[name="id_product"]').val(idProduct);
		$('.oneclickbuy .name').html(name_product);
		return false;
	});

});

$(function(){
	$(".pwexpressorder form").submit(function(){
		var okay = true;
		$(".pwexpressorder form input.is_required").each(function (i) {
			if($(this).val() == "0" || !$(this).val() || ($(this).attr('type') == 'checkbox' && !$(this).is(':checked'))){
				okay = false;
				$(this).css('border', '1px solid red');
                console.log($(this));
			}

		});
		if(!okay){
			alert('Заполните все необходимые поля');
			return false;
		}
        $('#submitPwExpressOrder').hide();
        $('body').append('<div id="loader"></div>');

    });

    $('#carrier-select td').on('click', function () {
        $(this).parent().find('input[type="radio"]').prop('checked', true);
        $(this).parent().find('input[type="radio"]').change();
        if (typeof $.uniform != "undefined") $.uniform.update();
    });

    adr1_tmp = $('input[name=address1]').attr('value');
    $('.radio-select input[type=radio]').on('change', function () {
        adr1 = $('input[name=address1]').attr('value') != 'Самовывоз' ? $('input[name=address1]').attr('value') : adr1_tmp;
       	if ($(this).val() == 6) {
            adr1_tmp = $('input[name=address1]').attr('value');
            $('label[for=address]').hide();
            $('input[name=address1]').hide().attr('value', 'Самовывоз');
        } else {
            $('label[for=address]').show();
            $('input[name=address1]').show().attr('placeholder', 'Адрес доставки').attr('value', adr1);
		}
    });

    /*$('.pwexpressorder .carrier-select .deliv-type .radio-select, .pwexpressorder .carrier-select .deliv-type .name').on('click', function () {
        $('.pwexpressorder .carrier-select .deliv-type').removeClass('brdr');
		$(this).parent().addClass('brdr');
    });*/

    $('.pwexpressorder .carrier-select .deliv-type').mousedown(function () {
        $('.pwexpressorder .carrier-select .deliv-type').removeClass('brdr');
		$(this).addClass('brdr');
        $(this).find('input[name=id_carrier]').trigger('click');
        if($(this).find('input[name=id_carrier]').val() == 48) {
          $('label[for=address]').hide();
          $('input[name=address1]').hide();
        } else {
          $('label[for=address]').show();
          $('input[name=address1]').show();
        }
     });
});

// $(document).on('ready', function () {
//     $('#')
// });