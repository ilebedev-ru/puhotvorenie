(function($) {
    $.LeoCustomAjax = function() {
        this.leoData = 'leoajax=1';
    };
    $.LeoCustomAjax.prototype = {
        processAjax: function() {
            var myElement = this;
        
            if (leoOption.productNumber && $("#categories_block_left .leo-qty").length) myElement.getCategoryList();
			else if($("#categories_block_left .leo-qty").length) $("#categories_block_left .leo-qty").remove();
            if (leoOption.productRating && $("a.rating_box").length) myElement.getProductRating();
			else if($("a.rating_box").length) $("a.rating_box").remove();
            if (leoOption.productInfo && $(".leo-more-info").length) myElement.getProductListInfo();
			else if($(".leo-more-info").length) $(".leo-more-info").remove();
            if (leoOption.productTran && $(".product-additional").length) myElement.getProductListTran();
			else if($(".product-additional").length) $(".product-additional").remove();
            
            // if (leoOption.productQV && $('.quick-view').length) {
             //    $('.quick-view').fancybox(
             //            {
             //                'hideOnContentClick': 0,
             //                'transitionIn': 'elastic',
             //                'transitionOut': 'elastic',
             //                'autoDimensions': 0,
             //                'height': 900,
             //                'width': 900,
             //                'type': 'iframe'
             //            });
             //    $(".quick-view").click(function() {
             //        //global value in module
             //        leoFacyElement = this;
             //    });
            // }else{
			// 	if($(".quick-view").length)$('.quick-view').remove();
			// }
            //alert(myElement.leoData);
            //leoajax=1&cat_list=3,6,4,5&pro_list=1,6,3,2,7,4,5&pro_info=1,6,3,2,7,4,5
			if(myElement.leoData != "leoajax=1"){
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: baseDir + 'modules/leocustomajax/leoajax.php' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: myElement.leoData,
                success: function(jsonData) {
                    if (jsonData) {
                        if (jsonData.cat) {
                            for (i = 0; i < jsonData.cat.length; i++) {
                                $("#leo-cat-" + jsonData.cat[i].id_category).html(jsonData.cat[i].total);
                                $("#leo-cat-" + jsonData.cat[i].id_category).show();
                            }
                        }
                        if (jsonData.pro) {
                            $("a.rating_box").show();
                            for (i = 0; i < jsonData.pro.length; i++) {
                                $(".leo-rating-" + jsonData.pro[i].id).show();
                                $(".leo-rating-" + jsonData.pro[i].id).each(function(index) {
                                    $(this).find("i").each(function(index) {
                                        if (index < jsonData.pro[i].rate) {
                                            $(this).attr("class", "fa fa-star");
                                        }
                                    });
                                });
                            }
                        }
                        if (jsonData.pro_info) {
                            var listProduct = new Array();
                            for (i = 0; i < jsonData.pro_info.length; i++) {
                                listProduct[jsonData.pro_info[i].id] = jsonData.pro_info[i].content;
                            }

                            $(".leo-more-info").each(function() {
                                $(this).html(listProduct[$(this).attr("rel")]);
                                addJSProduct($(this).attr("rel"));
                            });
                            addEffectProduct();
                        }

                        if (jsonData.pro_add) {
                            var listProductImg = new Array();
                            for (i = 0; i < jsonData.pro_add.length; i++) {
                                listProductImg[jsonData.pro_add[i].id] = jsonData.pro_add[i].content;
                            }
                            $(".product-additional").each(function() {
                                if (listProductImg[$(this).attr("rel")])
                                    $(this).html('<img class="img-responsive" title="" alt="" src="' + listProductImg[$(this).attr("rel")] + '"/>');
                            });
                            //addEffOneImg();
                        }
                    }
                },
                error: function() {
                }
            });
            }
        },
        getCategoryList: function() {
            //get category id
            var leoCatList = "";
            $("#categories_block_left .leo-qty").each(function() {
                if (leoCatList)
                    leoCatList += "," + $(this).attr("id");
                else
                    leoCatList = $(this).attr("id");
            });
            
            leoCatList = leoCatList.replace(/leo-cat-/g, "");
            
            if (leoCatList) {
                this.leoData += '&cat_list=' + leoCatList;
            }
            return false;
        },
        getProductRating: function() {
            //get product id
            var leoProduct = "";
            var tmpPro = new Array();
            $("a.rating_box").each(function(i) {
                
                myrel = $(this).attr("rel");
                if ($.inArray(myrel, tmpPro) == -1) {
                    tmpPro[i] = myrel;
                    if (leoProduct)
                        leoProduct += "," + myrel;
                    else
                        leoProduct = myrel;
                }
            });
            if (leoProduct) {
                this.leoData += '&pro_list=' + leoProduct;
            }
            return false;
        },
        getProductListInfo: function() {
            var leoProInfo = "";
            $(".leo-more-info").each(function() {
                if (!leoProInfo)
                    leoProInfo += $(this).attr("rel");
                else
                    leoProInfo += "," + $(this).attr("rel");
            });
            if (leoProInfo) {
                this.leoData += '&pro_info=' + leoProInfo;
            }
            return false;
        },
        getProductListTran: function() {
            //tranditional image
            var leoAdditional = "";
            $(".product-additional").each(function() {
                if (!leoAdditional)
                    leoAdditional += $(this).attr("rel");
                else
                    leoAdditional += "," + $(this).attr("rel");
            });
            if (leoAdditional) {
                this.leoData += '&pro_add=' + leoAdditional;
            }
            return false;
        }
    };
}(jQuery));

function quickViewAddToCart(pid, aid, q) {
    ajaxCart.add(pid, aid, false, leoFacyElement, q, null);
    $.fancybox.close();
    return false;
}
function addJSProduct(currentProduct) {
    $('.thumbs_list_' + currentProduct).serialScroll({
        items: 'li:visible',
        prev: '.view_scroll_left_' + currentProduct,
        next: '.view_scroll_right_' + currentProduct,
        axis: 'y',
        offset: 0,
        start: 0,
        stop: true,
        duration: 700,
        step: 1,
        lazy: true,
        lock: false,
        force: false,
        cycle: false
    });
    $('.thumbs_list_' + currentProduct).trigger('goto', 1);// SerialScroll Bug on goto 0 ?
    $('.thumbs_list_' + currentProduct).trigger('goto', 0);
}

function addEffectProduct() {
    var speed = 800;
    var effect = "easeInOutQuad";

    $(".carousel-inner .product_block:first-child").mouseenter(function() {
        $(".carousel-inner").css("overflow", "inherit");
    });
    $(".carousel-inner").mouseleave(function() {
        $(".carousel-inner").css("overflow", "hidden");
    });

    $(".leo-more-info").each(function() {
        var leo_preview = this;
        $(leo_preview).find(".leo-hover-image").each(function() {
            $(this).mouseover(function() {
                var big_image = $(this).attr("rel");
                imgElement = $(leo_preview).parent().find(".product_img_link img").first();
                if (!imgElement.length) {
                    imgElement = $(leo_preview).parent().find(".product_image img").first();
                }

                if (imgElement.length) {
                    $(imgElement).stop().animate({opacity: 0}, {duration: speed, easing: effect});
                    $(imgElement).first().attr("src", big_image);
                    $(imgElement).first().attr("data-rel", big_image);
                    $(imgElement).stop().animate({opacity: 1}, {duration: speed, easing: effect});
                }
            });
        });
    });

    $('.thickbox').fancybox({
            'hideOnContentClick': true,
            'transitionIn'  : 'elastic',
            'transitionOut' : 'elastic'
    });
}

function addEffOneImg() {
    var speed = 800;
    var effect = "easeInOutQuad";

    $(".product-additional").each(function() {
        if ($(this).find("img").length) {
            var leo_hover_image = $(this).parent().find("img").first();
            var leo_preview = $(this);
            $(this).parent().mouseenter(function() {
                $(this).find("img").first().stop().animate({opacity: 0}, {duration: speed, easing: effect});
                $(leo_preview).stop().animate({opacity: 1}, {duration: speed, easing: effect});
            });
            $(this).parent().mouseleave(function() {
                $(this).find("img").first().stop().animate({opacity: 1}, {duration: speed, easing: effect});
                $(leo_preview).stop().animate({opacity: 0}, {duration: speed, easing: effect});
            });
        }
    });
}