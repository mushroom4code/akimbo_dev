jQuery(document).ready(function ($) {
    $('.add2Basket').on('click', function () {
        let product =  $(this).closest('li.type-product');
        if ($(document).find('.variation_add_basket').length !== 0) {
            $(document).find('.variation_add_basket').remove();
            $(document).find('.active_block').removeClass('active_block');
        }
        $.ajax({
            type: 'GET',
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'quick_shop',
                id: $(this).attr('data-product-id')
            },
            beforeSend: function(){
                $(product).append('<div  class="reload" style="position: absolute; top:0;background-color: white;' +
                    'z-index: 100000000000000000000000000000000000000000000000000000;width:'+$(product).width()+'px;' +
                    'height: '+$(product).height()+'px;opacity: 0.7"></div>');
            },
            success: function (html) {
                $('.reload').remove();
                $(product).append('<div class="variation_add_basket">' + html + '</div>');
                $(product).addClass('active_block');
                let tally = 0, total = 0,price = 0;
                $(document).find('input.product-quantity').each(function(){
                    if($(this).val()){
                        let vals = parseInt($(this).val());
                        tally = vals + tally;
                    }
                });
                price = $(document).find('table.wholesale tr[price]').attr('price');
                total = price * tally;
                $(document).find('td.tally').text(tally);
                $(document).find('p.tally span').text(tally);
                $(document).find('p.total span').text(total);
                $(document).find('td.total').text(total);
            }
        });
    });
    $(document).on('click','.close_window',function () {
        $(document).find('.variation_add_basket').remove();
        $(document).find('.active_block').removeClass('active_block');
    });
});