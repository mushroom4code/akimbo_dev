jQuery(document).ready(function ($) {
    $('.add2Basket').on('click', function () {
        if ($(document).find('.variation_add_basket').length !== 0) {
            $(document).find('.variation_add_basket').remove();
        }
       let product =  $(this).closest('li.type-product');
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
                console.log(html)
            }
        });
    });
});