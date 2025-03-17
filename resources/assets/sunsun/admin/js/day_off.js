(function($) {
    var date = new Date();
    var year = date.getFullYear();
    Create_month(year);
    ClickToggleClass();
    $('#input-current__date').val(year);
    var column = $(".datepicker-switch");
    column.attr("colspan","7")
    current_day = $('#input-current__date'),
    date_day = current_day.datepicker({
        language: 'ja',
        autoclose: false,
        weekStart: 1,
        format: "yyyy",
        minViewMode: "years",
    }).on("changeYear", function(e) {
        var year = e.date.getFullYear();
        Create_month(year);
        changeYear(year);
        ClickToggleClass();
    });
    $('#button-current__date').off('click');
    $('#button-current__date').on('click', function(e) {
        $('#input-current__date').focus();
    });
    function Create_month(year) {
        for (var index = 1; index <= 12; index++) {
            if(index < 10){
                index = '0'+index;
            }
            $('#date_'+index).datepicker({
            language: 'ja',
            autoclose: false,
            weekStart: 1,
            maxViewMode: 0,
            beforeShowDay: disableDates,
            }).datepicker("setDate", new Date(year, index, null));
        }
    };
    function disableDates(date) {
        let shouldDisable = true;
        var date_day = date.getDay();
        if(_off_def.indexOf(date_day) >= 0){
            shouldDisable = false;
        }
        var string_date = jQuery.datepicker.formatDate('yy/mm/dd', date);
        if (_date_enable.indexOf(string_date) >= 0) {
            shouldDisable = true;
        }
        return shouldDisable;
    }
    function setDefaultCombo() {
        for (var i = 0; i<count_holiday; i++) {
            var value = list_holiday[i];
            var arr = value.split("/");
            var month = arr[1];
            var day = arr[2];
            var id_datepicker = "#date_"+month;
            var arr_day = $(id_datepicker).find(".day");
            for (let index = 0; index < arr_day.length; index++) {
                var check_day = arr_day[index].innerText;
                if (check_day.length < 2) {
                    check_day = '0'+check_day;
                }
                if (check_day == day && arr_day[index].className == 'day')
                {
                    var obj_dd = $(arr_day[index]).attr("data-date");
                    $("#date_"+month+" [data-date="+obj_dd+"]").addClass('active-date');
                }
            }
        }
    }
    setDefaultCombo();
    function ClickToggleClass() {
        for (var index = 1; index <= 12; index++) {
            if(index < 10){
                index = '0'+index;
            }
            $("#date_"+index+" .table-condensed td.day").on('click', function(e){
                e.preventDefault();
                e.stopPropagation(); // Ngăn chặn sự lan rộng của sự kiện hiện tại tới thằng khác.
                e.stopImmediatePropagation(); // ngăn chặn những listeners cũng đang đang lắng nghe cùng event được gọi.
                // lấy tháng hiện tại
                var year_change = $("#input-current__date").val(); // năm thay đổi
                var month_now = date.getMonth()+1;
                var day_now = date.getDate();
                var year_now = date.getFullYear(); // năm hiện tại
                if (year_change > year_now) {
                    if (!$(this).hasClass('disabled')) {
                        $(this).toggleClass('active-date').removeClass('active');
                    }
                }else if(year_change < year_now){ // kiểm tra năm change mà nhỏ hơn năm hiện tai thì báo lỗi
                    alert('休日が過去の日付に設定できません。');
                    return false;
                }else{
                    if (month_now < 10) {
                        month_now = '0'+month_now;
                    }
                    var id_datepicker = "#date_"+month_now;
                    var obj_day = $(id_datepicker).find(".day");
                    for (let index = 0; index < obj_day.length; index++) {
                        var check_day = obj_day[index].innerText;
                        if (check_day == day_now) // kiểm tra nếu ngày trong tháng bằng với ngày hiện tại
                        {
                            var data_date_now = $(obj_day[index]).attr("data-date"); // lấy data data-date
                        }
                    }
                    var data_date_click = $(this).attr("data-date"); // lấy data-date của ngày đang click
                    if (data_date_now < data_date_click) { // so sánh nếu date-date hiện tại nhỏ hơn data date click thì add class
                        if (!$(this).hasClass('disabled')) {
                            $(this).toggleClass('active-date').removeClass('active');
                        }
                    }else{ // ngược lại báo lỗi
                        alert('休日が過去の日付に設定できません。');
                        return false;
                    }
                }
            });
        }
    };
    $(document).on("click", "#submitButton", function(){
        var arr_data = [];
        var year = $("#input-current__date").val();
        for (var index = 1; index <= 12; index++) {
            if(index < 10){
                index = '0'+index;
            }
            var id_datepicker = "#date_"+index;
            var obj_active = $(id_datepicker).find(".active-date");
            for (let i = 0; i < obj_active.length; i++) {
                var day_active = obj_active[i].innerText;
                if (day_active.length < 2) {
                    day_active = '0'+day_active;
                }
                var month = index;
                var day = day_active;
                var dateFull = year+month+day;
                arr_data.push(dateFull);
            }
        }
        var data_holiday = arr_data;
        hanldSubmitAjax(year,data_holiday)
    });
    function hanldSubmitAjax(year,data) {
        $.ajax({
            url: url_submit,
            type: 'POST',
            data: {year:year, data:data},
            dataType: 'JSON',
            beforeSend: function () {
                loader.css({'display': 'block'});
            },
            success: function (html) {
                if (html.status == true) {
                    window.location.reload();
                    alert("保存に成功しました。");
                }
            },
            complete: function () {
                loader.css({'display': 'none'});
            },
        });
    }
    function changeYear(year) {
        $.ajax({
            url: url_ajax,
            type: 'POST',
            data: {
                year_search : year
            },
            dataType: 'JSON',
            beforeSend: function () {
                loader.css({'display': 'block'});
            },
            success: function (html) {
                if (html.status == true) {
                    list_holiday =  html.data.list_holiday;
                    count_holiday = list_holiday.length;
                    setDefaultCombo();
                }
            },
            complete: function () {
                loader.css({'display': 'none'});
            },
        });
}
})(jQuery);