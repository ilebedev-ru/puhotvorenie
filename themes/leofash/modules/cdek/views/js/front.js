/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(function () {
    startCDEK();
    console.log('startCDEK: init');
});

function startCDEK(init_only){

    initCDEK();
    if (init_only){return}

    if (typeof window.refreshDeliveryOptions != 'undefined')
    {
        window.oldRefreshDeliveryOptions = window.refreshDeliveryOptions;
        window.refreshDeliveryOptions = function () {
            setLoading();
            oldRefreshDeliveryOptions();
            initCDEK();
        }
    }

    if (typeof window.updateCarriers != 'undefined')
    {
        window.oldupdateCarriers = window.updateCarriers;
        window.updateCarriers = function () {
            setLoading();
            oldupdateCarriers();
            initCDEK();
        }
    }

    if (typeof window.updateCarrierList != 'undefined') {
        window.oldUpdateCarrierList = window.updateCarrierList;
        window.updateCarrierList = function (json) {
            setLoading();
            oldUpdateCarrierList(json);
            initCDEK();
        }
    }

    if (typeof prestashop != 'undefined') {
        $('#js-delivery input').on('change', function () {
            var id_carrier = parseInt($(this).val());
            if (typeof cdek_carriers[id_carrier] != 'undefined') {
                setLoading();
            }
        });

        prestashop.on('updatedDeliveryForm', function () {
            initCDEK();
        });
    }

    if (typeof Payment != 'undefined' && Payment.getByCountry != 'undefined') {
        Payment.oldGetByCountry = Payment.getByCountry;
        Payment.getByCountry = function (params) {
            initCDEK();
            Payment.oldGetByCountry(params);
        }
    }

    var ajaxGetListPvz = null;
    function initCDEK()
    {
        console.log('CDEK: init');
        if (ajaxGetListPvz != null)
            ajaxGetListPvz.abort();
        setLoading();
        ajaxGetListPvz = $.ajax({
            url: document.location.href.replace(document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: {
                ajax_cdek: true,
                method: 'get_list_pvz'
            },
            success: function (r)
            {
                clearLoading();
                cdek_carriers = r.result.cdek_carriers;

                $.each(cdek_carriers, function (id_carrier, carrier) {
                    var is_ps_17 = false;
                    var there_insert = null;

                    var delivery_option = $('.delivery_option_radio[value="'+id_carrier+',"]');
                    var delivery_option_elem = delivery_option.closest('tr').find('td');

                    if (delivery_option_elem.length) {
                        there_insert = delivery_option_elem.eq(2);
                    }

                    if (!delivery_option_elem.length) {
                        delivery_option = $('#delivery_option_'+id_carrier);
                        if (delivery_option.length) {
                            is_ps_17 = true;
                        }
                        delivery_option_elem = delivery_option.closest('.delivery-option').find('label[for="delivery_option_'+id_carrier+'"]');
                        delivery_option_elem.find('.cdek_row').remove();
                    }

                    //onepagecheckoutps 2.1.6
                    if (!delivery_option_elem.length) {
                        delivery_option = $('.delivery_option_radio[value="'+id_carrier+',"]');
                        delivery_option_elem = delivery_option.closest('.delivery_option');
                        there_insert = delivery_option_elem;
                    }

                    //onepagecheckout
                    if (!delivery_option_elem.length) {
                        delivery_option = $('#id_carrier'+String(String(id_carrier).length) +id_carrier+'0000');
                        delivery_option_elem = delivery_option.closest('tr').find('.carrier_infos');
                        there_insert = delivery_option_elem;
                    }

                    if (delivery_option_elem.length)
                    {
                        if (!is_ps_17) {
                            delivery_option_elem.find('.cdek_address').remove();
                            delivery_option_elem.find('.cdek_pvz_list').remove();
                        }
                        delivery_option_elem.find('.cdek_delay').remove();

                        var $delay = '<span class="cdek_delay">'+carrier.delay+'</span>';
                        if ($.inArray(carrier.mode, [1, 3]) != -1)
                        {
                            insertDelay();

                            if (delivery_option.is(':checked') && !cdek_address_parameter)
                            {
                                var address_html = $($('#cdek_address').html());
                                address_html.find(
                                    '.cdek_delivery_date'
                                ).attr(
                                    'data-min-date',
                                    delivery_option_elem.find('.cdek_delay .delivery_date_min').text()
                                );
                                address_html.find('.cdek_street').val(r.result.address.street);
                                address_html.find('.cdek_house').val(r.result.address.house);
                                address_html.find('.cdek_flat').val(r.result.address.flat);
                                there_insert.append(address_html);
                            }
                        }

                        if ($.inArray(carrier.mode, [2, 4]) != -1)
                        {
                            insertDelay();

                            if (delivery_option.is(':checked'))
                            {
                                var cdek_pvz_list = $('#cdek_pvz_list').html();

                                //var list = '';
                                var selected = '';
                                $.each(r.result.pzv_list, function (index, item) {
                                    var render = $('#cdek_pvz_list_item').html()
                                        .split('%active%').join(item.Code == r.result.cdek_pvz_code ? 'active' : '')
                                        .split('%code%').join(item.Code)
                                        .split('%address%').join(item.Address)
                                        .split('%city%').join(item.City)
                                        .split('%phone%').join(item.Phone)
                                        .split('%work_time%').join(item.WorkTime);

                                    //list += render;
                                    if (item.Code == r.result.cdek_pvz_code)
                                        selected = render;
                                });

                                var list = '<div id="ymaps_pvz"></div>';

                                var html = cdek_pvz_list
                                    .split('%selected%').join($(selected).html())
                                    .split('%list%').join(list);

                                html = $(html);
                                html.find(
                                    '.cdek_delivery_date'
                                ).attr(
                                    'data-min-date',
                                    delivery_option_elem.find('.cdek_delay .delivery_date_min').text()
                                );

                                there_insert.append(html);
                                initMap(r.result.pzv_list, r.result.cdek_pvz_code);
                            }
                        }

                        function insertDelay() {
                            if (is_ps_17) {
                                createCdekRowAndGoTo();
                                delivery_option_elem.find('.carrier-delay').html($delay);
                            } else {
                                there_insert.append($delay);
                            }
                        }

                        function createCdekRowAndGoTo() {
                            delivery_option_elem.append('<div class="row cdek_row"><div class="col-md-12"></div></div>');
                            there_insert = delivery_option_elem.find('.cdek_row > div');
                        }

                        initDeliveryTime();
                    }
                });
            },
            error: function ()
            {
                clearLoading();
            }
        });

    }

    function initDeliveryTime() {
        var cdek_block = $('.cdek_selected, .cdek_address');
        var date_str = cdek_block.find('.cdek_delivery_date').attr('data-min-date');
        var date = new Date(date_str);
        //date.setDate(date.getDate() + 1);
        cdek_block.find('.cdek_delivery_date').datepicker({
            dateFormat: "dd-mm-yy",
            minDate: date
        }).val(cdek_order_info.date);

        var $time_begin = $('.time_begin_clockpicker');
        var $time_end = $('.time_end_clockpicker');
        if ($time_begin.length && $time_end.length) {
            $time_begin.clockpicker({
                afterDone: function() {
                    updateCdekDate();
                }
            });
            $time_end.clockpicker({
                afterDone: function() {
                    updateCdekDate();
                }
            });

            cdek_block.find('.cdek_delivery_time_begin').val(cdek_order_info.time_begin);
            cdek_block.find('.cdek_delivery_time_end').val(cdek_order_info.time_end);
        } else {
            cdek_order_info.time_begin = '0:00';
            cdek_order_info.time_end = '23:59';
        }
    }

    function setLoading()
    {
        $.each(cdek_carriers, function (id_carrier) {
           var delivery_option = $('.delivery_option_radio[value="'+id_carrier+',"]');
           if (delivery_option.length)
                delivery_option.closest('.delivery_option').addClass('cdek_loading');

            if (!delivery_option.length) {
                delivery_option = $('#delivery_option_'+id_carrier);
                if (delivery_option.length)
                    delivery_option.closest('.delivery-option').addClass('cdek_loading');
            }
        });
    }

    function clearLoading()
    {
        $.each(cdek_carriers, function (id_carrier) {
            var delivery_option = $('.delivery_option_radio[value="'+id_carrier+',"]');
            if (delivery_option.length)
                delivery_option.closest('.delivery_option').removeClass('cdek_loading');

            if (!delivery_option.length) {
                delivery_option = $('#delivery_option_'+id_carrier);
                if (delivery_option.length)
                    delivery_option.closest('.delivery-option').removeClass('cdek_loading');
            }
        });
    }

    $('body').delegate('.cdek_pvz_list_item', 'click', function (e) {
        e.preventDefault();
        setLoading();
        var self = $(this);
        $.ajax({
            url: document.location.href.replace(document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: {
                ajax_cdek: true,
                method: 'cdek_code',
                code: $(this).data('code'),
                delivery_point: $(this).data('delivery-point')
            },
            success: function (r)
            {
                $('.cdek_pvz_list_item').removeClass('active');
                self.addClass('active');
                $('.cdek_selected').html(self.html());
                initDeliveryTime();
                clearLoading();
            },
            error: function ()
            {
                clearLoading();
            }
        });
    });

    var ajaxSetAddress = null;
    $('body').delegate('[data-cdek-address]', 'keyup', function () {
        var street = $('.cdek_street').val();
        var house = $('.cdek_house').val();
        var flat = $('.cdek_flat').val();

        if (ajaxSetAddress != null)
            ajaxSetAddress.abort();
        ajaxSetAddress = $.ajax({
            url: document.location.href.replace(document.location.hash, ''),
            type: 'POST',
            data: {
                ajax_cdek: true,
                method: 'cdek_address',
                street: street,
                house: house,
                flat: flat
            }
        });
    });

    $('body').delegate('.cdek_delivery_date', 'change', function () {
        updateCdekDate();
    });

    var ajaxSetDeliveryDate = null;
    function updateCdekDate() {
        if (ajaxSetDeliveryDate != null) {
            ajaxSetDeliveryDate.abort();
        }
        var cdek_block = $('.cdek_selected, .cdek_address');

        var date = cdek_block.find('.cdek_delivery_date').val();
        var time_begin = 0;
        if (cdek_block.find('.cdek_delivery_time_begin').length) {
            time_begin = cdek_block.find('.cdek_delivery_time_begin').val();
        }
        var time_end = 23;
        if (cdek_block.find('.cdek_delivery_time_end').length) {
            time_end = cdek_block.find('.cdek_delivery_time_end').val();
        }

        if (time_begin > time_end) {
             $('.cdek_delivery_time_end').val(time_begin);
        }

        cdek_order_info.date = date;
        cdek_order_info.time_begin = time_begin;
        cdek_order_info.time_end = time_end;

        ajaxSetDeliveryDate = $.ajax({
            url: document.location.href.replace(document.location.hash, ''),
            type: 'POST',
            data: {
                ajax: true,
                ajax_cdek: true,
                method: 'cdek_delivery_date',
                date: date,
                time_begin: time_begin,
                time_end: time_end
            }
        });
    }

    var map_settings = {};
    map_settings.ready = false;

    if (typeof ymaps != 'undefined') {
        ymaps.ready(function () {
            map_settings.balloon_content_layout = ymaps.templateLayoutFactory.createClass(
                '<div class="balloon_message"><div class="arrow"></div>$[properties.balloonContent]</div>'
            );

            map_settings.balloon_layout = ymaps.templateLayoutFactory.createClass(
                '<div class="wrapp_balloon">' +
                '$[[options.contentLayout observeSize minWidth=235 maxWidth=235 maxHeight=350]]' +
                '</div>', {
                    /**
                     * Строит экземпляр макета на основе шаблона и добавляет его в родительский HTML-элемент.
                     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#build
                     * @function
                     * @name build
                     */
                    build: function () {
                        this.constructor.superclass.build.call(this);

                        this._$element = $('.balloon_message', this.getParentElement());

                        this.applyElementOffset();
                    },

                    /**
                     * Метод будет вызван системой шаблонов АПИ при изменении размеров вложенного макета.
                     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
                     * @function
                     * @name onSublayoutSizeChange
                     */
                    onSublayoutSizeChange: function () {
                        map_settings.balloon_layout.superclass.onSublayoutSizeChange.apply(this, arguments);

                        if(!this._isElement(this._$element)) {
                            return;
                        }

                        this.applyElementOffset();

                        this.events.fire('shapechange');
                    },

                    /**
                     * Сдвигаем балун, чтобы "хвостик" указывал на точку привязки.
                     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
                     * @function
                     * @name applyElementOffset
                     */
                    applyElementOffset: function () {
                        this._$element.css({
                            left: -(this._$element[0].offsetWidth / 2),
                            top: -(this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight)
                        });
                    },

                    /**
                     * Используется для автопозиционирования (balloonAutoPan).
                     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ILayout.xml#getClientBounds
                     * @function
                     * @name getClientBounds
                     * @returns {Number[][]} Координаты левого верхнего и правого нижнего углов шаблона относительно точки привязки.
                     */
                    getShape: function () {
                        if(!this._isElement(this._$element)) {
                            return map_settings.balloon_layout.superclass.getShape.call(this);
                        }

                        var position = this._$element.position();

                        return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
                            [position.left, position.top], [
                                position.left + this._$element[0].offsetWidth,
                                position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight
                            ]
                        ]));
                    },

                    /**
                     * Проверяем наличие элемента (в ИЕ и Опере его еще может не быть).
                     * @function
                     * @private
                     * @name _isElement
                     * @param {jQuery} [element] Элемент.
                     * @returns {Boolean} Флаг наличия.
                     */
                    _isElement: function (element) {
                        return element && element[0] && element.find('.arrow')[0];
                    }
                });

            map_settings.ready = true;
        });
    }

    function initMap(balloons, cdek_pvz_code) {
        if (!map_settings.ready) {
            setTimeout(function () {
                initMap(balloons, cdek_pvz_code);
            }, 300);
            return false;
        }

        window.map_pvz = new ymaps.Map('ymaps_pvz', {
            zoom: 4,
            center: [96.834944, 63.586587],
            behaviors: ['default', 'scrollZoom']
        });

        $.each(balloons, function (key, balloon) {
            setPlaceMark(
                window.map_pvz,
                balloon,
                cdek_pvz_code,
                (key == 0 ? true : false)
            );
        });
    }


    function setPlaceMark(map, balloon, cdek_pvz_code, open) {
        var render = $('#cdek_pvz_list_item').html()
            .split('%active%').join((balloon.Code == cdek_pvz_code ? 'active' : '')+' map_item')
            .split('%code%').join(balloon.Code)
            .split('%address%').join(balloon.Address)
            .split('%city%').join(balloon.City)
            .split('%phone%').join(balloon.Phone)
            .split('%work_time%').join(balloon.WorkTime.split(',').join(',<br>'));


        map.setCenter([parseFloat(balloon.coordY), parseFloat(balloon.coordX)], 10);
        var placemark = new ymaps.Placemark([parseFloat(balloon.coordY), parseFloat(balloon.coordX)], {
            balloonContent: render
        }, {
            iconImageHref: cdek_dir + 'views/img/geo.png',
            iconLayout: 'default#image',
            iconImageSize: [25, 40],
            iconImageOffset: [-25, -40],
            balloonCloseButton: false,
            hideIconOnBalloonOpen: false,
            balloonShadow: false,
            balloonLayout: map_settings.balloon_layout,
            balloonContentLayout: map_settings.balloon_content_layout,
            balloonPanelMaxMapArea: 0
        });

        map.geoObjects.add(placemark);
        if (open) {
            placemark.balloon.open();
        }

        if (balloon.Code == cdek_pvz_code) {
            placemark.balloon.open();
        }
    }

});

