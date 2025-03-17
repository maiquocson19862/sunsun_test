$(function () {
	let main_head__top = $('.main-head__top'),
		current_day = $('#input-current__date'),
		date_day = current_day.datepicker({
		language: 'ja',
		weekStart: 1,
		dateFormat: 'yyyy/mm/dd',
		autoclose: true,
		onSelect: function() {
			var mon = $(this).datepicker('getDate');
			mon.setDate(mon.getDate() + 1 - (mon.getDay() || 7));
			var sun = new Date(mon.getTime());
			sun.setDate(sun.getDate() + 6);
		}
	});
	$('#button-current__date').off('click');
	$('#button-current__date').on('click', function(e) {
		$('#input-current__date').focus();
	});
	$('#input-current__view').off('click');
	$('#input-current__view').on('click', function(e) {
		$('#input-current__date').focus();
	});
	current_day.on('input change',function (e) {
		let date = $(this).val().split('/').join('');
		setDateView();
		window.location.href = $curent_url+"?date="+date;
	});
	if (current_day.val() === '') {
		date_day.datepicker("setDate", new Date());
		current_day.trigger("input");
	}
	main_head__top.on('click','.prev-date',function (e) {
		var date = date_day.datepicker('getDate');
		date.setTime(date.getTime() - (1000*60*60*24));
		date_day.datepicker("setDate", date);
		current_day.trigger("input");
	});
	main_head__top.on('click','.next-date',function (e) {
		var date = date_day.datepicker('getDate');
		date.setTime(date.getTime() + (1000*60*60*24));
		date_day.datepicker("setDate", date);
		current_day.trigger("input");
	});
	let booking_edit = $('#edit_booking');
	booking_edit.on('show.bs.modal', function (e) {
		$('.modal .modal-dialog').attr('class', 'modal-dialog modal-dialog-centered zoomIn  animated faster');
	})
	// booking_edit.on('hide.bs.modal', function (e) {
	// })
	let booking_edit_hidden = function(){
		$('.modal .modal-dialog').attr('class', 'modal-dialog modal-dialog-centered zoomOut  animated faster');
		setTimeout(function(){
			booking_edit.modal('hide');
		}, 500);
	}
	$('#edit_booking').off('click','.btn-cancel');
	$('#edit_booking').on('click','.btn-cancel',function (e) {
		booking_edit_hidden();
	});
	$('#edit_booking').off('click','.btn-delete');
	$('#edit_booking').on('click','.btn-delete',function (e) {
		var delete_icon_url = window.location.origin + "/sunsun/imgs/icons/delete.png";
		var booking_id = $('#edit_booking').find("#booking_id").val();
		var ref_booking_id = $('#edit_booking').find("#ref_booking_id").val();
		var message = "予約者の予約を削除すると、同行者の予約も削除されます。全ての予約を削除しますか？";
		if(ref_booking_id != ''){
			message = "同行者の予約を削除しても、予約者の予約は削除されません。同行者の予約を削除しますか？";
		}
		Swal.fire({
			target: '#edit_booking',
			text: message,
			// icon: 'warning',
			imageUrl: delete_icon_url,
			imageWidth: "5em",
			imageHeight: "5em",
			showCancelButton: true,
			confirmButtonColor: '#ff0000',
			cancelButtonColor: '#ffc000',
			confirmButtonText: '削除する',
			cancelButtonText: 'キャンセル',
			width: 350,
			showClass: {
				popup: 'animated zoomIn faster'
			},
			hideClass: {
				popup: 'animated zoomOut faster'
			},
			allowOutsideClick: false
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: '/admin/delete_booking',
					type: 'POST',
					data: {
						'booking_id' : booking_id,
						'ref_booking_id' : ref_booking_id
					},
					dataType: 'text',
					beforeSend: function () {
						loader.css({'display': 'block'});
					},
					success: function (html) {
						html = JSON.parse(html);
						if(html.status === true){
							$('#edit_booking').modal('hide');
							window.location.reload();
						}
					},
					complete: function () {
						loader.css({'display': 'none'});
					}
				});
			}
		})
	});
	let show_booking = function (obj,type,bed) {
		var booking_id = $(obj).find('.booking-id').val();
		// get time
		var time_admin = obj.parentElement.parentElement.id.replace("row_", "");
		if (type == "05") {
			time_admin = obj.id.replace("r_pet_", "").split("-")[0];
		}
		// end get time
		var id_sex = "";
		if (type == "01") {
			id_sex = "1";
			var list_class = obj.parentElement.classList;
			for (var i = 0; i< list_class.length; i++) {
				if (list_class[i].indexOf("famale") > 0) {
					id_sex = "2";
					break;
				}
			}
		}
		var date_admin = "";
		if (booking_id == "" || booking_id == null) {
			date_admin = $("#input-current__date").val();
			if (type == "02") return;
		}
		$.ajax({
			url: '/admin/edit_booking',
			type: 'POST',
			data: {
				'new' : 0,
				'booking_id' : booking_id,
				'type_admin' : type,
				'sex_admin' : id_sex,
				'date_admin' : date_admin,
				'time_admin' : time_admin,
				'bed_admin' : bed,
			},
			dataType: 'text',
			beforeSend: function () {
				loader.css({'display': 'block'});
			},
			success: function (html) {
				booking_edit.find('.mail-booking').html(html);
				booking_edit.modal({
					show: true,
					backdrop: 'static',
					keyboard: false
				});
				load_payment_event();
			},
			complete: function () {
				loader.css({'display': 'none'});
			},
			error: function(jqXHR){
				if(jqXHR.status === 419){
					Swal.fire({
						text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#d7751e',
						cancelButtonColor: '#343a40',
						confirmButtonText: 'はい',
						cancelButtonText: 'いいえ',
						width: 350,
						showClass: {
							popup: 'animated zoomIn faster'
						},
						hideClass: {
							popup: 'animated zoomOut faster'
						},
						allowOutsideClick: false
					}).then((result) => {
						if (result.value) {
							window.location.reload(true);
						}
					})
				}
			}
		});
	};
	function setDateView() {
		var new_day = moment(new Date($('#input-current__date').val()));
		var days_short = new Array("日","月","火","水","木","金","土");
		$('#input-current__view').val(new_day.format('YYYY') + "/" + new_day.format('MM') + "/" + new_day.format('DD') + "(" + days_short[new_day.weekday()] + ")");
	}
	function getBed(id) {
		if (id != null && id != "") {
			var arr_id = id.split("_");
			return arr_id[1];
		}
	}
	function checkShow(obj) {
		var checkDisEdit = 1;
		if (obj.hasClass("bg-dis")) {
			var booking_id = obj.find(".booking-id");
			checkDisEdit = (booking_id.length > 0) ? 2 : 0;
		}
		$("#checkDisEdit").val(checkDisEdit);
		return checkDisEdit;
	}
	$('.main-col__data').not(".bg-free").off('click');
	$('.main-col__data').not(".bg-free").on('click', function (e) {
		if (checkShow($(this)) == 0) return;
		if(!$(this).find('.control-align_center').text()){
			show_booking(this,"01",getBed(this.id));
		}
	});
	$('.main-col__pet').not(".space-white").not(".head").off('click');
	$('.main-col__pet').not(".space-white").not(".head").on('click', function (e) {
		if (checkShow($(this)) == 0) return;
		show_booking(this,"05","");
	});
	$('.main-col__wt').not(".not-wt").not(".head").off('click');
	$('.main-col__wt').not(".not-wt").not(".head").on('click', function (e) {
		if (checkShow($(this)) == 0) return;
		show_booking(this,"02",getBed(this.id));
	});
	$('#go-weekly').off('click');
	$('#go-weekly').on('click',function (e) {
		let date = date_day.datepicker('getDate');
		let currentDate = moment(date);
		let weekStart = currentDate.clone().startOf('isoweek');
		let start_weekly = moment(weekStart).add(0, 'days').format("YMMDD");
		let end_weekly = moment(weekStart).add(6, 'days').format("YMMDD");
		let weekly_url = $curent_url.substring(0, $curent_url.length - 3) + "weekly";
		window.location.href = weekly_url + "?date_from=" + start_weekly + "&date_to=" + end_weekly;
	});
	$('#go-monthly').off('click');
	$('#go-monthly').on('click',function (e) {
		let date = date_day.datepicker('getDate');
		let currentDate = moment(date);
		let monthly = currentDate.format("YMM");
		let monthly_url = $curent_url.substring(0, $curent_url.length - 3) + "monthly";
		window.location.href = monthly_url + "?date=" + monthly;
	})
	$('#go-user').off('click');
	$('#go-user').on('click',function (e) {
		let user_url = $curent_url.substring(0, $curent_url.length - 9) + "admin/msuser";
		window.location.href = user_url;
	})
	$('#go-timeoff').off('click');
	$('#go-timeoff').on('click',function (e) {
		let timeoff_url = $curent_url.substring(0, $curent_url.length - 9) + "admin/time_off";
		window.location.href = timeoff_url;
	})
	function isValidDate(d) {
		if(!isNaN(d.getTime())) {
			return false;
		}
		return true;
	}
	function setDate03(date_string) {
		var new_day = new Date(date_string);
		if (isValidDate(new_day)) return false;
		new_day = moment(new_day);
		if (new_day.weekday() == 6 || new_day.weekday() == 0) return false;
		return true;
	}
	// function beforeSave() {
	// 	if ($("#checkDisEdit").val() > 1) {
	// 		$("#name").removeAttr('disabled');
	// 		$("#phone").removeAttr('disabled');
	// 		$("#email").removeAttr('disabled');
	// 		$("#repeat_user").removeAttr('disabled');
	// 		$("#transport").removeAttr('disabled');
	// 		$("#course").removeAttr('disabled');
	// 		$(".service-warp").find('input, textarea, button, select').removeAttr('disabled');
	// 		$(".booking-field.bus").find('input, textarea, button, select').removeAttr('disabled');
	// 	}
	// }
	// function disDiv() {
	// 	if ($("#checkDisEdit").val() > 1) {
	// 		$("#name").attr('disabled','disabled');
	// 		$("#phone").attr('disabled','disabled');
	// 		$("#email").attr('disabled','disabled');
	// 		$("#repeat_user").attr('disabled','disabled');
	// 		$("#transport").attr('disabled','disabled');
	// 		$("#course").attr('disabled','disabled');
	// 		$(".service-warp").find('input, textarea, button, select').attr('disabled','disabled');
	// 		$(".booking-field.bus").find('input, textarea, button, select').attr('disabled','disabled');
	// 	}
	// }
	$('#edit_booking').off('click','.btn-update');
	$('#edit_booking').on('click','.btn-update',function (e) {
		e.preventDefault();
		// check course 03
		var course_id = JSON.parse($('#course').val()).kubun_id;
		if (course_id == "03") {
			$('#date').removeClass('validate_failed');
			var date_check = $("#date-value").val();
			var check = false;
			if (date_check != undefined && date_check.length >= 8) {
				date_check = date_check.substring(0, 4)+"/"+date_check.substring(4, 6)+"/"+date_check.substring(6, 8);
				check = setDate03(date_check);
			}
			if (!check) {
				$("#date").addClass('validate_failed');
				return;
			}
		}
		$('p.note-error').remove();
		if($('select[name=gender]').val() === "0"){
			$('select[name=gender]').addClass('validate_failed');
			$('select[name=gender]').after('<p class="note-error node-text">性別が空白できません。</p>');
		} else if(
			(   $('#room').val()  !== undefined && JSON.parse($('#room').val())['kubun_id'] != '01')
			&&(
				($('#range_date_start').val() == '')
				|| ($('#range_date_start').val() == '－')
				|| ($('#range_date_end').val() == '')
				|| ($('#range_date_end').val() == '－')
			)
		) {
			$('#range_date_start').addClass('validate_failed');
			$('#range_date_end').addClass('validate_failed');
			var text_err = "宿泊日は空白できません";
			$('#range_date_start').parent().parent().after('<p class="note-error node-text booking-laber-padding"> '+text_err+'。</p>');
		} else {
			// beforeSave();
			let data = $('form.booking').serializeArray();
			// console.log(data);
			$.ajax({
				url: $site_url +'/admin/update_booking',
				type: 'POST',
				data: data,
				dataType: 'JSON',
				beforeSend: function () {
					loader.css({'display': 'block'});
				},
				success: function (html) {
					// console.log(html);
					if((html.status == false) && (html.type == 'validate')){
						make_color_input_error(html.message.booking);
						make_payment_validate(html.message.payment);
						Swal.fire({
							icon: 'warning',
							// title: 'エラー',
							text: '入力した内容を確認してください。',
							confirmButtonColor: '#d7751e',
							confirmButtonText: '閉じる',
							width: 350,
							showClass: {
								popup: 'animated zoomIn faster'
							},
							hideClass: {
								popup: 'animated zoomOut faster'
							},
							allowOutsideClick: false
						})
					}else{
						$('#edit_booking').modal('hide');
						window.location.reload();
					}
				},
				complete: function () {
					loader.css({'display': 'none'});
					// disDiv();
				},
				error: function(jqXHR){
					if(jqXHR.status === 419){
						Swal.fire({
							target: '#edit_booking',
							text: "セッションがタイムアウトされました。ウェブサイトをリロードしてください。",
							icon: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#d7751e',
							cancelButtonColor: '#343a40',
							confirmButtonText: 'はい',
							cancelButtonText: 'いいえ',
							width: 350,
							showClass: {
								popup: 'animated zoomIn faster'
							},
							hideClass: {
								popup: 'animated zoomOut faster'
							},
							// customClass: {
							//     popup: 'modal-dialog'
							// },
							allowOutsideClick: false
						}).then((result) => {
							if (result.value) {
								window.location.reload(true);
							}
						})
					}
					// disDiv();
				}
			});
		}
	})
	$('#edit_booking').on('click','#credit-card',function (e) {
		return false;
	})
	let make_payment_validate = (array) => {
			$('p.note-error').remove();
			$.each(array.error, function (index, item) {
				$('#'+item).css({'border': 'solid 1px #f50000'});
				switch(item) {
					case 'name': $('#'+item).parent().after('<p class="note-error node-text"> お名前をカタカナで入力してください。</p>');
						break;
					case 'phone': $('#'+item).parent().after('<p class="note-error node-text"> 電話番号は数字のみを入力してください。</p>');
						break;
					case 'email': $('#'+item).parent().after('<p class="note-error node-text"> 入力したメールアドレスを確認してください。</p>');
						break;
				}
			})
			$.each(array.clear, function (index, item) {
				$('#'+item).css({'border': 'solid 1px #ced4da'});
			})
	}
	let make_color_input_error = (json) => {
		$('p.note-error').remove();
		if (typeof json.clear_border_red !== "undefined" ) {
			$.each(json.clear_border_red, function (index, item) {
				$('#'+item.element).css({'border': 'solid 1px #ced4da'});
				$('#bus_arrive_time_slide').closest('button').css({'border': 'solid 1px #ced4da'});
				$('select[name=gender]').css({'border': 'solid 1px #ced4da'});
			})
		}
		if (typeof json.error_time_transport !== "undefined" ) {
			$.each(json.error_time_transport, function (index, item) {
				let input_error_transport = $('#'+item.element);
				input_error_transport.css({'border': 'solid 1px #f50000'});
				let repeat_user = JSON.parse($('#repeat_user').val());
				if(repeat_user.kubun_id == '01'){
					input_error_transport.parent().after('<p class="note-error node-text">バス停からの移動と初回説明の時間があるので、バスの到着時間から30分以内の予約はできません。</p>');
				}else if(repeat_user.kubun_id == '02'){
					input_error_transport.parent().after('<p class="note-error node-text">バス停からの移動があるので、バスの到着時間から15分以内の予約はできません。</p>');
				}
				$('#bus_arrive_time_slide').closest('button').css({'border': 'solid 1px #f50000'});
			})
		}
		if (typeof json.error_time_gender  !== "undefined") {
			$.each(json.error_time_gender, function (index, item) {
				let input_error_gender = $('#'+item.element);
				input_error_gender.css({'border': 'solid 1px #f50000'});
				input_error_gender.parent().after('<p class="note-error node-text"> 選択された時間は予約できません。</p>');
				$('select[name=gender]').css({'border': 'solid 1px #f50000'});
			})
		}
		if (typeof json.error_time_empty  !== "undefined") {
			$.each(json.error_time_empty, function (index, item) {
				let input_error_required = $('#'+item.element);
				input_error_required.css({'border': 'solid 1px #f50000'});
				input_error_required.parent().after('<p class="note-error node-text"> 予約時間を選択してください。</p>');
			})
		}
		if (typeof json.room_select_error  !== "undefined") {
			$.each(json.room_select_error, function (index, item) {
				$('#'+item.element).css({'border': 'solid 1px #f50000'});
			})
			var text_err = "選択された日は予約できません";
			if (json.room_error_holiday == "1")
				text_err = "定休日が含まれているため予約できません";
			$('#range_date_start').parent().parent().after('<p class="note-error node-text booking-laber-padding"> '+text_err+'。</p>');
		}
		if (typeof json.error_fasting_plan_holyday  !== "undefined") {
			$.each(json.error_fasting_plan_holyday, function (index, item) {
				$('#'+item.element).addClass('validate_failed');
			});
			var text_err = "定休日が含まれているため予約できません";
			$('#plan_date_start').parent().parent().after('<p class="note-error node-text booking-laber-padding"> '+text_err+'。</p>');
		}
	};
	// $('.search-button').off('click');
	// $('.search-button').on('click',function (e) {
	//     $('#search').val('');
	//     $('#result').html('');
	//     $('.search-button').html('');
	// });
	if ((typeof load_search_event) === 'undefined') {
		load_search_event = function(){
			$('.list-group-item').off('click');
			$('.list-group-item').on('click',function (e) {
				var name = $(this).find('.search-element').val();
				$.ajax({
					url: '/admin/ajax_name_search',
					type: 'POST',
					data: {
						'name' : name
					},
					success: function (expert) {
						// var expert = JSON.parse($(this).find('.search-expert').val());
						// console.log(name);
						// console.log(expert);
						var data_expert = '';
						expert = expert.result;
						$.each(expert, function(key, value) {
							var check_re = value.ref_booking_id == null ? '' : '同行者';
							var course_re = '';
							switch(value.course) {
								case '01':
									course_re = '入浴';
									break;
								case '02':
									course_re = '朝リ';
									break;
								case '03':
									course_re = '貸切';
									break;
								case '04':
									course_re = '断食初';
									break;
								case '05':
									course_re = 'Pet';
									break;
								case '06': // 2020/06/05
									course_re = '断食リ';
									break;
								case '07':
									course_re = '昼ス';
									break;
								case '08':
									course_re = '美肌';
									break;
								case '09':
									course_re = '免疫';
									break;
								case '10':
									course_re = '昼り';
									break;
								default:
									course_re = '';
									break;
							}
							var day = moment(value.service_date);
							var date = day.format('Y/M/D')
							var hour = parseInt(value.time.substr(0, 2), 10);
							var minute = value.time.substr(2, 2);
							data_expert += '<li class="list-group-item link-class list-body">'
							+ "<input type='hidden' class='bookingSeclect' value='" + value.booking_id + "' />"
							+ "<input type='hidden' class='dateSeclect' value='" + value.service_date + "' />"
							+ "<input type='hidden' class='timeSeclect' value='" + value.time + "' />"
							+ value.name
							+ check_re
							+ " ["
							+ course_re
							+ "] "
							+ date
							+ " "
							+ hour
							+ ":"
							+ minute
							+ '</li>';
						})
						Swal.fire({
							html:
							'<ul><li class="list-group-item link-class list-head">' + name + '</li>'
							+ data_expert
							+ '</ul>',
							text: ' 入力した内容を確認してください。',
							confirmButtonColor: '#d7751e',
							confirmButtonText: '閉じる',
							width: 500,
							showClass: {
								popup: 'animated zoomIn faster'
							},
							hideClass: {
								popup: 'animated zoomOut faster'
							},
							allowOutsideClick: false
						})
						$('#search').val('');
						$('#result').html('');
						$('.search-button').html('');
						$('.list-group-item.list-body').off('click');
						$('.list-group-item.list-body').on('click',function (e) {
							$("#bookingSeclect").val($(this).find(".bookingSeclect").val());
							$("#timeSeclect").val($(this).find(".timeSeclect").val());
							$("#selectCourse").attr('action', $("#selectCourse").attr('action') + "?date=" + $(this).find(".dateSeclect").val()).submit()
						})
					},
					beforeSend: function () {
						loader.css({'display': 'block'});
					},
					complete: function () {
						loader.css({'display': 'none'});
					}
				});
			});
		};
	}
	let load_payment_event = function () {
		$('#collapseOne').collapse('hide');
		$('#headingOne').on('click', function (e) {
			e.preventDefault();
		});
		$('.card').on('show.bs.collapse', function () {
			$(this).find('.payment-method').prop('checked',true);
		});
		$(`[data-toggle="collapse"]`).on('click',function(e){
			if ($(this).attr('id') !== 'nav') {
				var idx = $(this).index('[data-toggle="collapse"]');
				if(idx === 0){
					e.stopPropagation();
				}else if ( $(this).parents('.accordion').find('.collapse.show') ){
					if (idx === $('.collapse.show').index('.collapse')) {
						// console.log(idx);
						e.stopPropagation();
					}
				}
			}
		});
	}
	$(document).bind('contextmenu', function(e) {
		e.preventDefault();
	});
	$('.main-col__data').not(".bg-free").contextmenu(function() {
		if (checkShow($(this)) == 0) return;
		var payment_id = $(this).find(".payment_id").val();
		if((payment_id !== "") && (payment_id !== undefined)){
			Swal.fire({
				html: "オーダーID: " + payment_id,
				// icon: 'info',
				showCloseButton: true,
				showConfirmButton: false,
				showClass: {
					popup: 'animated zoomIn faster'
				},
				hideClass: {
					popup: 'animated zoomOut faster'
				},
				allowOutsideClick: false
			})
		}
	});
	$('.main-col__pet').not(".space-white").not(".head").contextmenu(function() {
		if (checkShow($(this)) == 0) return;
		var payment_id = $(this).find(".payment_id").val();
		if((payment_id !== "") && (payment_id !== undefined)){
			Swal.fire({
				html: "オーダーID: " + payment_id,
				// icon: 'info',
				showCloseButton: true,
				showConfirmButton: false,
				showClass: {
					popup: 'animated zoomIn faster'
				},
				hideClass: {
					popup: 'animated zoomOut faster'
				},
				allowOutsideClick: false
			})
		}
	});
	$('.main-col__wt').not(".not-wt").not(".head").contextmenu(function() {
		if (checkShow($(this)) == 0) return;
		var payment_id = $(this).find(".payment_id").val();
		if((payment_id !== "") && (payment_id !== undefined)){
			Swal.fire({
				html: "オーダーID: " + payment_id,
				// icon: 'info',
				showCloseButton: true,
				showConfirmButton: false,
				showClass: {
					popup: 'animated zoomIn faster'
				},
				hideClass: {
					popup: 'animated zoomOut faster'
				},
				allowOutsideClick: false
			})
		}
	});
	$("#txt_notes").focusout(function(){
		$.ajax({
			url: '/admin/ajax_save_notes',
			type: 'POST',
			data: {
				'date_notes' : $("#input-current__date").val(),
				'txt_notes' : $("#txt_notes").val().trim()
			},
			success: function (html) {
				console.log(html);
			}
		});
	});
	$('#go-sales_list').off('click');
	$('#go-sales_list').on('click',function (e) {
		window.location.href = $curent_url.substring(0, $curent_url.length - 3) + "sales_list";
	});
	$('#go-day_on').off('click');
	$('#go-day_on').on('click',function (e) {
		window.location.href = $curent_url.substring(0, $curent_url.length - 3) + "day_on";
	})
});