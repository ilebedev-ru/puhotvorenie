$(function(){

    var form_url = $('#pworderform').attr('action');

    $('.uipw-form_goods_modal').fancybox({
        //'autoSize': true,
        autoCenter: true,
        autoDimensions: true,
        centerOnScroll: false,
        'afterLoad': function () {
            $(".fancybox-wrap").attr('id', 'uipw-goods_modal');
        },
        'beforeLoad': function (){
            $('.uipw-form_layout').show();
            $('.uipw-form_success').hide();
        },
        'beforeShow': getCustomer(form_url)
    });

    $('body').on('click', '.uipw-form_goods_modal', function(e){
        e.preventDefault();
        var id = $(this).attr('data-pwoneclick-id');
        var name = $(this).attr('data-pwoneclick-name');
        var price = $(this).attr('data-pwoneclick-price');
        var old_price = $(this).attr('data-pwoneclick-old-price');
        var image = $(this).attr('data-pwoneclick-image');

        if($('#bigpic').length){
            image = $('#bigpic').attr('src');
        }

        $('.goods_info').show();
        $('.goods_order').show();
        $('.pleace_wait').hide();

        $('#uipw-form_goods_modal').find('input[name=id_product]').val(id);
        $('#uipw-form_goods_modal .goods_info').find('.title').html(name);
        $('#uipw-form_goods_modal').find('#bigpic').attr('src', image);
        $('#uipw-form_goods_modal').find('.current-price').html(price);
        $('#uipw-form_goods_modal').find('.discount').html(old_price);
    });

    function getCustomer(url) {
        $.ajax({
            url:url+'?getCustomer=1',
            type:'POST',
            success: function (result) {
                $.each(result, function (field, value){
                    $('#goods_' + field).val(value);
                });
            },
            dataType: 'json'
        });
    }

    $('#uipw-form_goods_modal').on('submit', '#pworderform', function(e){
        var _this = $(this);
        var button = $(this).find('input[type=submit]');
        var label = $(this).find('.pleace_wait');
        var paypal = $(this).find('.pwpaypal');

        var combination = $('body').find('input[name=id_product_attribute]').val();
        var quantity = $('body').find('input[name=qty]').val();


        /*
        var form = new FormData();

        var data = $(_this).serializeArray();
        //data.push('combination',id_product_attribute);

        $.each(data, function (i, field) {
            console.log(field);
            form.append(field.name, field.value);
        });

        console.log(form);*/


        var form = $(_this).serialize();
        form = form + '&combination=' + combination + '&quantity=' + quantity;

        $(paypal).hide();
        $(button).hide();
        $(label).show();

        $.ajax({
            url: form_url,
            type: 'POST',
            data: form,
            success: function (result) {
                var fields = $(_this).find('input');

                if (result.status == 'error') {
                    $(label).hide();
                    $(button).show();
                    $(paypal).show();

                    $.each(fields, function(k, v){
                        $(v).removeClass('error');
                    });

                    $.each(result.errors, function(name){
                        var str_error = '<div class="err">' + result.errors[name] + '</div>';
                        $(_this).find('input[name='+name+']').addClass('error');
                        $(_this).find('.'+name+'_error').html(str_error).show();
                    });
                } else {
                    $(button).show();
                    $(paypal).show();
                    $(label).hide();
                    $('.goods_info').hide();
                    $('.goods_order').hide();
                    $('.uipw-form_success').html(result.message).show();

                    $.fancybox.update();
                        console.log(result);

                    if (result.location) {
                        console.log(result.location);
                        window.location = result.location;
                    }
                }
            },
            dataType: 'json'
        });
        e.preventDefault();
    });
    
    $('select[name^="group_"]').change(function () {
		setTimeout(function(){pwupdateButtonDisplay()}, 1000);
	});

	$('.color_pick').click(function () {
		setTimeout(function(){pwupdateButtonDisplay()}, 1000);
	});

	if($('body#product').length > 0)
		setTimeout(function(){pwupdateButtonDisplay()}, 1000);
    if ($('form[target="hss_iframe"]').length == 0) {
		if ($('select[name^="group_"]').length > 0)
			pwupdateButtonDisplay();
		return false;
	}
    
    function pwupdateButtonDisplay()
    {
        $('#pwoneclick').toggle($('#add_to_cart').is(':visible'));
    }
    
});