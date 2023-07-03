

( function( $ ) {
    console.log('test')
$('div[name="sendKapsulsSubmit"]').on('click', function (e) {
    e.preventDefault();

    let form = new FormData($('form[name="sendKapsuls"]')[0]);

    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        data: form,
        type: "POST",
        beforeSend: function () {
            $(document).find('.admin_fields').append('<div id="appendEtc" ' +
                'style="position:fixed; height: 100%; width: 100%; background-color: rgba(10,0,0,0.43); display: flex;' +
                'justify-content: center;align-items:center; top: 0">' +
                '<h1 style="color: white">Файл с капсулами загружается ...</h1></div>')
        },
        success: function (response) {
            $(document).find('.admin_fields #appendEtc').remove();
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
} )( jQuery );