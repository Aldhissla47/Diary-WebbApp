function monthName(arg1) {
	var month = ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"];
	var i = arg1 - 1;
	return month[i];
}

function validateDate() {
	var year = $('#form_year').val();
	var month = $('#form_month').val();
	
	var date = new Date();
    var currentYear = date.getFullYear();
	var currentmonth = date.getMonth() + 1;
	
	var pageform = document.getElementById("form").name;
	
	$.ajax({
		url:'validate_monthly_year_and_month.php',
		type: 'POST',
		data: {year: year, month: month},
		success: function(data) {
			if (data != '0') {
				if (pageform == 'add_form') {
					alert('Det finns redan en månadsrapport för ' + monthName(month) + ' ' + year + '!');
					$('#form_year').val(currentYear);
					$('#form_month').val(currentmonth);
					updateMonthRows();
					return false;
				}
			}
		}
	});
	return true;
}

function validateForm() {
	var sendvalue = $('#form_send_button_value').val();
	var string = '';
	
	if (sendvalue == "no") {
		string = 'Spara?';
	} else if (sendvalue == "yes") {
		string = 'Spara och skicka?';
	} else {
		string = 'Spara och lås?';
	}
	if (confirm(string)) {
		var supervisor = document.forms["form"]["supervisor"].value;
		var year = $('#form_year').val();
		var month = $('#form_month').val();
		
		if (supervisor == "") {
			alert("Fyll i Beställare!");
			return false;
		}
		if (!validateDate()) {
			alert('Det finns redan en månadsrapport för ' + monthName(month) + ' ' + year + '!');
			return false;
		}
		var count = document.getElementById("form_month_rows").childElementCount;
		var day = 0;
		var job = '';
		var time = '';
		
		var emptyrows = 0;
		
		for (var i = 0; i < count; i++) {
			day = i + 1;
			job = $('#form_month_job' + i + '').val();
			time = $('#form_month_time' + i + '').val();
			
			if (job.length > 0 && time.length == 0) {
				alert("Fyll i Tid för dag: " + day + "!");
				return false;
			} else if (job.length == 0 && time.length > 0) {
				alert("Fyll i Utfört arbete/aktivitet för dag: " + day + "!");
				return false;
			} else if (job.length == 0 && time.length == 0) {
				emptyrows++;
			}
		}
		if (i == emptyrows) {
			alert("Minst en dag måste vara ifylld!");
			return false;
		}
		return true;
	}
	return false;
}

function updateTotalTime() {
	var count = document.getElementById("form_month_rows").childElementCount;
	var sum = 0;
	
	for (var i = 0; i < count; i++) {
		if ($('#form_month_time' + i + '').val() != '') {
			sum = sum + parseFloat($('#form_month_time' + i + '').val());
		}
	}
	if (sum > 0) {
		$('#form_total_time').val(sum.toFixed(1));
	} else {
		$('#form_total_time').val('');
	}
}

function updateSend(value) {
	if (value == 0) {
		$('#form_send_button_value').val("no");
	} else if (value == 1) {
		$('#form_send_button_value').val("yes");
	} else {
		$('#form_send_button_value').val("lock");
	}
}

function updateSelectMonth(value) {
	var date = new Date();
    var currentYear = date.getFullYear();
	var currentmonth = date.getMonth() + 1;
	var month = $('#form_month').val();
	
	var optionstring = '';
	
	var max = 0;
	
	if (value < currentYear) {
		max = 12;
	} else {
		max = currentmonth;
		if (month > currentmonth) {
			month = currentmonth;
		}
	}
	$('#form_month').remove();
	
	for (var i = 1; i <= max; i++) {
		if (i == month) {
			optionstring += '<option value="' + i + '" selected>' + monthName(i) + '</option>';
		} else {
			optionstring += '<option value="' + i + '">' + monthName(i) + '</option>';
		}
	}
	$('#form_month_div').append( '<select name="month" class="form_textbox" id="form_month" onchange="updateMonthRows()">' + optionstring + '</select>' );
	updateMonthRows();
}

function updateMonthDays() {
	var count = document.getElementById("form_month_rows").childElementCount;
	var year = $('#form_year').val();
	var month = $('#form_month').val();
	
	var dayname = ["Mån", "Tis", "Ons", "Tor", "Fre", "Lör", "Sön"];
	var day = '';
	
	for (var i = 0; i < count; i++) {
		day = new Date(year, (month - 1), i).getDay();
		$('#form_month_dayname' + i + '').val(dayname[day]);
		
		if (day == 6 || (month == 1 && i == 0) || (month == 12 && (i > 23 && i <= 25))) {
			$('#form_month_day' + i + '').css('color', 'red');
			$('#form_month_dayname' + i + '').css('color', 'red');
		} else {
			$('#form_month_day' + i + '').css('color', 'black');
			$('#form_month_dayname' + i + '').css('color', 'black');
		}
	}
}

function weekNumber(year,month,day) {
    function serial(days) { return 86400000*days; }
    function dateserial(year,month,day) { return (new Date(year,month-1,day).valueOf()); }
    function weekday(date) { return (new Date(date)).getDay()+1; }
    function yearserial(date) { return (new Date(date)).getFullYear(); }
    var date = year instanceof Date ? year.valueOf() : typeof year === "string" ? new Date(year).valueOf() : dateserial(year,month,day), 
        date2 = dateserial(yearserial(date - serial(weekday(date-serial(1))) + serial(4)),1,3);
    return ~~((date - date2 + serial(weekday(date2) + 5))/ serial(7));
}

function updateWeeks() {
	var count = document.getElementById("form_month_rows").childElementCount;
	var year = $('#form_year').val();
	var month = $('#form_month').val();
	var monthstring = '';
	var daystring = '';
	var day = '';
	
	var ddate = '';
	var date = '';
	
	if (month < 10) {
		monthstring = '0' + month + '';
	} else {
		monthstring = '' + month + '';
	}
	for (var i = 0; i < count; i++) {
		if (i < 9) {
			daystring = '0' + (i + 1) + '';
		} else {
			daystring = '' + (i + 1) + '';
		}
		ddate = '' + year + '-' + monthstring + '-' + daystring + '';		
		date = new Date(ddate);		
		
		day = new Date(year, (month - 1), i).getDay();
		
		if (day == 0) {
			$('#form_week' + i + '').val('v. ' + weekNumber(ddate) + '');
		} else {
			$('#form_week' + i + '').val('');
		}
	}
}

function updateMonthRows() {
	var div = document.getElementById("form_month_rows");
	var count = div.childElementCount;
	
	var year = $('#form_year').val();
	var month = $('#form_month').val();
	
	var days = new Date(year, month, 0).getDate();
	
	if (days < count) {
		for (var i = 0; i < count - days; i++) {
			div.removeChild(div.lastChild);
		}
	} else if (days > count) {
		for (var i = count; i < days; i++) {
			$('#form_month_rows').append( '<div id="form_month_row' + i + '"><input type="text" name="month_day' + i + '" class="form_textbox form_day_textbox" id="form_month_day' + i + '" value="' + (i + 1) + '" readonly/><input type="text" class="form_textbox form_day_textbox" id="form_month_dayname' + i + '" readonly/><input type="text" name="month_job' + i + '" class="form_textbox form_work_textbox" id="form_month_job' + i + '" maxlength="255"/><input type="text" name="month_time' + i + '" class="form_textbox form_time_textbox" id="form_month_time' + i + '" onchange="return updateTotalTime()" onkeypress="return isFloatNumberKey(event)" maxlength="10"/><input type="text" value="" class="form_textbox form_borderless" id="form_week' + i + '" readonly/></div>' );
		}
	}
	$('#form_month_day_count').val(div.childElementCount);
	updateTotalTime();
	updateMonthDays();
	updateWeeks();
}
