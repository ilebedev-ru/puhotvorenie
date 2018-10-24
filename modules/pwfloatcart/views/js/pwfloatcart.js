$(function(){

    $('.block-up').on('click', '.pw_label', function(){
        $('html, body').animate({
            scrollTop: $('body').offset().top
        }, 2000);
    });

    var ajax_element = 'ajax_cart_quantity';
    var ajax_elemnt_hidden = 'ajax_cart_quantity hidden';
    var ajax_elemnt_unvisible = 'ajax_cart_quantity unvisible';


    var callback = function(allmutations){
        var el = '#pw_ajax_cart_quantity';
        var total = 0;

        allmutations.map( function(mr){

            if (mr.target.className == ajax_element
                || mr.target.className == ajax_elemnt_hidden
                || mr.target.className == ajax_elemnt_unvisible
            ) {
                total = parseInt(mr.target.innerHTML);

                if (total > 0) {
                    $(el).html(total + ' шт.');
                    $('.pwfloatcart .block-cart').css({'background':float_cart_background});
                } else {
                    $(el).html('');
                    $('.pwfloatcart .block-cart').css({'background':'none'});
                }
            }
        });
    };

    var element = document.getElementsByClassName(ajax_element);

    var mo = new MutationObserver(callback);
    var options = {
        'childList': true
    };

    mo.observe(element[0], options);
});