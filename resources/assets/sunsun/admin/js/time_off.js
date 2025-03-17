$(function () {
    (function($) {
        // Date.prototype.addDays = function(days) {
        //     var date = new Date(this.valueOf());
        //     date.setDate(date.getDate() + days);
        //     return date;
        // }
        // var today = new Date();
        // var day = today.getDay();
        // if (day == 3 || day == 4) {
        //     today = (day == 3) ? today.addDays(2) : today.addDays(1);
        // } else {
        //     first_page = false;
        //     createListKubun();
        // }
        function addDayNow(today) {
            return moment(today).add(1, 'days');
        }
        function getToDay(today) {
            var date = today.format('Y/MM/DD');
            var date_day = today.weekday();
            if (_date_enable.indexOf(date) >= 0) {
                return date;
            }
            if (_off_def.indexOf(date_day) >= 0) {
                date = getToDay(addDayNow(today));
            }
            return date;
        }
        var date = getToDay(moment())
        var today = new Date(date);
        first_page = false;
        createListKubun();
        $('#go-day').off('click');
        $('#go-day').on('click',function (e) {
            let day_url = $curent_url.substring(0, $curent_url.length - 8) + "day";
            window.location.href = day_url;
        })
        $('#go-weekly').off('click');
        $('#go-weekly').on('click',function (e) {
            let weekly_url = $curent_url.substring(0, $curent_url.length - 8) + "weekly";
            window.location.href = weekly_url;
        })
        $('#go-monthly').off('click');
        $('#go-monthly').on('click',function (e) {
            let monthly_url = $curent_url.substring(0, $curent_url.length - 8) + "monthly";
            window.location.href = monthly_url;
        })
        $('#show_current_date').datepicker({
            language: 'ja',
            weekStart: 1,
            beforeShowDay: disableDates,
            datesDisabled: _date_holiday,
            dateFormat: 'yyyy/mm/dd'
        }).on("changeDate", function(e) {
            searchDate(e.date);
            if (first_page) {
                createListKubun();
                first_page = false;
                return;
            }
            refeshPage();
            checkDateDisableButton();
        }).datepicker("setDate", today);
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
        function searchDate(date_select) {
            var _date_search = date_select.getFullYear()+"-"+(date_select.getMonth()+1)+"-"+date_select.getDate();
            $("#date_search").val(_date_search);
        }
        $(document).on("click", "#submitButton", function(e){
            e.preventDefault();
            var _data = [];
            var count_list_combo = listCombo.length;
            for (var i =0 ; i < count_list_combo ; i++) {
                var id_cmb = "#"+listCombo[i];
                if ($(id_cmb).val() == "off") {
                    _data.push(listCombo[i].replace("cmb_", ""));
                }
            }
            $.ajax({
                url: $site_url +'/admin/submit_time_off',
                type: 'POST',
                data: {
                    date_search : $("#date_search").val(),
                    data : _data
                },
                dataType: 'JSON',
                beforeSend: function () {
                    loader.css({'display': 'block'});
                },
                success: function (html) {
                    if (html.status == true) {
                        alert("保存に成功しました。");
                    }
                },
                complete: function () {
                    loader.css({'display': 'none'});
                },
            });
        });
        // function
        function createListKubun() {
            var listhtml = [];
            listCombo = [];
            // kubun_type : 1,2,3,4
            for (var i = 0; i<count_list; i++) {
                var data = list_kubun[i];
                var type = data.type_holiday;
                for (var z=0; z<1; z++) {
                    var id = type+"_"+data.time_holiday;
                    var id_cmb = "cmb_"+id;
                    var id_tr = "tr_"+type+"_"+data.time_holiday;
                    listCombo.push(id_cmb);
                    var html = " \
                            <tr id='"+id_tr+"'> \
                                <td class='name_holiday'>"
                                    + data.kubun_value +
                                "</td> \
                                <td class='value_holiday' align='right'> \
                                    <div class='checkbox-container'> \
                                        <input type='checkbox' id='"+id_cmb+"' name='"+id_cmb+"' checked> \
                                    </div> \
                                </td> \
                            </tr>";
                    if (listhtml[type] == undefined) listhtml[type] = html;
                    else listhtml[type] += html;
                    if (type == "1") {
                        type = "4";
                        z--;
                    }

                }
            }
            listhtml.forEach(_listKubun);
            listCombo.forEach(_listCombo);
        }
        function _listKubun(item, index) {
            var html = "<table>" + item + "</table>";
            $("#div"+index).html(html);
        }
        var check_roll = false;
        function _listCombo(item, index) {
            setDefaultCombo(item);
            new DG.OnOffSwitch({
                el: "#"+item,
                height: 20,
                textOn:'ON',
                textOff:'OFF',
                listener:function(name, checked){
                    if (!check_roll) {
                        var arr_date = handleDate();
                        var error_check = false;
                        if (arr_date.date_search == arr_date.date_now) {
                            var today = new Date();
                            var h = today.getHours();
                            var m = today.getMinutes();
                            if (m < 10) m = "0"+m;
                            var time_check = parseInt(""+h+m);
                            var arr_name = name.split("_");
                            var time = parseInt(arr_name[arr_name.length - 1]);
                            if (time_check >= time) {
                                error_check = true;
                            }
                        }
                        if (arr_date.date_search < arr_date.date_now || error_check) {
                            //$('.on-off-switch-track>div').attr('style','left:0px');
                            // $('.on-off-switch-thumb').addClass('disable-checkbox');
                            var kubun_type = name.split("_");
                            if (kubun_type.length > 2 && kubun_type[1] == '5') {
                                error_check = false;
                            } else {
                                if (!this.checked) {
                                    this.animateRight();
                                } else {
                                    this.animateLeft();
                                }
                                alert("休日が未来の日付のみに設定できます。");
                                check_roll = true;
                                return;
                            }
                        }
                        if (!error_check) {
                            var id_tr = "#"+name.replace("cmb_", "tr_");
                            if (!checked) {
                                $("#"+name).val("off");
                                $(id_tr).addClass("tr_disable");
                            }
                            else {
                                $("#"+name).val("on");
                                $(id_tr).removeClass("tr_disable");
                            }
                        }
                    }
                    else check_roll = false;
                }
            });
        }
        function setDefaultCombo(item) {
            var id = "#"+item;
            var arr = item.split("_");
            var type = arr[1];
            var time_holiday = arr[2];
            for (var i = 0; i<count_holiday; i++) {
                var row = list_holiday[i];
                if (row.type_holiday == type && row.time_holiday == time_holiday) {
                    $(id).click();
                    $(id).val("off");
                    var id_tr = id.replace("cmb_", "tr_");
                    $(id_tr).addClass("tr_disable");
                    break;
                }
            }
        }
        function refeshPage() {
            $.ajax({
                url: $site_url +'/admin/ajax_time_off',
                type: 'POST',
                data: {
                    date_search : $("#date_search").val()
                },
                dataType: 'JSON',
                beforeSend: function () {
                    loader.css({'display': 'block'});
                },
                success: function (html) {
                    if (html.status == true) {
                        var _data = html.data;
                        list_kubun = html.data.list_kubun;
                        list_holiday =  html.data.list_holiday;
                        count_list = list_kubun.length;
                        count_holiday = list_holiday.length;
                        createListKubun();
                    }
                },
                complete: function () {
                    loader.css({'display': 'none'});
                },
            });
        }
        // rule time off
        function checkDateDisableButton() {
            var arr_data = handleDate();
            if (arr_data.date_search < arr_data.date_now) {
                $("#submitButton").addClass('disabled');
            }else{
                $("#submitButton").removeClass('disabled');
            }
        }
        function handleDate() {
            var _current_day = new Date();
            var date_now = _current_day.toISOString().slice(0,10);
            date_now = date_now.replace(/-/g,"");
            var date_search = $('#date_search').val();
            date_search = date_search.split("-");
            var day = (date_search[2].length < 2) ? "0"+date_search[2] : date_search[2];
            var month = (date_search[1].length < 2) ? "0"+date_search[1] : date_search[1];
            date_search = date_search[0]+month+day;
            return {date_now: date_now, date_search: date_search};
        }
    })(jQuery);
});
