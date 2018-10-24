$(document).ready(
	function()
	{
		var checkagree=function()
		{
			var agree;
			if(agree=document.getElementById('cgb'))
			{
				agree=document.getElementById('cgb');
			
				$('#columns button[type=submit], #oneclickadvanced button[type=submit]').attr('disabled',!agree.checked)
			}
		}
		checkagree();
		$('#cgb').change(checkagree);
		
    $(document).on('submit', '.advancedForm', function(e){
        e.preventDefault();
        var btnSubmit = $(this).find('[type=submit]');
        var data = $(this).serializeArray();
        var frm = $(this);
        $('.form_info.form_error').remove();

        if (frm.find('#cgv').length) {

            if (!frm.find('#cgv:checked').length) {
                $('#checkoutadv .panel-body').after('<div class="form_info form_error">Вы должны дать согласие на обработку персональных данных</div>');
                $('html, body').animate({
                    scrollTop: frm.offset().top
                }, 1000);
                return false;
            }
        }
        btnSubmit.attr('disabled', true);

        $.ajax({
            async: true,
            url: 'index.php',
            dataType: 'json',
            data: {
                fc: 'module',
                controller: 'ajax',
                module: 'pwadvancedorder',
                method: 'getStep',
                data: data,
            },
            method: 'POST',
            beforeSend: function() {
                $('body').append('<div class="loader_spinner"></div>');
            },
            success: function() {
                $(".loader_spinner").remove();
            }

        })
            .done(function(resp) {

                btnSubmit.removeAttr('disabled');
                //var panel = frm.closest('.openStep').next('.panel');
                var panel = frm.closest('.openStep');
                panel.removeClass('closeStep').addClass('openStep');
                var lnk = panel.find('a.togglesteps');

                lnk.attr('data-toggle', 'collapse');

                lnk.trigger('click');

                if (typeof resp.is_error != 'undefined') {
                    for (field in resp.errors) {
                        if (frm.find('[name='+field+']').length) {
                            frm.find('[name='+field+']')
                                .addClass('form-error')
                                .after('<div class="form_info form_error">'+resp.errors[field]+'</div>');
                        } else {
                            frm.prepend('<div class="form_info form_error">'+resp.errors[field]+'</div>');
                            $('html, body').animate({
                                scrollTop: frm.offset().top
                            }, 1000);
                        }
                    }
                } else if(typeof resp.success != 'undefined') {
                    var panel = frm.closest('.openStep').next('.panel');

                    panel.removeClass('closeStep').addClass('openStep');
                    var lnk = panel.find('a.togglesteps');

                    lnk.attr('data-toggle', 'collapse');

                    lnk.trigger('click');
                    $.uniform.update();

                    $('html, body').animate({
                        scrollTop: frm.parents('.panel').offset().top
                    }, 1000);

                }
                if (typeof(resp.carrier_block) != 'undefined') {
                    $('#shippingtadv>div').html(resp.carrier_block);
                    $.uniform.update();
                }

                if (typeof(resp.HOOK_PAYMENT) != 'undefined') {
                    $('#paymenttadv .payment').empty().append(resp.HOOK_PAYMENT);
                    $.uniform.update();
                }
                if (typeof(resp.address) != 'undefined') {
                    location.reload();
                }
                if(panel.find('input[name=advStep]').val() == 'shipping'){
                    startCDEK(true);
                }
            });
        });
    $('.closeStep').find('.panel-title a').removeAttr('data-toggle');
    $(document).on('change', '[name=id_address_delivery]', function(){
        $('.adressAdvenc').find('.list-group-item').removeClass('active');
        $(this).closest('.list-group-item').addClass('active');
        var id_address = $(this).val();
        $('[id^=address_]').hide();
        $('#address_'+id_address).show();
        $('#newAddress').removeClass('in');
    });
    $('.openStep:first').find('a').trigger('click');
    setTimeout(function () {
        $('.panel-collapse').not('.collapse').parent().removeClass('closeStepos');
        $('.panel-collapse.collapse').parent().addClass('closeStepos');
    }, 370);
    $('.openStep').find('a').click(function () {
        setTimeout(function () {
            $('.panel-collapse').not('.collapse').parent().removeClass('closeStepos');
            $('.panel-collapse.collapse').parent().addClass('closeStepos');
        }, 370);


    });
    $(document).on('click', '#cgv', function(){
        var state = $(this).is(':checked');
        $.ajax({
            async: true,
            url: 'index.php',
            dataType: 'json',
            data: {
                fc: 'module',
                controller: 'ajax',
                module: 'pwadvancedorder',
                method: 'changeState',
                state: state,
            },
            method: 'POST'
        });
    });
    $(document).on('submit', '#oneclickform', function(e){
        $(this).find('.form-error').remove();
        if ($(this).find('#cgv').length) {
            if (!$(this).find('#cgv').is(':checked')) {
                e.preventDefault();
                $(this).prepend('<p class="form-error">Вы должны дать согласие на обработку персноальных данных</p>');
                return false;
            }
        }
    });
    $(document).on('click', '.cgvlink', function(e){
        if (!!$.prototype.fancybox) {
            e.preventDefault();
            $.fancybox({
                //'orig'			: $(this),
                'padding'		: 10,
                'href'			: $(this).attr('href'),
                'transitionIn'	: 'elastic',
                'transitionOut'	: 'elastic',
                'type' : 'iframe'
            });
            return false;
        }
    });


    $(document).on('keyup', '[name=email]', function(){
        if ($(this).val().length) {
            $('.password').slideDown();
        } else {
            $('.password').slideUp();
        }
    });
});

$(document).on('submit', '.updateAddressForm', function(e){
    e.preventDefault();
    var frm = $(this);
    if (frm.find('#cgv').length) {

        if (!frm.find('#cgv:checked').length) {
            $('#checkoutadv .panel-body').after('<div class="form_info form_error">Вы должны дать согласие на обработку персональных данных</div>');
            $('html, body').animate({
                scrollTop: frm.offset().top
            }, 1000);
            return false;
        }
    }
    updateAddress();

});

$(document).on('click', '.submitOrderPayment', function(e)
{
    e.preventDefault();
	if($('input[name=pay_method]:checked').length)
	{
		location.href = $('input[name=pay_method]:checked').val();
	}
	else
	{
		var err=document.getElementById('delivery_error'),container=document.getElementById('paymenttadv');
		if(!err)
		{
			err=document.createElement('p');
			err.className='error';
			err.id='delivery_error';
			err.innerHTML='Вы должны выбрать способ оплаты';	
			container.insertBefore(err,container.firstChild);
		}
	}
});

function updateAddress()
{
    var firstname = $('[name=firstname]').val() ? $('[name=firstname]').val() : 'Имя';
    var lastname = $('[name=lastname]').val() ? $('[name=lastname]').val() : 'Фамилия';
    var city = $('[name=city_1]').val() ? $('[name=city_1]').val() : 'Город';
    var address1 = $('[name=address1_1]').val() ? $('[name=address1_1]').val() : 'Адрес';
    var postcode = $('[name=postcode_1]').val() ? $('[name=postcode_1]').val() : '000000';
    var phone = $('[name=phone_1]').val() ? $('[name=phone_1]').val() : '0000';
    // var id_state = $('[name=id_state_1]').val() ? $('[name=id_state_1]').val() : '242';
    var id_state = $('[name=id_state]').val() ? parseInt($('[name=id_state]').val()) : 242;
    var email = $('[name=email]').val() ? $('[name=email]').val() : '_@_._';
    var id_country = $('[name=id_country_1]').val() ? $('[name=id_country_1]').val() : '177';
    var id_address = $('[name=id_address_1]').val() ? $('[name=id_address_1]').val() : '1';
    var alias = $('[name=alias]').val() ? $('[name=alias]').val() : 'Дом';
    var token = $('[name=token]').val() ? $('[name=token]').val() : '';
    // console.log(firstname + '|' + lastname + '|' + city + '|' + address1 + '|' + phone + '|' + id_state + '|' + id_country + '|' + id_address + '|' + alias + '|' + token);
    var params = '';
        params += 'firstname='+firstname;
        params += '&lastname='+lastname;
        params += '&city='+city;
        params += '&email='+email;
        params += '&address1='+address1;
        params += '&postcode='+postcode;
        params += '&phone='+phone;
        params += '&id_state='+id_state;
        params += '&id_country='+id_country;
        params += '&id_address='+id_address;
        params += '&alias='+alias;
        params += '&token='+token;


    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: "/address",
        async: false,
        cache: false,
        dataType : "json",
        data: 'ajax=true&submitAddress=true&'+params,
        success: function(jsonData)
        {
			if(!jsonData){console.log('!')}
            if (jsonData&&jsonData.hasError)
            {
                $('ol.errs').remove();
                var errors = '';
                for(error in jsonData.errors)
                    //IE6 bug fix
                    if(error != 'indexOf')
                        errors += '<li>' + jsonData.errors[error] + '</li>';
                $('.submit2').before('<ol class="errs">' + errors + '</ol>').show();
            }
            else {
                $( "#customeradv button.submitAdvanceForm" ).trigger( "click" );
                // $('.panel.openStep:not(.closeStepos)').next().find('a.togglesteps').trigger('click');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log('Ошибка обновления адреса');
        }
    });
}
