$(function () {
    $('.clear_log').live('click', function () {
        $.ajax({
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            data: {
                ajax: true,
                action: 'clear_cdek_log'
            },
            success: function () {
                var table = $('.cdek_logger tbody');
                table.html('');
                pages = [];
                checkPagination();
            }
        });
    });

    $('.show_more').live('click', function () {
        var page = pages.shift();
        paginationSet(page);
    });

    highlightJSON();

    var clipboard = new Clipboard('[data-copy-field]', {
        text: function (trigger) {
            alert('Copied!');
            return $(trigger).parent().find('code').data('code');
        }
    });

    $('body').delegate('.trigger_row', 'change', function () {
        var $tr = $(this).closest('tr');
        var enabled = false;
        $tr.find('.trigger_row').each(function () {
            if ($(this).is(':checked')) {
                enabled = true;
            }
        });

        if (enabled) {
            $tr.find(':input').not('.trigger_row').each(function () {
                $(this).removeAttr('disabled');
            });
        } else {
            $tr.find(':input').not('.trigger_row').each(function () {
                $(this).attr('disabled', true);
            });
        }
    });
    $('.trigger_row').trigger('change');
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('.applyFilterLogger').live('click', function () {
        var data = {};
        $('[name^="search["]').each(function () {
            data[$(this).attr('name')] = $(this).val();
        });
        paginationSet(1, data);
    });
});

function checkPagination() {
    if (!pages.length) {
        $('.show_more').hide();
    }
}

function highlightJSON() {
    $('code').each(function(i, block) {
        $(this).data('code', $(this).html());

        hljs.highlightBlock(block);
    });
}

var pagination_ajax = null;
function paginationSet(page, filter) {
    var table = $('.cdek_logger tbody');
    var data = {
        ajax: true,
            action: 'get_cdek_log',
        page: page
    };
    if (typeof filter != 'undefined') {
        $.extend(data, filter);
    }

    if (pagination_ajax != null) {
        pagination_ajax.abort();
        pagination_ajax = null;
    }

    pagination_ajax = $.ajax({
        url: document.location.href.replace('#'+document.location.hash, ''),
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function (json) {
            if (page == 1) {
                pages = [];
                for (var i = 2; i <= json.pages; i++) {
                    pages.push(i);
                }
            }

            checkPagination();
            if (page == 1) {
                table.html(json.html);
            } else {
                table.append(json.html);
            }
            highlightJSON();
        }
    });
}
