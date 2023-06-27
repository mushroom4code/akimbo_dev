(function ($) {
    console.log('test')
    $("body").on('submit', 'name="sendKapsulsSubmit"', function (e) {
        e.preventDefault();

        let form = new FormData($(this)[0]);
        console.log('test11')
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: "POST",
            data: form,
            async: true,
            beforeSend: function () {
            },
            success: function (response) {
                console.log(response)
            },
            error: function (response) {
                alert('Ошибка загрузки данных из формы');
            },
            cache: true,
            contentType: false,
            processData: false
        });
    });
})