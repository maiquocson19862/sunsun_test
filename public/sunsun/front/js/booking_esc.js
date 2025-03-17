(() => {
    function e(t) {
        return e = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
            return typeof e
        } : function (e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        }, e(t)
    }

    function t(t, a, n) {
        return (a = function (t) {
            var a = function (t, a) {
                if ("object" !== e(t) || null === t) return t;
                var n = t[Symbol.toPrimitive];
                if (void 0 !== n) {
                    var o = n.call(t, a || "default");
                    if ("object" !== e(o)) return o;
                    throw new TypeError("@@toPrimitive must return a primitive value.")
                }
                return ("string" === a ? String : Number)(t)
            }(t, "string");
            return "symbol" === e(a) ? a : String(a)
        }(a)) in t ? Object.defineProperty(t, a, {
            value: n,
            enumerable: !0,
            configurable: !0,
            writable: !0
        }) : t[a] = n, t
    }

    var a = _off_def.slice(), n = !1, o = !1, i = !1, d = "", l = !1, r = "", s = "", c = !1, u = !1;

    function m(e) {
        return !!isNaN(e.getTime())
    }

    function f(e) {
        var t = new Date(e), a = new Array("日", "月", "火", "水", "木", "金", "土");
        m(t) || (t = moment(t), $("#date").val(t.format("YYYY") + "/" + t.format("MM") + "/" + t.format("DD") + "(" + a[t.weekday()] + ")"), $("#date-value").val(t.format("YYYYMMDD")), $("#date-view").val(t.format("YYYY") + "年" + t.format("MM") + "月" + t.format("DD") + "日(" + a[t.weekday()] + ")"))
    }

    function _(e, t) {
        var a = new Date(e), n = new Array("日", "月", "火", "水", "木", "金", "土");
        m(a) || (a = moment(a), $(t).val(a.format("YYYY") + "/" + a.format("MM") + "/" + a.format("DD")), $(t + "-value").val(a.format("YYYYMMDD")), $(t + "-view").val(a.format("YYYY") + "年" + a.format("MM") + "月" + a.format("DD") + "日(" + n[a.weekday()] + ")"))
    }

    function p(e) {
        var t = new Date(e), a = "" + (t.getMonth() + 1), n = "" + t.getDate(), o = t.getFullYear();
        return a.length < 2 && (a = "0" + a), n.length < 2 && (n = "0" + n), [o, a, n].join("/")
    }

    function v() {
        if (window.location.href.indexOf("admin") >= 0) {
            var e = $("#div_pet_plus");
            void 0 !== e && e.show(), document.getElementById("div_pet").style.display = "", $(".option_hd").removeClass("option_hd")
        }
    }

    function h(e) {
        return moment(e).add(1, "days")
    }

    function w(e) {
        var t = e.weekday(), a = e.format("Y/MM/DD");
        if (_date_enable.indexOf(a) >= 0) return e;
        _off_def.indexOf(t) >= 0 && (e = w(h(e)));
        a = e.format("Y/MM/DD");
        return _date_holiday.indexOf(a) >= 0 && (e = w(h(e))), e
    }

    Date.prototype.addDays = function (e) {
        var t = new Date(this.valueOf());
        return t.setDate(t.getDate() + e), t
    }, $((function () {
        var e = 0, t = $("#modal_confirm");
        window.onpopstate = function (t) {
            !1 === t.state.booking && 0 !== e ? $("#back_2_booking").submit() : (e++, history.forward())
        }, history.pushState({booking: !1}, "Not checked", ""), history.pushState({booking: !0}, "Checked", ""), history.back();
        var h = $("#choice_date_time"), S = ["日", "月", "火", "水", "木", "金", "土"], z = w(moment()),
            j = (moment(z).add(1, "days"), function (e, t, a, n) {
                $("#bus_arrive_time_slide").closest("button").css({border: "solid 1px #ced4da"}), "" == e && (e = $("#course").val());
                var o = {service: e, course_data: t, course_time: a, pop_data: n};
                "on" == $("input[name=add_new_user]").val() && (o.add_new_user = "on"), $.ajax({
                    url: $site_url + "/get_service", type: "POST", data: o, dataType: "text", beforeSend: function () {
                        loader.css({display: "block"})
                    }, success: function (e) {
                        $(".service-warp").empty().append(e).hide().fadeIn("slow"), I(!0), C(), J(!0), function () {
                            var e = document.getElementById("course");
                            if (null == e || null == e) return;
                            var t = JSON.parse(e.value);
                            if ("05" == t.kubun_id) return;
                            var a = document.getElementById("lunch"), n = document.getElementById("whitening"),
                                o = document.getElementById("whitening2"), i = document.getElementById("core_tuning"),
                                d = document.getElementById("pet_keeping"),
                                l = document.getElementById("lunch_guest_num"), r = "01", s = "01", c = "01", u = "01",
                                m = "01", f = "01";
                            if ("01" == t.kubun_id || "08" == t.kubun_id || "09" == t.kubun_id) {
                                if (null == a || null == a) return;
                                r = B(a)
                            }
                            if ("01" == t.kubun_id || "02" == t.kubun_id || "03" == t.kubun_id || "07" == t.kubun_id || "08" == t.kubun_id || "09" == t.kubun_id || "10" == t.kubun_id) {
                                if (null == n || null == n) return;
                                if (null == o || null == o) return;
                                if (null == i || null == i) return;
                                s = B(n), m = B(o), f = B(i)
                            }
                            if ("03" == t.kubun_id) {
                                if (null == l || null == l) return;
                                u = B(l)
                            }
                            if (null == d || null == d) return;
                            c = B(d), ("01" != r || "01" != s || "01" != u || "01" != c || "01" != m || "01" != f) && Y("btn-collapse-between")
                        }(), null != document.getElementById("gender") && "" != _sex_admin && (document.getElementById("gender").selectedIndex = _sex_admin), "undefined" == typeof _date_admin || "" == _date_admin || l || ("05" == _type_admin ? $(".js-set-room_pet").click() : $(".js-set-time").click()), void 0 !== $("#date").val() && "" != r && f(r), "", "", r = "", function () {
                            if ($(".add-new-people").length > 0) {
                                var e = JSON.parse($("#course").val());
                                "08" == e.kubun_id || "09" == e.kubun_id ? $(".add-new-people").parent().hide() : $(".add-new-people").parent().show()
                            }
                        }()
                    }, complete: function () {
                        loader.css({display: "none"}), v()
                    }, error: function (e) {
                        419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                            target: "#edit_booking",
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })) : Swal.fire({
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })))
                    }
                })
            });
        $("#btn-home").off("click"), $("#btn-home").on("click", (function () {
            var e = window.location.origin + "/clear_session";
            window.location.href = e
        })), $("#btn-back").off("click"), $("#btn-back").on("click", (function () {
            history.back()
        })), t.off("shown.bs.modal"), t.on("shown.bs.modal", (function (e) {
            $("#confirm_cancel").off("click"), $("#confirm_cancel").on("click", (function (e) {
                e.preventDefault(), t.modal("hide")
            })), $("#confirm_ok").off("click"), $("#confirm_ok").on("click", (function (e) {
                e.preventDefault(), i = !0, t.modal("hide")
            })), $("#edit_booking .modal-dialog").css("opacity", .7)
        })), t.off("hidden.bs.modal"), t.on("hidden.bs.modal", (function (e) {
            i ? j($("#course").val(), $("#course_data").val(), $("#course_time").val(), $("#pop_data").val()) : (console.log("course_tmp"), console.log(d), $("#course").val(d)), $("#edit_booking .modal-dialog").css("opacity", 1), $("body").addClass("modal-open")
        })), $("#course").off("focusin"), $("#course").on("focusin", (function () {
            d = $("#course").val()
        })), $("#course").off("change"), $("#course").on("change", (function () {
            $("#course").val();
            void 0 !== $("#date").val() && "" == r && ($("#date-view").val(), $("#date-value").val(), r = $("#date").val(), s = r), "undefined" != typeof _date_admin && "" != _date_admin || !o || null === document.getElementById("booking_id") ? j($("#course").val(), $("#course_data").val(), $("#course_time").val(), $("#pop_data").val()) : (i = !1, t.modal({
                show: !0,
                backdrop: !1,
                keyboard: !1
            }))
        })), "undefined" != typeof _date_admin && "" != _date_admin || j($("#pick_course").val(), $("#course_data").val(), $("#course_time").val(), $("#pop_data").val());
        var I = function () {
            var e;
            (e = z.format("Y") + "/" + z.format("MM") + "/" + z.format("DD"), "04" == JSON.parse($("#course").val()).kubun_id || "06" == JSON.parse($("#course").val()).kubun_id) && ("" == $("#plan_date_start").val() && _("" != r ? r : "" != s ? s : e, "#plan_date_start"));

            function t() {
                var e = arguments.length > 0 && void 0 !== arguments[0] && arguments[0],
                    t = arguments.length > 1 && void 0 !== arguments[1] && arguments[1], a = _off_def.slice();
                return void 0 !== $("#course") && null != $("#course") && (!0 === t && a.push(2), !1 === e && $("#course").val().indexOf('"kubun_id":"03"') >= 0 && (a.push(6), a.push(0))), a
            }

            function i(e) {
                return a = t(), m(e)
            }

            function d(e) {
                return a = t(!0), m(e)
            }

            function l(e) {
                return a = t(!0, !0), m(e)
            }

            function m(e) {
                var t = !0, n = e.getDay();
                a.indexOf(n) >= 0 && (t = !1);
                var o = jQuery.datepicker.formatDate("yy/mm/dd", e);
                return _date_enable.indexOf(o) >= 0 && (t = !0), 0 == t && 2 == n && (e = e.addDays(1), o = jQuery.datepicker.formatDate("yy/mm/dd", e), _date_enable.indexOf(o) >= 0 && (t = !0)), t
            }

            if ("" == $("#date").val() && void 0 !== $("#date").val() && f(e), $("#room").off("change"), $("#room").on("change", (function () {
                var e = JSON.parse($("#room").val());
                $("#range_date_start").removeClass("validate_failed"), $("#range_date_end").removeClass("validate_failed"), $("p.note-error").remove(), "01" == e.kubun_id ? $(".room").hide() : $.ajax({
                    url: $site_url + "/get_free_room",
                    type: "POST",
                    data: {room: e.kubun_id},
                    dataType: "text",
                    beforeSend: function () {
                        loader.css({display: "block"})
                    },
                    success: function (e) {
                        e = JSON.parse(e), $("#range_date_start").datepicker("destroy"), $("#range_date_end").datepicker("destroy"), $("#range_date_start").val("－"), $("#range_date_end").val("－"), window.location.href.indexOf("admin") >= 0 ? ($("#range_date_start").datepicker({
                            container: ".mail-booking",
                            language: "ja",
                            dateFormat: "yyyy/mm/dd",
                            autoclose: !0,
                            startDate: new Date,
                            beforeShowDay: l,
                            datesDisabled: e.date_selected,
                            weekStart: 1,
                            orientation: "bottom"
                        }), $("#range_date_end").datepicker({
                            container: ".mail-booking",
                            language: "ja",
                            dateFormat: "yyyy/mm/dd",
                            autoclose: !0,
                            startDate: new Date,
                            beforeShowDay: d,
                            datesDisabled: e.date_selected_end,
                            weekStart: 1,
                            orientation: "bottom"
                        })) : ($("#range_date_start").datepicker({
                            language: "ja",
                            dateFormat: "yyyy/mm/dd",
                            autoclose: !0,
                            startDate: new Date,
                            beforeShowDay: l,
                            datesDisabled: e.date_selected,
                            weekStart: 1,
                            orientation: "bottom"
                        }), $("#range_date_end").datepicker({
                            language: "ja",
                            dateFormat: "yyyy/mm/dd",
                            autoclose: !0,
                            startDate: new Date,
                            beforeShowDay: d,
                            datesDisabled: e.date_selected_end,
                            weekStart: 1,
                            orientation: "bottom"
                        })), $("#range_date_start").datepicker().on("hide", (function () {
                            $("#range_date_end").val("－")
                        }));
                        var t = moment(new Date($("#range_date_start").val())),
                            a = moment(new Date($("#range_date_end").val()));
                        $("#range_date_start-view").val(t.format("YYYY") + "年" + t.format("MM") + "月" + t.format("DD") + "日(" + S[t.weekday()] + ")"), $("#range_date_end-view").val(a.format("YYYY") + "年" + a.format("MM") + "月" + a.format("DD") + "日(" + S[a.weekday()] + ")"), $("#range_date_start-value").val(t.format("YYYYMMDD")), $("#range_date_end-value").val(a.format("YYYYMMDD")), O(), $(".room").show()
                    },
                    complete: function () {
                        loader.css({display: "none"}), v()
                    },
                    error: function (e) {
                        419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                            target: "#edit_booking",
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })) : Swal.fire({
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })))
                    }
                })
            })), $("#whitening").off("change"), $("#whitening").on("change", (function () {
                "01" == JSON.parse($("#whitening").val()).kubun_id ? $(".whitening").hide() : $(".whitening").show()
            })), $("#whitening2").off("change"), $("#whitening2").on("change", (function () {
                "01" == JSON.parse($("#whitening2").val()).kubun_id ? $(".whitening2").hide() : $(".whitening2").show()
            })), DatePicker = {
                hideOldDays: function () {
                    var e = $("td.old.day");
                    e.length > 0 && (e.css("visibility", "hidden"), 7 === e.length && e.parent().hide())
                }, hideNewDays: function () {
                    var e = $("td.new.day");
                    e.length > 0 && e.hide()
                }, hideOtherMonthDays: function () {
                    DatePicker.hideOldDays(), DatePicker.hideNewDays()
                }
            }, window.location.href.indexOf("admin") >= 0 ? ($("#date").datepicker({
                container: ".mail-booking",
                language: "ja",
                dateFormat: "yyyy/mm/dd",
                startDate: new Date,
                autoclose: !0,
                beforeShowDay: i,
                datesDisabled: _date_holiday,
                weekStart: 1,
                orientation: "bottom"
            }), "undefined" != typeof _date_admin && "" != _date_admin && (o || function () {
                if ("05" == _type_admin) {
                    var e = document.getElementById("course");
                    if (null == e || null == e) return;
                    for (var t = e.length, a = 0; a < t; a++) if ("05" == JSON.parse(e[a].value).kubun_id) {
                        e.value = e[a].value;
                        break
                    }
                } else _type_admin = "01";
                $("#course").change()
            }(), void 0 !== $("#date") && null != $("#date") && f(_date_admin)), o = !0) : $("#date").datepicker({
                language: "ja",
                dateFormat: "yyyy/mm/dd",
                startDate: new Date,
                autoclose: !0,
                beforeShowDay: i,
                datesDisabled: _date_holiday,
                weekStart: 1,
                orientation: "bottom"
            }), "03" == JSON.parse($("#course").val()).kubun_id) {
                var h = $("#date").val(), w = new Date(h), g = w.getDay();
                6 == g ? h = p(w.addDays(2)) : 0 == g && (h = p(w.addDays(1))), f(h)
            }
            if ($("#date").datepicker().off("hide"), $("#date").datepicker().on("hide", (function (e) {
                $("#error_time_0").val("－"), $("#time\\[0\\]\\[value\\]").val(0), $("#time\\[0\\]\\[bed\\]").val(0), $("#time\\[0\\]\\[json\\]").val(""), $(".time-content").empty(), $("#time1-value").val(0), $("#time1-bed").val(0), $("#time1-view").val("－"), $("#time\\[0\\]\\[json\\]").val(""), $("#time2-value").val(0), $("#time2-bed").val(0), $("#time2-view").val("－"), $("#time\\[1\\]\\[json\\]").val(""), $("#time_room_value").val(0), $("#time_room_bed").val(0), $("#time_room_view").val("－"), $("#time\\[0\\]\\[json\\]").val(""), $("#time_room_time1").val("0"), $("#time_room_time2").val("0"), $("#time_room_pet_0").val("－"), $("#time_room_pet_json").val(""), $("#whitening-time_view").val("－"), $("#whitening-time_value").val("0"), $("#whitening_data\\[json\\]").val(""), f($("#date").val())
            })), $("#date").datepicker().on("show", (function (e) {
                DatePicker.hideOtherMonthDays()
            })), n && void 0 !== $("#date").val() && "" != $("#date").val()) {
                var C = moment(new Date($("#date").val()));
                C.format("Y"), C.format("MM"), C.format("DD"), C.weekday();
                f($("#date").val())
            }
            var B = void 0 !== $("#room").val() ? JSON.parse($("#room").val()) : void 0;
            null != B && ("01" == B.kubun_id ? $(".room").hide() : $.ajax({
                url: $site_url + "/get_free_room",
                type: "POST",
                data: {room: B.kubun_id},
                dataType: "text",
                beforeSend: function () {
                    loader.css({display: "block"})
                },
                success: function (e) {
                    e = JSON.parse(e), $("#range_date_start").datepicker("destroy"), $("#range_date_end").datepicker("destroy"), window.location.href.indexOf("admin") >= 0 ? ($("#range_date_start").datepicker({
                        container: ".mail-booking",
                        language: "ja",
                        dateFormat: "yyyy/mm/dd",
                        autoclose: !0,
                        startDate: new Date,
                        beforeShowDay: l,
                        datesDisabled: e.date_selected,
                        weekStart: 1,
                        orientation: "bottom"
                    }), $("#range_date_end").datepicker({
                        container: ".mail-booking",
                        language: "ja",
                        dateFormat: "yyyy/mm/dd",
                        autoclose: !0,
                        startDate: new Date,
                        beforeShowDay: d,
                        datesDisabled: e.date_selected_end,
                        weekStart: 1,
                        orientation: "bottom"
                    })) : ($("#range_date_start").datepicker({
                        language: "ja",
                        dateFormat: "yyyy/mm/dd",
                        autoclose: !0,
                        startDate: new Date,
                        beforeShowDay: l,
                        datesDisabled: e.date_selected,
                        weekStart: 1,
                        orientation: "bottom"
                    }), $("#range_date_end").datepicker({
                        language: "ja",
                        dateFormat: "yyyy/mm/dd",
                        autoclose: !0,
                        startDate: new Date,
                        beforeShowDay: d,
                        datesDisabled: e.date_selected_end,
                        weekStart: 1,
                        orientation: "bottom"
                    })), $("#range_date_start").datepicker().on("hide", (function () {
                        $("#range_date_end").val("－")
                    }));
                    var t = moment(new Date($("#range_date_start").val())),
                        a = moment(new Date($("#range_date_end").val()));
                    $("#range_date_start-view").val(t.format("YYYY") + "年" + t.format("MM") + "月" + t.format("DD") + "日(" + S[t.weekday()] + ")"), $("#range_date_end-view").val(a.format("YYYY") + "年" + a.format("MM") + "月" + a.format("DD") + "日(" + S[a.weekday()] + ")"), $("#range_date_start-value").val(t.format("YYYYMMDD")), $("#range_date_end-value").val(a.format("YYYYMMDD")), O()
                },
                complete: function () {
                    loader.css({display: "none"}), v()
                },
                error: function (e) {
                    419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                        target: "#edit_booking",
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })) : Swal.fire({
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })))
                }
            }));
            var Y = function () {
                var e = moment(new Date($("#plan_date_start").val()));
                return 5 == e.weekday() ? e.add(4, "days").toDate() : e.add(6, "days").toDate()
            };
            window.location.href.indexOf("admin") >= 0 ? $("#plan_date_start").datepicker({
                container: ".mail-booking",
                language: "ja",
                dateFormat: "yyyy/mm/dd",
                autoclose: !0,
                startDate: new Date,
                beforeShowDay: i,
                datesDisabled: _date_holiday,
                weekStart: 1,
                orientation: "bottom"
            }) : $("#plan_date_start").datepicker({
                language: "ja",
                dateFormat: "yyyy/mm/dd",
                autoclose: !0,
                startDate: new Date,
                beforeShowDay: i,
                datesDisabled: _date_holiday,
                weekStart: 1,
                orientation: "bottom"
            }), window.location.href.indexOf("admin") >= 0 ? $("#plan_date_end").datepicker({
                container: ".mail-booking",
                language: "ja",
                dateFormat: "yyyy/mm/dd",
                autoclose: !0,
                startDate: new Date,
                endDate: Y(),
                dbeforeShowDay: i,
                datesDisabled: _date_holiday,
                weekStart: 1,
                orientation: "bottom"
            }) : $("#plan_date_end").datepicker({
                language: "ja",
                dateFormat: "yyyy/mm/dd",
                autoclose: !0,
                startDate: new Date,
                endDate: Y(),
                beforeShowDay: i,
                datesDisabled: _date_holiday,
                weekStart: 1,
                orientation: "bottom"
            });
            var M = [];
            $("#range_date_start").datepicker().on("hide", (function () {
            })), $("#plan_date_start").datepicker().on("hide", (function () {
                $("#plan_date_end").datepicker("destroy"), -1 == $.inArray(moment(new Date($("#plan_date_start").val())).format("YYYY-MM-DD"), M) && _($("#plan_date_start").val(), "#plan_date_end"), window.location.href.indexOf("admin") >= 0 ? $("#plan_date_end").datepicker({
                    container: ".mail-booking",
                    language: "ja",
                    dateFormat: "yyyy/mm/dd",
                    autoclose: !0,
                    startDate: new Date($("#plan_date_start").val()),
                    endDate: Y(),
                    beforeShowDay: i,
                    datesDisabled: _date_holiday,
                    weekStart: 1,
                    orientation: "bottom"
                }) : $("#plan_date_end").datepicker({
                    language: "ja",
                    dateFormat: "yyyy/mm/dd",
                    autoclose: !0,
                    startDate: new Date($("#plan_date_start").val()),
                    endDate: Y(),
                    beforeShowDay: i,
                    datesDisabled: _date_holiday,
                    weekStart: 1,
                    orientation: "bottom"
                }), $("#plan_date_end").focus()
            })), $("#plan_date_start").datepicker().on("changeDate", (function (e) {
                u = !0
            }));
            var j = function (e) {
                var t = x($("#plan_date_start").val(), $("#plan_date_end").val());
                t.length > 5 && ($("#plan_date_end").val(t[4]), t.splice(5, 2)), t.forEach((function (a, n) {
                    var o = moment(a + " 00:00 +0000", "YYYY-MM-DD HH:mm Z").utc().format("X") + "000";
                    0 == n || n == t.length - 1 ? (e && 0 == n || !e && n == t.length - 1 ? $("td[data-date='" + o + "']").css("background-image", "linear-gradient(to bottom, #08c, #0044cc)") : $("td[data-date='" + o + "']").css("background", "#9e9e9e"), $("td[data-date='" + o + "']").css("color", "#fff")) : ($("td[data-date='" + o + "']").css("background", "#eee"), $("td[data-date='" + o + "']").css("border-radius", "unset"), $("td[data-date='" + o + "']").css("color", "#212529"))
                }))
            };
            $("#plan_date_start").datepicker().on("show", (function (e) {
                u = !1, DatePicker.hideOtherMonthDays(), j(!0)
            })), $("#plan_date_end").datepicker().on("show", (function (e) {
                c = !1, DatePicker.hideOtherMonthDays(), j(!1)
            })), $("#plan_date_end").datepicker().on("hide", (function (e) {
                (M = x($("#plan_date_start").val(), $("#plan_date_end").val())).length > 5 && ($("#plan_date_end").val(M[4]), M.splice(5, 2)), (c || u) && (r = $("#plan_date_start").val(), u && $(".range_date").change(), T()), c = !1, u = !1
            })), $("#plan_date_end").datepicker().on("changeDate", (function (e) {
                c = !0
            })), $("#range_date_start, #range_date_end").datepicker().on("show", (function (e) {
                DatePicker.hideOtherMonthDays()
            })), $(".agecheck").on("click", (function () {
                $(".agecheck").removeClass("color-active"), $(".agecheck").addClass("btn-outline-warning"), $(this).addClass("color-active"), $(this).removeClass("btn-outline-warning"), $("#agecheck").val($(this).val()), "1" == $(this).val() || "2" == $(this).val() ? $(".age-left").css("visibility", "hidden") : "3" == $(this).val() && $(".age-left").css("visibility", "visible")
            })), $(".room_range_date").on("change blur", (function () {
                var e = moment(new Date($("#range_date_start").val())),
                    t = moment(new Date($("#range_date_end").val()));
                $("#range_date_start-view").val(e.format("YYYY") + "年" + e.format("MM") + "月" + e.format("DD") + "日(" + S[e.weekday()] + ")"), $("#range_date_end-view").val(t.format("YYYY") + "年" + t.format("MM") + "月" + t.format("DD") + "日(" + S[t.weekday()] + ")"), $("#range_date_start-value").val(e.format("YYYYMMDD")), $("#range_date_end-value").val(t.format("YYYYMMDD"))
            })), b(), y(), k(), D(), O(), n = !1
        };
        h.on("show.bs.modal", (function (e) {
            $(".modal .modal-dialog").attr("class", "modal-dialog modal-dialog-centered zoomIn  animated faster")
        })), h.on("hide.bs.modal", (function (e) {
            window.location.href.indexOf("admin")
        }));
        var N = function () {
            window.location.href.indexOf("admin") >= 0 ? $(".modal .modal-dialog").first().attr("class", "modal-dialog  modal-dialog-centered  zoomOut  animated faster") : ($(".modal .modal-dialog").attr("class", "modal-dialog  modal-dialog-centered  zoomOut  animated faster"), $(".modal-backdrop.show").css("opacity", "0")), setTimeout((function () {
                h.modal("hide")
            }), 500)
        };
        $("#confirm_cancel").off("click"), $("#confirm_cancel").on("click", (function (e) {
            e.preventDefault(), i = !1, t.modal("hide")
        })), $("#confirm_ok").off("click"), $("#confirm_ok").on("click", (function (e) {
            e.preventDefault(), i = !0, t.modal("hide")
        })), $("#btn-cancel").off("click"), $("#btn-cancel").on("click", (function (e) {
            N()
        })), h.off("hidden.bs.modal"), h.on("hidden.bs.modal", (function () {
            h.find(".modal-body-time").empty(), $(".set-time").removeClass("edit"), window.location.href.indexOf("admin") >= 0 && ($("#edit_booking").css("z-index", ""), $("body").addClass("modal-open"))
        })), h.draggable({handle: ".title-table-time"}), h.off("click", "#js-save-time"), h.on("click", "#js-save-time", (function (e) {
            var t = h.find("input[name=time]:checked").val(),
                a = h.find("input[name=time]:checked").closest("div").find(".bed").val(),
                n = h.find("input[name=time]:checked").closest("div").find("input[name=data-json]").val(),
                o = $(".booking-time").length, i = t.replace(/[^0-9]/g, "");
            if (1 == $("#new-time").val()) {
                var d = $('<div class="block-content-1 margin-top-mini"> <div class="block-content-1-left"><div class="timedate-block set-time">    <input name="time[' + o + '][view]" type="text" class="form-control time js-set-time booking-time bg-white" id="error_time_' + o + '" readonly="readonly"  value="" /><input name="time[' + o + '][value]" class="time_value" id="time[' + o + '][value]" type="hidden" value=""><input name="time[' + o + '][bed]" class="time_bed" id="time[' + o + '][bed]" type="hidden" value=""><input name="time[' + o + '][json]" class="data-json_input" id="time[' + o + '][json]" type="hidden" ><input name="time[' + o + '][element]" id="" type="hidden" value="error_time_' + o + '"></div> </div> <div class="block-content-1-right"><img class="svg-button" src="/sunsun/svg/close.svg" alt="Close" /></div>           </div>');
                d.find(".data-json_input").val(n), $(".time-content").append(d), g(), b(), $(".booking-time").last().val(t), $(".time_value").last().val(i), $(".time_bed").last().val(a)
            } else $(".set-time.edit input.time").val(t), $(".set-time.edit input.time").parent().find(".time_value").val(i), $(".set-time.edit input.time").parent().find(".time_bed").val(a);

            function l(e, t) {
                return (e += "").length >= t ? e : new Array(t - e.length + 1).join("0") + e
            }

            if ($(".set-time.edit input.time").parent().find("#time1-value").val(i), $(".set-time.edit input.time").parent().find("#time2-value").val(i), $(".set-time.edit input.time").parent().find("#time1-bed").val(a), $(".set-time.edit input.time").parent().find("#time2-bed").val(a), $(".set-time.edit input.time").parent().find("#time_room_value").val(l(i, 4)), $(".set-time.edit input.time").parent().find("#time_room_bed").val(a), $(".set-time.edit input.time").parent().find(".time_from").val(i), $(".set-time.edit input.time").parent().find(".time_to").val(i), $(".set-time.edit input.time").parent().find(".time_bed").val(a), $(".set-time.edit input.time").parent().find(".data-json_input").val(n), t.indexOf("～") >= 0) {
                var r = t.split("～");
                $(".set-time.edit input.time").parent().find("#time_room_time1").val(l(r[0].replace(/[^0-9]/g, ""), 4)), $(".set-time.edit input.time").parent().find("#time_room_time2").val(l(r[1].replace(/[^0-9]/g, ""), 4)), $(".set-time.edit input.time").parent().find("#whitening-time_value").val(l(r[0].replace(/[^0-9]/g, ""), 4) + "-" + l(r[1].replace(/[^0-9]/g, ""), 4))
            }
            N()
        })), $("li.dropdown-item").off("click"), $("li.dropdown-item").on("click", (function () {
            $("li.dropdown-item").removeClass("active"), $(this).addClass("active"), $("#bus_arrive_time_slide").val($(this).find(".bus_arrive_time_slide").val()), $("#bus_time_first").text($(this).find(".bus_time_first").text()), $("#bus_time_second").text($(this).find(".bus_time_second").text()), T()
        })), $("#repeat_user").off("change"), $("#repeat_user").on("change", (function (e) {
            "01" == JSON.parse($("#repeat_user").val()).kubun_id ? $("#hint-repeat").text("※バスの場合、到着時間から30分以内の予約はできません。希望時間が選択できない場合は　バス到着時間をご確認ください。") : $("#hint-repeat").text("※バスの場合、到着時間から15分以内の予約はできません。希望時間が選択できない場合は　バス到着時間をご確認ください。"), T()
        })), $("#transport").off("change"), $("#transport").on("change", (function (e) {
            "01" == JSON.parse($("#transport").val()).kubun_id ? $(".bus").hide() : $(".bus").show(), T()
        })), $(".btn-booking").off("click"), $(".btn-booking").on("click", (function (e) {
            var t;
            if (e.preventDefault(), "03" == JSON.parse($("#course").val()).kubun_id) {
                $("#date").removeClass("validate_failed");
                var a = $("#date-value").val();
                null == a && (a = $("#date-value-new").val());
                var n = !1;
                if (void 0 !== a && a.length >= 8 && (a = a.substring(0, 4) + "/" + a.substring(4, 6) + "/" + a.substring(6, 8), n = !m(t = new Date(a)) && 6 != (t = moment(t)).weekday() && 0 != t.weekday()), !n) return void $("#date").addClass("validate_failed")
            }
            if ($("p.note-error").remove(), "0" === $("select[name=gender]").val()) $("select[name=gender]").addClass("validate_failed"), $("select[name=gender]").after('<p class="note-error node-text">性別が空白できません。</p>'); else if (void 0 === $("#room").val() || "01" == JSON.parse($("#room").val()).kubun_id || "" != $("#range_date_start").val() && "－" != $("#range_date_start").val() && "" != $("#range_date_end").val() && "－" != $("#range_date_end").val()) {
                $("select[name=gender]").removeClass("validate_failed");
                var o = $(this);
                $.ajax({
                    url: $site_url + "/save_booking",
                    type: "POST",
                    data: $("form.booking").serializeArray(),
                    dataType: "JSON",
                    beforeSend: function () {
                        loader.css({display: "block"})
                    },
                    success: function (e) {
                        "OK" == e.status ? o.hasClass("add-new-people") ? window.location.href = $site_url + "/booking?add_new_user=on" : window.location.href = $site_url + "/confirm" : M(e)
                    },
                    complete: function () {
                        loader.css({display: "none"})
                    },
                    error: function (e) {
                        419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                            target: "#edit_booking",
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })) : Swal.fire({
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })))
                    }
                })
            } else {
                $("#range_date_start").addClass("validate_failed"), $("#range_date_end").addClass("validate_failed");
                $("#range_date_start").parent().parent().after('<p class="note-error node-text booking-laber-padding"> 宿泊日は空白できません。</p>')
            }
        }));
        var J = function () {
            var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : null;
            $(".time-list").attr("value");
            $(".range_date").change((function () {
                var e = x($("#plan_date_start").val(), $("#plan_date_end").val());
                e.length > 5 && (e.splice(5, 2), $("#plan_date_end").val(e[4])), $(".time-list").empty(), moment.locale("ja"), e.forEach((function (e, t) {
                    var a = moment(e), n = a.format("YYYY"), o = a.format("MM"), i = a.format("DD"), d = a.weekday(),
                        l = "";
                    0 === t && (l = '<input class="bus_first" type="hidden" value="1">'), $(".time-list").append('<div class="booking-field choice-time"><input value="' + t + '" class="time_index" type="hidden" ><div class="booking-field-label label-data pt-2"><label class="">' + o + "/" + i + "(" + S[d] + ')</label><input name="date[' + t + '][day][view]" value="' + o + "/" + i + "(" + S[d] + ')" type="hidden" ><input name="date[' + t + '][day][value]" value="' + n + o + i + '" type="hidden" ></div>    <div class="booking-field-content date-time"><div class="choice-data-time set-time time-start">    <div class="set-time"><input name="date[' + t + '][from][value]" type="hidden" class="time_from time_value"  readonly="readonly"  value="0" /><input name="date[' + t + '][from][bed]" type="hidden" class="time_bed"  readonly="readonly"  value="1" /><input name="date[' + t + '][from][view]" type="text" id="time_bath_' + t + '1"  class="time form-control js-set-time bg-white" data-date_value="' + n + o + i + '" data-date_type="form"  readonly="readonly"  value="－" />    <input name="time[' + t + '][from][element]" type="hidden" value="time_bath_' + t + '1" />' + l + '<input name="time[' + t + '][from][json]" type="hidden" class="data-json_input" value="" /></div>    <div class="icon-time mt-1"></div></div><div class="choice-data-time set-time time-end">    <div class="set-time"><input name="date[' + t + '][to][value]" type="hidden" class="time_to time_value"  readonly="readonly"  value="0" /><input name="date[' + t + '][to][bed]" type="hidden" class="time_bed"  readonly="readonly"  value="1" /><input name="time[' + t + '][to][json]" type="hidden" class="data-json_input" value="" /><input name="time[' + t + '][to][element]" type="hidden" value="time_bath_' + t + '2" />' + l + '<input name="date[' + t + '][to][view]" type="text" id="time_bath_' + t + '2" class="time form-control js-set-time bg-white" data-date_value="' + n + o + i + '" data-date_type="to"  readonly="readonly"  value="－" />    </div>    <div class="icon-time mt-1"></div></div>    </div></div>')
                }));
                var t = moment(new Date($("#plan_date_start").val())), a = moment(new Date($("#plan_date_end").val()));
                $("#plan_date_start-value").val(t.format("YYYY") + t.format("MM") + t.format("DD")), $("#plan_date_end-value").val(a.format("YYYY") + a.format("MM") + a.format("DD")), $("#plan_date_start-view").val(t.format("YYYY") + "年" + t.format("MM") + "月" + t.format("DD") + "日(" + S[t.weekday()] + ")"), $("#plan_date_end-view").val(a.format("YYYY") + "年" + a.format("MM") + "月" + a.format("DD") + "日(" + S[a.weekday()] + ")"), I()
            })), null == e && I()
        };
        J()
    }));
    var g = function () {
        $(".svg-button").off("click"), $(".svg-button").on("click", (function () {
            var e = this;
            window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                target: "#edit_booking",
                text: "予約時間を削除します、よろしいですか?",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#d7751e",
                cancelButtonColor: "#343a40",
                confirmButtonText: "はい",
                cancelButtonText: "いいえ",
                width: 350,
                showClass: {popup: "animated zoomIn faster"},
                hideClass: {popup: "animated zoomOut faster"},
                allowOutsideClick: !1
            }).then((function (t) {
                t.value && $($(e).parent().parent().remove())
            })) : Swal.fire({
                text: "予約時間を削除します、よろしいですか?",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#d7751e",
                cancelButtonColor: "#343a40",
                confirmButtonText: "はい",
                cancelButtonText: "いいえ",
                width: 350,
                showClass: {popup: "animated zoomIn faster"},
                hideClass: {popup: "animated zoomOut faster"},
                allowOutsideClick: !1
            }).then((function (t) {
                t.value && $($(e).parent().parent().remove())
            }))
        }))
    };
    var b = function () {
        var e = $("#choice_date_time"), t = $(".js-set-time");
        t.off("click"), t.on("click", (function () {
            var t = $(this).parent().find(".bus_first").val(), a = $(this);
            if (void 0 !== t ? $("#bus_first").val(1) : $("#bus_first").val(0), $("p.note-error").remove(), "0" === $("select[name=gender]").val()) $("select[name=gender]").addClass("validate_failed"), $("select[name=gender]").after('<p class="note-error node-text">性別が空白できません。</p>'); else if (void 0 === $("#room").val() || "01" == JSON.parse($("#room").val()).kubun_id || "" != $("#range_date_start").val() && "－" != $("#range_date_start").val() && "" != $("#range_date_end").val() && "－" != $("#range_date_end").val()) {
                $("select[name=gender]").removeClass("validate_failed");
                var n = $("form.booking").serializeArray(), o = {name: "data_get_attr"}, i = {};
                i.date = a.attr("data-date_value"), i.date_type = a.attr("data-date_type"), o.value = JSON.stringify(i), n.push(o);
                var d = this.name, l = d.replace("view", "value");
                $.each(n, (function () {
                    this.name != d && this.name != l || (this.value = "")
                })), $.ajax({
                    url: "/get_time_room", type: "POST", data: n, dataType: "text", beforeSend: function () {
                        loader.css({display: "block"})
                    }, success: function (t) {
                        a.closest(".set-time").addClass("edit"), window.location.href.indexOf("admin") >= 0 ? (e.find(".modal-body-time").html(t), e.modal({
                            show: !0,
                            backdrop: !1,
                            keyboard: !1
                        }), S(e), void 0 !== _date_admin && "" != _date_admin ? _date_admin = void 0 : $("#edit_booking").css("z-index", "0")) : (e.find(".modal-body-time").html(t), e.modal({
                            show: !0,
                            backdrop: "static",
                            keyboard: !1
                        }))
                    }, complete: function () {
                        loader.css({display: "none"})
                    }, error: function (e) {
                        419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                            target: "#edit_booking",
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })) : Swal.fire({
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })))
                    }
                })
            } else {
                $("#range_date_start").addClass("validate_failed"), $("#range_date_end").addClass("validate_failed");
                $("#range_date_start").parent().parent().after('<p class="note-error node-text booking-laber-padding"> 宿泊日は空白できません。</p>')
            }
        }))
    }, y = function () {
        var e = $("#choice_date_time"), t = $(".js-set-room");
        t.off("click"), t.on("click", (function () {
            var t = $(this), a = $("form.booking").serializeArray(), n = {name: "data_get_attr"}, o = {};
            o.date = t.attr("data-date_value"), o.date_type = t.attr("data-date_type"), n.value = JSON.stringify(o), a.push(n), $.ajax({
                url: $site_url + "/book_room", type: "POST", data: a, dataType: "text", beforeSend: function () {
                    loader.css({display: "block"})
                }, success: function (a) {
                    t.closest(".set-time").addClass("edit"), window.location.href.indexOf("admin") >= 0 ? (e.find(".modal-body-time").html(a), e.modal({
                        show: !0,
                        backdrop: !1,
                        keyboard: !1
                    }), $("#edit_booking").css("z-index", "0")) : (e.find(".modal-body-time").html(a), e.modal({
                        show: !0,
                        backdrop: "static",
                        keyboard: !1
                    }))
                }, complete: function () {
                    loader.css({display: "none"})
                }, error: function (e) {
                    419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                        target: "#edit_booking",
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })) : Swal.fire({
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })))
                }
            })
        }))
    }, k = function () {
        var e = $("#choice_date_time"), a = $(".js-set-room_wt");
        a.off("click"), a.on("click", (function () {
            var a = $(this), n = $("form.booking").serializeArray(), o = {name: "data_get_attr"};
            n.push(o), $.ajax({
                url: $site_url + "/book_time_room_wt",
                type: "POST",
                data: n,
                dataType: "text",
                beforeSend: function () {
                    loader.css({display: "block"})
                },
                success: function (n) {
                    a.closest(".set-time").addClass("edit"), window.location.href.indexOf("admin") >= 0 ? (e.find(".modal-body-time").html(n), e.modal(t({
                        show: !0,
                        backdrop: "static"
                    }, "backdrop", !1)), $("#edit_booking").css("z-index", "0")) : (e.find(".modal-body-time").html(n), e.modal("show"))
                },
                complete: function () {
                    loader.css({display: "none"}), v()
                },
                error: function (e) {
                    419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                        target: "#edit_booking",
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })) : Swal.fire({
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })))
                }
            })
        }))
    }, D = function () {
        var e = $("#choice_date_time"), a = $(".js-set-room_pet");
        a.off("click"), a.on("click", (function () {
            var a = $(this), n = $("form.booking").serializeArray(), o = {name: "data_get_attr"}, i = {};
            i.date = a.attr("data-date_value"), i.date_type = a.attr("data-date_type"), o.value = JSON.stringify(i), n.push(o), $.ajax({
                url: $site_url + "/book_time_room_pet",
                type: "POST",
                data: n,
                dataType: "text",
                beforeSend: function () {
                    loader.css({display: "block"})
                },
                success: function (n) {
                    a.closest(".set-time").addClass("edit"), window.location.href.indexOf("admin") >= 0 ? (e.find(".modal-body-time").html(n), e.modal(t({
                        show: !0,
                        backdrop: "static"
                    }, "backdrop", !1)), S(e), void 0 !== _date_admin && "" != _date_admin ? _date_admin = void 0 : $("#edit_booking").css("z-index", "0")) : (e.find(".modal-body-time").html(n), e.modal("show"))
                },
                complete: function () {
                    loader.css({display: "none"}), v()
                },
                error: function (e) {
                    419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                        target: "#edit_booking",
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })) : Swal.fire({
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })))
                }
            })
        }))
    }, C = function () {
        var e = $("#choice_date_time");
        g();
        var t = ["日", "月", "火", "水", "木", "金", "土"], a = w(moment());
        moment(a).add(1, "days");
        $("#add-time").off("click"), $("#add-time").on("click", (function () {
            if ($("p.note-error").remove(), "0" === $("select[name=gender]").val()) $("select[name=gender]").addClass("validate_failed"), $("select[name=gender]").after('<p class="note-error node-text">性別が空白できません。</p>'); else if (void 0 === $("#room").val() || "01" == JSON.parse($("#room").val()).kubun_id || "" != $("#range_date_start").val() && "－" != $("#range_date_start").val() && "" != $("#range_date_end").val() && "－" != $("#range_date_end").val()) {
                $("select[name=gender]").removeClass("validate_failed");
                var t = $(this), a = $("form.booking").serializeArray(), n = {name: "data_get_attr"}, o = {};
                o.date = t.attr("data-date_value"), o.date_type = t.attr("data-date_type"), o.new = 1, n.value = JSON.stringify(o), a.push(n), $.ajax({
                    url: "/get_time_room", type: "POST", data: a, dataType: "text", beforeSend: function () {
                        loader.css({display: "block"})
                    }, success: function (a) {
                        t.closest(".set-time").addClass("edit"), window.location.href.indexOf("admin") >= 0 ? (e.find(".modal-body-time").html(a), e.modal({
                            show: !0,
                            backdrop: !1
                        }), $("#edit_booking").css("z-index", "0")) : (e.find(".modal-body-time").html(a), e.modal("show"))
                    }, complete: function () {
                        loader.css({display: "none"})
                    }, error: function (e) {
                        419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                            target: "#edit_booking",
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })) : Swal.fire({
                            text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#d7751e",
                            cancelButtonColor: "#343a40",
                            confirmButtonText: "はい",
                            cancelButtonText: "いいえ",
                            width: 350,
                            showClass: {popup: "animated zoomIn faster"},
                            hideClass: {popup: "animated zoomOut faster"},
                            allowOutsideClick: !1
                        }).then((function (e) {
                            e.value && window.location.reload(!0)
                        })))
                    }
                })
            } else {
                $("#range_date_start").addClass("validate_failed"), $("#range_date_end").addClass("validate_failed");
                $("#range_date_start").parent().parent().after('<p class="note-error node-text booking-laber-padding"> 宿泊日は空白できません。</p>')
            }
        })), $("#gender").off("change"), $("#gender").on("change", (function (e) {
            T()
        })), $(".collapse-between").off("hidden.bs.collapse"), $(".collapse-between").on("hidden.bs.collapse", (function () {
            $("#btn-collapse-between").attr("src", "/sunsun/svg/plus.svg")
        })), $(".collapse-between").off("shown.bs.collapse"), $(".collapse-between").on("shown.bs.collapse", (function () {
            $("#btn-collapse-between").attr("src", "/sunsun/svg/hide.svg")
        })), $(".collapse-finish").off("hidden.bs.collapse"), $(".collapse-finish").on("hidden.bs.collapse", (function () {
            $("#btn-collapse-finish").attr("src", "/sunsun/svg/plus.svg")
        })), $(".collapse-finish").off("shown.bs.collapse"), $(".collapse-finish").on("shown.bs.collapse", (function () {
            $("#btn-collapse-finish").attr("src", "/sunsun/svg/hide.svg")
        })), $("#edit_booking").off("click", ".show_history"), $("#edit_booking").on("click", ".show_history", (function (t) {
            t.preventDefault();
            var a = $("#edit_booking").find("#booking_id").val();
            $.ajax({
                url: $site_url + "/admin/show_history",
                type: "POST",
                data: {booking_id: a, is_history: !0},
                dataType: "text",
                beforeSend: function () {
                    loader.css({display: "block"})
                },
                success: function (t) {
                    e.find(".modal-body-time").html(t), e.modal({
                        show: !0,
                        backdrop: !1
                    }), $("#edit_booking").css("z-index", "0")
                },
                complete: function () {
                    loader.css({display: "none"}), v()
                },
                error: function (e) {
                    419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                        target: "#edit_booking",
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })) : Swal.fire({
                        text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#d7751e",
                        cancelButtonColor: "#343a40",
                        confirmButtonText: "はい",
                        cancelButtonText: "いいえ",
                        width: 350,
                        showClass: {popup: "animated zoomIn faster"},
                        hideClass: {popup: "animated zoomOut faster"},
                        allowOutsideClick: !1
                    }).then((function (e) {
                        e.value && window.location.reload(!0)
                    })))
                }
            })
        })), $(document).on("touchmove", (function () {
            $("#date").blur(), $("#range_date_start").blur(), $("#range_date_end").blur(), $("#plan_date_start").blur(), $("#plan_date_end").blur()
        })), f("" != s && "" == r ? s : $("#date").val());
        var n = $("#plan_date_start").val(), o = $("#plan_date_end").val(), i = moment(new Date(n)),
            d = moment(new Date(o));
        $("#plan_date_start-value").val(i.format("YYYY") + i.format("MM") + i.format("DD")), $("#plan_date_end-value").val(d.format("YYYY") + d.format("MM") + d.format("DD")), $("#plan_date_start-view").val(i.format("YYYY") + "年" + i.format("MM") + "月" + i.format("DD") + "日(" + t[i.weekday()] + ")"), $("#plan_date_end-view").val(d.format("YYYY") + "年" + d.format("MM") + "月" + d.format("DD") + "日(" + t[d.weekday()] + ")")
    };

    function x(e, t) {
        var a = [], n = moment(new Date(e));
        for (t = moment(new Date(t)); n <= t;) {
            var o = moment(n).format("YYYY/MM/DD");
            _date_enable.indexOf(o) >= 0 && a.push(o), 3 != moment(n).weekday() && 4 != moment(n).weekday() && a.push(o), n = moment(n).add(1, "days")
        }
        return a
    }

    function O() {
        var e = $("#room").val();
        if (null != e && null != e) {
            e = JSON.parse(e), $("#stay_guest_num").html($("#stay_guest_num_temp").html());
            for (var t = $("#stay_guest_num_temp").prop("selectedIndex"), a = $("#stay_guest_num option").length, n = $("#stay_guest_num option"), o = $("#stay_guest_num"), i = 0; i < a; i++) {
                var d = JSON.parse(n[i].value);
                "02" == d.kubun_id && "04" == e.kubun_id && n[i].remove(), "03" != d.kubun_id || "02" != e.kubun_id && "03" != e.kubun_id && "04" != e.kubun_id || n[i].remove()
            }
            "01" == e.kubun_id || Y("btn-collapse-finish") || o.val(n[0].value), t <= $("#stay_guest_num option").length - 1 && $("#stay_guest_num").prop("selectedIndex", t)
        }
    }

    function B(e) {
        return JSON.parse(e.value).kubun_id
    }

    function Y(e) {
        return e = "#" + e, $(e)[0].src.indexOf("plus") > 0 && ($(e).click(), !0)
    }

    function S(e) {
        if ("undefined" != typeof _date_admin && "" != _date_admin && !l) {
            l = !0;
            for (var t = e.find("input[name=time]"), a = t.length, n = 0; n < a; n++) {
                var o = t[n].value.split("～")[0].replace(":", "");
                if (("" == _bed_admin || (bed = t[n].parentElement.parentElement.querySelector(".bed").value, bed == _bed_admin)) && o == _time_admin) {
                    t[n].checked = !0, $("#js-save-time").click();
                    break
                }
            }
            e.find(".modal-body-time").html(""), $("#btn-cancel").click()
        }
    }

    function M(e) {
        var t = !(arguments.length > 1 && void 0 !== arguments[1]) || arguments[1];
        if ($("p.note-error").remove(), $(".validate_failed").removeClass("validate_failed"), void 0 === e.error_time_transport && void 0 === e.error_time_gender && void 0 === e.error_time_empty && void 0 === e.room_select_error && void 0 === e.error_fasting_plan_holyday || !t || Swal.fire({
            icon: "warning",
            text: "入力した内容を確認してください。",
            confirmButtonColor: "#d7751e",
            confirmButtonText: "閉じる",
            width: 350,
            showClass: {popup: "animated zoomIn faster"},
            hideClass: {popup: "animated zoomOut faster"},
            allowOutsideClick: !1
        }), void 0 !== e.error_time_transport && $.each(e.error_time_transport, (function (e, t) {
            var a = $("#" + t.element);
            a.addClass("validate_failed");
            var n = JSON.parse($("#repeat_user").val());
            "01" == n.kubun_id ? a.parent().after('<p class="note-error node-text">バス停からの移動と初回説明の時間があるので、バスの到着時間から30分以内の予約はできません。</p>') : "02" == n.kubun_id && a.parent().after('<p class="note-error node-text">バス停からの移動があるので、バスの到着時間から15分以内の予約はできません。</p>'), $("#bus_arrive_time_slide").closest("button").addClass("validate_failed")
        })), void 0 !== e.error_time_gender && $.each(e.error_time_gender, (function (e, t) {
            var a = $("#" + t.element);
            a.addClass("validate_failed"), a.parent().after('<p class="note-error node-text"> 選択された時間は予約できません。</p>'), $("select[name=gender]").addClass("validate_failed")
        })), void 0 !== e.error_time_empty && t && $.each(e.error_time_empty, (function (e, t) {
            var a = $("#" + t.element);
            a.addClass("validate_failed"), a.parent().after('<p class="note-error node-text"> 予約時間を選択してください。</p>')
        })), void 0 !== e.room_select_error) {
            $.each(e.room_select_error, (function (e, t) {
                $("#" + t.element).addClass("validate_failed")
            }));
            var a = "選択された日は予約できません";
            "1" == e.room_error_holiday && (a = "定休日が含まれているため予約できません"), $("#range_date_start").parent().parent().after('<p class="note-error node-text booking-laber-padding"> ' + a + "。</p>")
        }
        if (void 0 !== e.error_fasting_plan_holyday && "" != $("#plan_date_start").val() && "" != $("#plan_date_end").val()) {
            $.each(e.error_fasting_plan_holyday, (function (e, t) {
                $("#" + t.element).addClass("validate_failed")
            }));
            a = "定休日が含まれているため予約できません";
            $("#plan_date_start").parent().parent().after('<p class="note-error node-text booking-laber-padding"> ' + a + "。</p>")
        }
    }

    function T() {
        $(this);
        $.ajax({
            url: $site_url + "/validate_before_booking",
            type: "POST",
            data: $("form.booking").serializeArray(),
            dataType: "JSON",
            beforeSend: function () {
                loader.css({display: "block"})
            },
            success: function (e) {
                M(e, !1)
            },
            complete: function () {
                loader.css({display: "none"})
            },
            error: function (e) {
                419 === e.status && (window.location.href.indexOf("admin") >= 0 ? Swal.fire({
                    target: "#edit_booking",
                    text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                    icon: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#d7751e",
                    cancelButtonColor: "#343a40",
                    confirmButtonText: "はい",
                    cancelButtonText: "いいえ",
                    width: 350,
                    showClass: {popup: "animated zoomIn faster"},
                    hideClass: {popup: "animated zoomOut faster"},
                    allowOutsideClick: !1
                }).then((function (e) {
                    e.value && window.location.reload(!0)
                })) : Swal.fire({
                    text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
                    icon: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#d7751e",
                    cancelButtonColor: "#343a40",
                    confirmButtonText: "はい",
                    cancelButtonText: "いいえ",
                    width: 350,
                    showClass: {popup: "animated zoomIn faster"},
                    hideClass: {popup: "animated zoomOut faster"},
                    allowOutsideClick: !1
                }).then((function (e) {
                    e.value && window.location.reload(!0)
                })))
            }
        })
    }
})();
