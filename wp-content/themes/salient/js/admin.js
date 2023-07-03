

( function( $ ) {
    console.log('test')
$('div[name="sendKapsulsSubmit"]').on('click', function (e) {
    e.preventDefault();

    let form = new FormData($('form[name="sendKapsuls"]')[0]);
    console.log('test11')
    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        data: form,
        type: "POST",
        beforeSend: function () {
            console.log('testtetste')
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
} )( jQuery );