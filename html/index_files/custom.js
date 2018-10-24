$(document).ready(function () {
	

    $("#cart_img_full_size").fancybox();

    if ($.cookie('listing_products_mode')) {

        $("#product_list").removeClass('view-grid').removeClass('view-list').addClass($.cookie('listing_products_mode'));

    }



    $("#productsview a").each(function () {

        if ($.cookie('listing_products_mode') && $(this).attr('rel') == $.cookie('listing_products_mode')) {

            $('#productsview a i').removeClass('active');

            $('i', this).addClass('active');

        }

        $(this).click(function () {

            $('#productsview a i').removeClass('active');

            $('i', this).addClass('active');

            $("#product_list").removeClass('view-grid').removeClass('view-list').addClass($(this).attr('rel'));

            $.cookie('listing_products_mode', $(this).attr('rel'));

            return false;

        });

    });



    $('.short_description-delivery_info').click(function () {

        console.log(1);

        $('.short_description-delivery_info-show').slideToggle();

    });



    //userinfo



    $("#header_user").each(function () {

        var content = $(".groupe-content");

        $(".groupe-btn", this).click(function () {

            content.toggleClass("eshow");

        });

    });



    // scroll top

    $('#nav_up').click(function () {

        $('body,html').animate({

            scrollTop: 0

        }, 800);

        return false;

    });



    // canvas menu

    $(document.body).on('click', '[data-toggle="dropdown"]', function () {

        if (!$(this).parent().hasClass('open') && this.href && this.href != '#') {

            window.location.href = this.href;

        }



    });



    //tooltip

    $('.btn-tooltip').tooltip('show');

    $('.btn-tooltip').tooltip('hide');



    // gototop

    // hide #back-top first

    $("#back-top").hide();

    // fade in #back-top

    $(function () {

        $(window).scroll(function () {

            if ($(this).scrollTop() > 100) {

                $('#back-top').fadeIn();

            } else {

                $('#back-top').fadeOut();

            }

        });

        // scroll body to 0px on click

        $('#back-top a').click(function () {

            $('body,html').animate({

                scrollTop: 0

            }, 800);

            return false;

        });

    });

    $('.search_button').click(function(){

    	$('.top-wrap #search_block_top').toggleClass('fullsized');

    });

});

function LeoWishlistCart(id, action, id_product, id_product_attribute, quantity) {

    $.ajax({

        type: 'GET',

        url: baseDir + 'modules/blockwishlist/cart.php',

        async: true,

        cache: false,

        data: 'action=' + action + '&id_product=' + id_product + '&quantity=' + quantity + '&token=' + static_token + '&id_product_attribute=' + id_product_attribute,

        success: function (data) {

            if (action == 'add') {

                var html = '<div id="page_notification" class="notification alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="not-content">' + data + '</div></div>';

                if (!$("#page_notification").length) $("body").append(html);

                else $("#page_notification .not-content").html(data);



                $(".notification").show().delay(2000).fadeOut(600);

            }



            if ($('#' + id).length != 0) {

                $('#' + id).slideUp('normal');

                document.getElementById(id).innerHTML = data;

                $('#' + id).slideDown('normal');

            }

        }

    });

}



