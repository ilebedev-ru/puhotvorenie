
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

$(window).load(function () {

    $(".product_autocomplete_input").after('<div class="divAccessories"></div><div id="jsonbag" style="display:none"></div>');

    $(".olreadyadded").each(function () {
        itemid = this.getAttribute('value');

        text = ($(this).parent().siblings("label").html());
        string = '<div class="addings form-control-static"><button type="button" onclick = "delAccessory(' + itemid + ');" class="delAccessory btn btn-default" id="delAccessory' + itemid + '">X</button>' + text + '<input type = "hidden" value = "' + itemid + '" name = "products[]"></div>';
        $(".divAccessories").append(string);
        $(this).parent().parent().remove();
    });
    /* function autocomplete */
    /*$('.product_autocomplete_input')
            .autocomplete('ajax_products_list.php', {
                appendTo: "#jsonbag",
                minChars: 3,
                autoFill: true,getProductsByIDs
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
                formatItem: function (data, i, n, value) {
                    var values = JSON.parse(value);
                    returnAutoCompletesHtml += '<ul>';
                    $.each(values, function (index, item) {
                        if (itemid !== item.id)
                            returnAutoCompletesHtml += '<li onclick = "addProduct(`' + item.name + '`, ' + item.id + ');" class="addings-list"> <span class="itemid">' + item.id + '</span>  - ' + item.name + '</li>';
                        itemid = item.id;
                        if(index==0){
                            firstword = item.name;
                        }
                    });
                    returnAutoCompletesHtml += '</ul>';
                   //$('.product_autocomplete_input').val(firstword);
                    return returnAutoCompletesHtml;
                }
            });*/
    $('.product_autocomplete_input')
        .autocomplete('ajax_products_list.php', {
            appendTo: "#jsonbag",
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
               // returnAutoCompletesHtml += '<ul>';
               /* console.log(data);
                $.each(data, function (index, item) {
                    console.log(item);*/
                    if (typeof item.id != "undefined")
                        value = '<li onclick = "addProduct(`' + item.name + '`, ' + item.id + ');" class="addings-list"> <span class="itemid">' + item.id + '</span>  - ' + item.name + '</li>';
                    itemid = item.id;

                //});
                //returnAutoCompletesHtml += '</ul>';
                //$('.product_autocomplete_input').val(firstword);
                return value;
            }
        });
});



function addProduct(text, itemid) {
    string = '<div class="addings form-control-static"><button type="button" onclick = "delAccessory(' + itemid + ');" class="delAccessory btn btn-default" id="delAccessory' + itemid + '">X</button>' + text + '<input type = "hidden" value = "' + itemid + '" name = "products[]"></div>';
    $(".divAccessories").append(string);
}

function delAccessory(id) {
    id = '#delAccessory' + id;
    $(id).parent().remove();
}










