var submit = false;
var open = false;
let main_head__top = $('.main-head__top'),
	start_day = $('#input-start__date'),
	start_view = $('#input-start__view'),
	date_start_day = start_day.datepicker({
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
	}),
	end_day = $('#input-end__date'),
	end_view = $('#input-end__view'),
	date_end_day = end_day.datepicker({
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
$("#searchSubmit").on("click", function(){
	if (!submit) {
		searchSubmit();
	}
});
$('#button-start__date').off('click');
$('#button-start__date').on('click', function(e) {
	$('#input-start__date').focus();
});
start_view.off('click');
start_view.on('click', function(e) {
	start_day.focus();
});
$('#button-end__date').off('click');
$('#button-end__date').on('click', function(e) {
	$('#input-end__date').focus();
});
end_view.off('click');
end_view.on('click', function(e) {
	end_day.focus();
});
start_day.on('input change',function (e) {
	let date = $(this).val().split('/').join('');
	start_view.val(getDateView(start_day.val()));
});
if (start_day.val() === '') {
	date_start_day.datepicker("setDate", new Date());
	start_day.trigger("input");
}
end_day.on('input change',function (e) {
	let date = $(this).val().split('/').join('');
	end_view.val(getDateView(end_day.val()));
});
if (end_day.val() === '') {
	date_end_day.datepicker("setDate", new Date());
	end_day.trigger("input");
}
function getDateView(val) {
	var new_day = moment(new Date(val));
	var days_short = new Array("日","月","火","水","木","金","土");
	return new_day.format('YYYY') + "/" + new_day.format('MM') + "/" + new_day.format('DD') + "(" + days_short[new_day.weekday()] + ")";
}
function eventLoad() {
	$('#currentPage').keypress(function(e){
		if(e.which == 13){
			e.preventDefault()
			var url=url_paginate.value;
			var page=currentPage.value;
			var url_active=url+'?page='+page;
			window.location.href = url_active;
		}
	});
}
// search
function searchSubmit() {
	var data = $('form#searchform').serializeArray();
	$.ajax({
		url: $site_url + '/admin/sales_list',
		type: 'GET',
		data: data,
		dataType: 'JSON',
		beforeSend: function beforeSend() {
			loader.css({
				'display': 'block'
			});
		},
		success: function success(html) {
			if (html.status == true) {
				$('.resulttable').html(html.data);
				eventLoad();
				const nextURL = $site_url + '/admin/sales_list';
				const nextTitle = $(document).find("title").text();
				const nextState = { additionalInformation: 'Updated the URL with JS' };
				// This will create a new entry in the browser's history, without reloading
				window.history.pushState(nextState, nextTitle, nextURL);
				// This will replace the current entry in the browser's history, without reloading
				window.history.replaceState(nextState, nextTitle, nextURL);
			}
		},
		complete: function complete() {
				loader.css({
				'display': 'none'
			});
		}
	});
	submit = false;
}
eventLoad();