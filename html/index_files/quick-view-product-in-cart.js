$(document).ready(function(){

    $('.cart_product.ajax_block_product .product, ' +
        '#pwproductincart_block_center a').click(function(){
        // $('.product-preview').show();
        var urlProd = $(this).attr('href');
        btt_spc = parseInt(window.window.innerHeight/3);
        $.fancybox.open({
            href: urlProd + '?content_only=1',
            type: 'iframe',
            width: '100%',
            height: '60%',
            padding : 20,
            topRatio    : 0,
            scrolling : false,
            margin: [20, 20, btt_spc, 20],
            afterLoad : function(){
                $("#fancybox-inner").css({'overflow-x':'hidden'});
            }
        });
        // $.ajax({
        //     url: urlProd + '?content_only=1',
        //     success: function(data){
        //         $('.product-preview .body .product-detail').html(data);
        //         setTimeout(function () {
        //             $('.product-preview .body').animate({
        //                 marginBottom: 'auto',
        //                 marginTop: 100
        //             }, 300);
        //         }, 200)
        //     }
        // });
        return false;
    });

    $('.product-preview .body .close').click(function () {
        $('.product-preview .body').animate({
            marginTop: '-1000px'
        }, 300);
        setTimeout(function () {
            $('.product-preview').hide();
        }, 350);
        $('.product-preview .body .product-detail').html('');
    });
});