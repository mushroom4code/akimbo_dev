
jQuery(document).ready(function ($) {

    if ($('#billing__inn').length) {
        // функция проверки ИНН
        var isInn = function(inn) {
            if (typeof inn === 'string' || typeof inn === 'number') {
                inn = inn.toString();
                if ((/^\d+$/).test(inn) === false) {
                    return false;
                }
                console.dir(inn.charAt(10) == ((
                    7 * inn.charAt(0) +
                    2 * inn.charAt(1) +
                    4 * inn.charAt(2) +
                    10 * inn.charAt(3) +
                    3 * inn.charAt(4) +
                    5 * inn.charAt(5) +
                    9 * inn.charAt(6) +
                    4 * inn.charAt(7) +
                    6 * inn.charAt(8) +
                    8 * inn.charAt(9)
                ) % 11) % 10)
                console.dir(7 * inn.charAt(0) +
                    2 * inn.charAt(1) +
                    4 * inn.charAt(2) +
                    10 * inn.charAt(3) +
                    3 * inn.charAt(4) +
                    5 * inn.charAt(5) +
                    9 * inn.charAt(6) +
                    4 * inn.charAt(7) +
                    6 * inn.charAt(8) +
                    8 * inn.charAt(9))
                // Проверка контрольных цифр
                if (inn.length === 10) {
                    // Для 10-значного ИНН
                    return inn.charAt(9) == ((
                        2 * inn.charAt(0) +
                        4 * inn.charAt(1) +
                        10 * inn.charAt(2) +
                        3 * inn.charAt(3) +
                        5 * inn.charAt(4) +
                        9 * inn.charAt(5) +
                        4 * inn.charAt(6) +
                        6 * inn.charAt(7) +
                        8 * inn.charAt(8)
                    ) % 11) % 10
                } else if (inn.length === 12) {
                    // Для 12-значного ИНН
                    return (inn.charAt(10) == ((
                            7 * inn.charAt(0) +
                            2 * inn.charAt(1) +
                            4 * inn.charAt(2) +
                            10 * inn.charAt(3) +
                            3 * inn.charAt(4) +
                            5 * inn.charAt(5) +
                            9 * inn.charAt(6) +
                            4 * inn.charAt(7) +
                            6 * inn.charAt(8) +
                            8 * inn.charAt(9)
                        ) % 11) % 10) &&
                        (inn.charAt(11) == ((
                            3 * inn.charAt(0) +
                            7 * inn.charAt(1) +
                            2 * inn.charAt(2) +
                            4 * inn.charAt(3) +
                            10 * inn.charAt(4) +
                            3 * inn.charAt(5) +
                            5 * inn.charAt(6) +
                            9 * inn.charAt(7) +
                            4 * inn.charAt(8) +
                            6 * inn.charAt(9) +
                            8 * inn.charAt(10)
                        ) % 11) % 10)
                }
                return false;
            }
            return false;
        }
        $("#billing__inn").focusout(function (e){
            if (isInn($(this).val()) === false) {
                $(this).after('<p class="er_mes">Некорректный ИНН !</p>')
                $(this).addClass('input_error');
            }
        })
        $('#billing__inn').focusin(function (e){
            $(this).siblings('.er_mes').remove();
            $(this).removeClass('input_error');
        })
    }

    if ($('#billing_phone').length) {
        $("#billing_phone").inputmask("+7(999)999-9999");
    }

    if ($('#reg_email').length) {
        $('#reg_email').focusout(function (e){
            var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/i;
            if (!re.test($(this).val())) {
                $(this).after('<p class="er_mes">Некорректный Email !</p>')
                $(this).addClass('input_error');
            }
        })
        $('#reg_email').focusin(function (e){
            $(this).siblings('.er_mes').remove();
            $(this).removeClass('input_error');
        })
    }

    if ($('img.attachment-woocommerce_thumbnail').length !== 0) {
        if ($(window).width() <= 700) {
            $(document).find('img.attachment-woocommerce_thumbnail').each(function () {
                $(this).attr('width', '120');
                $(this).attr('height', '182');
                $(this).removeAttr('sizes');
                $(this).removeAttr('srcset');
            })
        }
    }
    $(document).on('click', '.add2Basket', function () {
        let product = $(this).closest('li.type-product');
        $.ajax({
            type: 'GET',
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'quick_shop',
                id: $(this).attr('data-product-id')
            },
            beforeSend: function () {
                $(product).append('<div  class="reload" style="position: absolute; top:0;background-color: white;' +
                    'z-index: 100000000000000000000000000000000000000000000000000000;width:' + $(product).width() + 'px;' +
                    'height: ' + $(product).height() + 'px;opacity: 0.7"></div>');
            },
            success: function (html) {
                $('.reload').remove();
                $(product).append('<div class="variation_add_basket">' + html + '</div>');
                $(product).addClass('active_block');
                let tally = 0, total = 0, price = 0;
                $(product).find('input.product-quantity').each(function () {
                    if ($(this).val()) {
                        let vals = parseInt($(this).val());
                        tally = vals + tally;
                    }
                });
                price = $(product).find('table.wholesale tr[price]').attr('price');
                total = price * tally;
                $(product).find('td.tally').text(tally);
                $(product).find('p.tally span').text(tally);
                $(product).find('p.total span').text(total);
                $(product).find('td.total').text(total);
            }
        });
    });
    $(document).on('click', '.close_window', function () {
        $(this).closest('li').find('.variation_add_basket').remove();
        $(this).closest('li').find('.active_block').removeClass('active_block');
    });


});