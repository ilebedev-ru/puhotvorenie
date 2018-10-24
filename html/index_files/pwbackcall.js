$(function() {

    if(typeof $.fn.draggable !== 'undefined') {
        $('#pwbackcall').draggable();
    }

    function noError() {
        var fields = $('#uipw-form_call_modal').find('input');
                    
        $.each(fields, function(i, el){
            $(el).removeClass('error').css({'display':'block'});
        });
    }

    $('.backcall-button').fancybox({
        href: '#uipw-form_call_modal',
        afterClose: function () {
            $('#uipw-form_call_modal').trigger('reset').show();
            $('#uipw-form_call_modal .error').html('').hide();

            $('#uipw-form_call_modal .success').html('').hide();
            $('#uipw-form_call_modal .uipw-modal_form_fields').css({'display':'block'});
            $('#uipw-form_call_modal').hide();
            noError();
        },
        afterLoad: function () {
            $(".fancybox-wrap").attr('id', 'uipw-call_modal');
        }
    });

    $('#uipw-form_call_modal').on('submit', function (e) {
        e.preventDefault();

        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function (result) {
                if (result.status == 1) {
                    
                    noError();
                    
                    $('#uipw-form_call_modal .uipw-modal_form_fields').hide();
                    $('#uipw-form_call_modal .error').html('').hide();
                    $('#uipw-form_call_modal .success').html(backcallMessage).show();

                    if (typeof PwBackCallJs === 'function') {
                        PwBackCallJs();
                    }
                } else {
                    var errors = '';
                    $.each(result.errors, function (field, error) {
                        errors = errors + error + '<br/>';
                        $('#uipw-form_call_modal input[name='+field+']').addClass('error');
                    });
                    $('#uipw-form_call_modal .error').html(errors).show();
                }
            },
            dataType: 'json'
        });
    });
    
    $("#pwbackcall").hover(function() {
        $(this).addClass('pw-hover');
    }, function() {
        $(this).removeClass('pw-hover');
    });
});