
jQuery(document).ready(function ($) {

    if ($('#billing__inn').length) {
        $("#billing__inn").inputmask("9{12}");
    }
    if ($('#billing_phone').length) {
        $("#billing_phone").inputmask("+7(999)999-9999");
    }
    if ($('#reg_email').length) {
        $('#reg_email').focusout(function (e){
            var re = /^[a-z0-9_-]+@[a-z0-9-]+\.([a-z]{1,6}\.)?[a-z]{2,6}$/i;
            if (!re.test($(this).val())) {
                $(this).after('<p id="er_email_mes">Некорректный Email !</p>')
                $(this).addClass('input_email_error');
            }
        })
        $('#reg_email').focusin(function (e){
            $(this).siblings('#er_email_mes').remove();
            $(this).removeClass('input_email_error');
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