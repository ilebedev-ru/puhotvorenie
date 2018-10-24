
/* global variables */
returnAutoCompletesHtml = "";
itemid = 0;
firstword = "";
var strbefore = new String;
var str;
var to;
var newstr="";

/* functions called by cropping events */
var string;

function addProduct(text, itemid) {
    string = '<div class="addings form-control-static"><button type="button"  class="delAccessory btn btn-default" id="delAccessory' + itemid + '">X</button>' + text + '<input type = "hidden" value = "' + itemid + '" name = "products[]"></div>';
    $(".divAccessoriesProducts").append(string);
}

function addCategory(text, itemid) {
    string = '<div class="addings form-control-static"><button type="button"  class="delAccessory btn btn-default" id="delAccessory' + itemid + '">X</button>' + text + '<input type = "hidden" value = "' + itemid + '" name = "categories[]"></div>';
    $(".divAccessoriesCategories").append(string);
}

$(window).load(function () {

    $('body').delegate(".delAccessory", "click", function(){
        $(this).parent().remove();
    });

    $(".product_autocomplete_input").after('<div class="divAccessoriesProducts"></div><div id="jsonbagProducts" style="display:none"></div>');
    $(".categories_autocomplete_input").after('<div class="divAccessoriesCategories"></div><div id="jsonbagCategories" style="display:none"></div>');
    var howManyLabels = $('label').length;
    var howManyLabelsPC = howManyLabels - ($(".olreadyaddedProducts").length + $(".olreadyaddedCategories").length+1);//Элементы считаются с  0
    var labelN = 0;
    $(".olreadyaddedProducts").each(function () {
        labelN++;
        var itemid = this.getAttribute('value');
        var text = ($('label').eq(howManyLabelsPC+labelN).html());
        string = '<div class="addings form-control-static"><button type="button"  class="delAccessory btn btn-default" id="delAccessory' + itemid + '">X</button>' + text + '<input type = "hidden" value = "' + itemid + '" name = "products[]"></div>';
        $(".divAccessoriesProducts").append(string);
        $(this).parent().remove();
    });
    $(".olreadyaddedCategories").each(function () {
        labelN++;
        var itemid = this.getAttribute('id');//this.getAttribute('value');
        var text = ($('label').eq(howManyLabelsPC+labelN).html());
        string = '<div class="addings form-control-static"><button type="button"  class="delAccessory btn btn-default" id="delAccessory' + itemid + '">X</button>' + text + '<input type = "hidden" value = "' + itemid + '" name = "categories[]"></div>';
        $(".divAccessoriesCategories").append(string);
        $(this).parent().remove();
    });
    $('label:gt('+howManyLabelsPC+')').remove();


    $('.product_autocomplete_input')
        .autocomplete($moduleURL + 'ajax_products_list_smartblog.php', {
            appendTo: "#jsonbagProducts",
            minChars: 3,
            autoFill: true,
            max: 20,
            matchContains: true,
            mustMatch: true,
            scroll: true,
            dataType: "json",
            parse: function(data) {
                var mytab = new Array();
                for (var i = 0; i < data.length; i++)
                    mytab[mytab.length] = { data: data[i], value: data[i].name };
                return mytab;
            },
            formatItem: function(data, i, max, value, term) {
                var item = data;
                    if (typeof item.id != "undefined")
                        value = '<li onclick = "addProduct(`' + item.name + '`, ' + item.id + ');" class="addings-list"> <span class="itemid">' + item.id + '</span>  - ' + item.name + '</li>';
                return value;
            }
        });

    $('.categories_autocomplete_input')
        .autocomplete($moduleURL + 'ajax_category_list_smartblog.php', {
            appendTo: "#jsonbagCategories",
            minChars: 3,
            autoFill: true,
            max: 20,
            matchContains: true,
            mustMatch: true,
            scroll: true,
            dataType: "json",
            parse: function(data) {
                var mytab = new Array();
                for (var i = 0; i < data.length; i++)
                    mytab[mytab.length] = { data: data[i], value: data[i].name };
                return mytab;
            },
            formatItem: function(data, i, max, value, term) {
                var item = data;
                if (typeof item.id != "undefined")
                    value = '<li onclick = "addCategory(`' + item.name + '`, ' + item.id + ');" class="addings-list"> <span class="itemid">' + item.id + '</span>  - ' + item.name + '</li>';
                return value;
            }
        });
});














