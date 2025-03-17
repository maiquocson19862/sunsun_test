$(function () {
    $('.add-new-people').click(function () {
        $.ajax({
            url: $site_url +'/add_new_booking',
            type: 'POST',
            data: $('form.booking').serializeArray(),
            dataType: 'JSON',
            beforeSend: function () {
                loader.css({'display': 'block'});
            },
            success: function (r) {
                window.location.href = $site_url +'/booking?add_new_user=on';
            },
            complete: function () {
                loader.css({'display': 'none'});
            },
        });
    });
});
