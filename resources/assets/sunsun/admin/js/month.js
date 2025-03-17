$(function () {
    let main_head__top = $('.main-head');
    let current_day = $('#input-current__monthly');
    let date_day = current_day.datepicker({
        language: 'ja',
        format: "yyyy/mm",
        startView: "year",
        minViewMode: "months",
        autoclose: true,

    });
    $('#button-current__monthly').off('click');
    $('#button-current__monthly').on('click', function(e) {
        $('#input-current__monthly').focus();
    });
    current_day.on('input change',function (e) {
        let date = $(this).val().split('/').join('');
        window.location.href = $curent_url+"?date="+date;
    });
    if (current_day.val() === '') {
        date_day.datepicker("setDate", new Date());
        current_day.trigger("input");
    }

    main_head__top.on('click','.prev-month',function (e) {
        let date = date_day.datepicker('getDate');
        let time_moment = moment(date);
        let time_mement_prev = time_moment.subtract(1,'month').format('YYYY/MM');
        date_day.datepicker('update', time_mement_prev);

        current_day.trigger("input");
    });
    main_head__top.on('click','.next-month',function (e) {
        let date = date_day.datepicker('getDate');
        let time_moment = moment(date);
        let time_mement_next = time_moment.add(1,'month').format('YYYY/MM');
        date_day.datepicker('update', time_mement_next);
        current_day.trigger("input");
    });


    $('.table-col:not(.not-select)').off('click');
    $('.table-col:not(.not-select)').on('click',function (e) {
        let date = $(this).find('.full_date').val();
        let day_url = $curent_url.substring(0, $curent_url.length - 7) + "day";;
        window.location.href = day_url + "?date=" + date;
    });

});
