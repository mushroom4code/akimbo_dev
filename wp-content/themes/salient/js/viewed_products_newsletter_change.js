jQuery(document).ready(function ($) {
    $('#ViewedProductsNewsletter').on('change', function () {
        $.ajax({
            type: "POST",
            url: myajax.url,
            data: { action: 'viewed_products_newsletter_change', value: event.target.checked },
            success: function (result) {
                if (result !== 'true') {
                    console.log(result);
                }
            },
        });
    });
});