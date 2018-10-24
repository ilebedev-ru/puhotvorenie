$(function(){
    $('.'+pw_selector).fancybox({
        autoDimensions: true,
        afterLoad: function () {
            $(".fancybox-wrap").attr('id', 'uipw-question_modal');
        },
        'beforeLoad': function (){
            $('.uipw-form_layout').show();
            $('.uipw-form_success').hide();
        },
    });

    $('#'+pw_selector).on('submit', function(e){
        e.preventDefault();

        var _this = this;
        var action = $(_this).attr('action');
        var fields = {
            'email': $(_this).find('input[name="email"]'),
            'phone': $(_this).find('input[name="phone"]'),
            'message': $(_this).find('textarea')
        };

        //var form = new FormData(_this);

        $.ajax({
            type: 'POST',
            url: action,
            data: $(_this).serialize(),
            dataType: 'json',
            success: function (result) {
                if (result.status == 'error') {
                    $.each(fields, function(name, input){
                        if (typeof result.errors[name] !== 'undefined') {
                            var str_error = '<div class="err">' + result.errors[name] + '</div>';
                            var div_error = $(_this).find('.'+name);
                            div_error.html(str_error);

                            $(fields[name]).addClass('error');

                        } else {
                            $(input).removeClass('error');
                        }
                    });
                } else {
                    $.each(fields, function(name, field){
                        $(_this).find('.'+name).html('');
                        $(field).removeClass('error');
                    });

                    $(_this).trigger('reset');
                    $(_this).find('.uipw-form_layout').hide();
                    $(_this).find('.uipw-form_success').html(result.message).show();
                    $.fancybox.update();

                    if (typeof PWBackFormJs === 'function') {
                        PWBackFormJs();
                    }
                }
            }
        });
    });
});