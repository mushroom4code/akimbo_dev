jQuery(function ($){
    $(document).on('click','#notisend_import_customer', function (event){

        const spinner = $('#notisend_import_customer_spinner')
        spinner.addClass("is-active");
        $.post(
            '/wp-admin/admin-ajax.php',
            {
                action: 'notisend_export_clients',
            },
            function (response){
                spinner.removeClass("is-active");
            }
        )

        event.preventDefault();
    })
})