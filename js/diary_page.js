$('#form_date').change(function() {
	validateDate();
	validateAbnorms();
	var ddate = $('#form_date').val();
	var date = new Date(ddate);
	var weekday = new Array(7);
	weekday[0] = "Söndag";
	weekday[1] = "Måndag";
	weekday[2] = "Tisdag";
	weekday[3] = "Onsdag";
	weekday[4] = "Torsdag";
	weekday[5] = "Fredag";
	weekday[6] = "Lördag";	
	
	$('#form_week').val(weeknumber(ddate));
	$('#form_weekday').val(weekday[date.getDay()]);	
});

function weeknumber(year,month,day) {
    function serial(days) { return 86400000*days; }
    function dateserial(year,month,day) { return (new Date(year,month-1,day).valueOf()); }
    function weekday(date) { return (new Date(date)).getDay()+1; }
    function yearserial(date) { return (new Date(date)).getFullYear(); }
    var date = year instanceof Date ? year.valueOf() : typeof year === "string" ? new Date(year).valueOf() : dateserial(year,month,day), 
        date2 = dateserial(yearserial(date - serial(weekday(date-serial(1))) + serial(4)),1,3);
    return ~~((date - date2 + serial(weekday(date2) + 5))/ serial(7));
}

function validateWorkday() {
	var workday = $('#form_workday').val();
	var orgworkday = $('#form_org_workday').val();
	var company = $('#form_company').val();
	
	var pageform = document.getElementById("form").name;
	
	$.ajax({
		url:'validate_diary_workday_and_date.php',
		type: 'POST',
		data: {workday: workday, company: company},
		success: function(data) {
			if (data != '0') {
				if (pageform == 'add_form') {
					alert('Det finns redan ett dagboksinlägg för abetsdag: ' + workday + '!');
					$('#form_workday').val(orgworkday);
					return false;
				}
			}
		}
	});
	return true;
}

function validateDate() {
	var date = $('#form_date').val();
	var week = $('#form_week').val();
	var weekday = $('#form_weekday').val();
	var todaysdate = $('#form_todays_date').val();
	var company = $('#form_company').val();
	
	var pageform = document.getElementById("form").name;
	
	$.ajax({
		url:'validate_diary_workday_and_date.php',
		type: 'POST',
		data: {date: date, company: company},
		success: function(data) {
			if (data != '0') {
				if (data == '-1') {
					alert('Det går inte att lägga till dagböcker för framtida datum!');
					$('#form_date').val(todaysdate);
					$('#form_week').val(week);
					$('#form_weekday').val(weekday);
				} else {
					if (pageform == 'add_form') {
						alert('Det finns redan ett dagboksinlägg för: ' + date + '!');
						$('#form_date').val(todaysdate);
						$('#form_week').val(week);
						$('#form_weekday').val(weekday);
					}
				}
				return false;
			}
		}
	});
	return true;
}

function validateAbnorms() {
	var div = document.getElementById("form_abnorms_rows");
	var childDivs = div.getElementsByTagName("div");
	var children = div.childElementCount;
	var date = $('#form_date').val();
	var current = '';
	
	for (var i = 0; i < childDivs.length; i++) {
		current = childDivs[i];
		if (date < $('#form_abnorms_date' + i + '').val()) {
			div.removeChild(current);
		} else {
			
		}
		children = div.childElementCount;
	}
	$('#form_abnorms_count').val(children);
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
		var work_day = document.forms["form"]["workday"].value;
		var date = document.forms["form"]["date"].value;
		var supervisor = document.forms["form"]["supervisor"].value;
		var jobsite = document.forms["form"]["jobsite"].value;
		
		if (work_day == "" || work_day <= 0) {
			alert("Fyll i Arbetsdag!");
			return false;
		} else {
			if (!validateWorkday()) {
				alert('Det finns redan ett dagboksinlägg för abetsdag: ' + work_day + '!');
				return false;
			}
		}
		if (date == "") {
			alert("Fyll i Datum!");
			return false;
		} else {
			if (!validateDate()) {
				alert('Det gick inte att skapa dagboksinlägg för: ' + date + '!');
				return false;
			}
		}
		if (supervisor == "") {
			alert("Fyll i Arbetsledare!");
			return false;
		}
		if (jobsite == "") {
			alert("Fyll i Arbetsplats!");
			return false;
		}	
		var jobcount = document.getElementById("form_work_rows").childElementCount;		
		var crewcount = new Array(jobcount);
		
		for (var i = 0; i < jobcount; i++) {
			crewcount[i] = document.getElementById("form_crew_rows" + i + "").childElementCount;
		}
		var jobtype = '';
		var jobcomments = '';
		var jobstatus = '';
		
		var crewtype = '';
		var crewname = '';
		var crewtime = '';
		
		var filledjobs = 0;
		
		for (var i = 0; i < jobcount; i++) {
			jobtype = $("#form_crew_job" + i + "").val();
			jobstatus = $("#form_job_status" + i + "").val();
			
			if (jobtype != "" && jobstatus != "") {
				for (var j = 0; j < crewcount[i]; j++) {
					crewtype = $("#form_crew_type" + i + "" + j + "").val();
					crewname = $("#form_abnorms_crew_name" + i + "" + j + "").val();
					crewtime = $("#form_crew_time" + i + "" + j + "").val();
					
					if (crewtype == "") {
						alert("Fyll i Benämning för arbete: " + (i + 1) + " arbetsstyrka: " + (j + 1) + "!");
						return false;
					}
					if (crewname == "") {
						alert("Fyll i Namn för arbete: " + (i + 1) + " arbetsstyrka: " + (j + 1) + "!");
						return false;
					}
					if (crewtime == "") {
						alert("Fyll i Tid för arbete: " + (i + 1) + " arbetsstyrka: " + (j + 1) + "!");
						return false;
					}
				}
				filledjobs++;
			} else {
				if (jobstatus != "" && jobtype == "") {
					alert("Fyll i Rubrik för arbete: " + (i + 1) + "!");
					return false;
				} else if (jobtype != "" && jobstatus == "") {
					alert("Fyll i Status för arbete: " + (i + 1) + "!");
					return false;
				}
			}	
		}
		var abnormscount = document.getElementById("form_abnorms_rows").childElementCount;		
		var abnormscrewcount = new Array(abnormscount);
		
		for (var i = 0; i < abnormscount; i++) {
			abnormscrewcount[i] = document.getElementById("form_abnorms_crew_rows" + i + "").childElementCount;
		}
		var abnormsid = '';
		var abnormsheader = '';
		var abnormsjobsite = '';
		var abnormscomments = '';
		var abnormsstatus = '';
		
		var filledabnorms = 0;
		
		for (var i = 0; i < abnormscount; i++) {
			abnormsid = $("#form_abnorms_numberbox" + i + "").val();
			abnormsheader = $("#form_abnorms_header" + i + "").val();
			abnormsjobsite = $("#form_abnorms_jobsite" + i + "").val();
			abnormscomments = $("#form_abnorms_comment" + i + "").val();
			abnormsstatus = $("#form_abnorms_status" + i + "").val();
			
			if (abnormsheader != "" && abnormsjobsite != "" && abnormscomments != "" && abnormsstatus != "") {
				for (var j = 0; j < abnormscrewcount[i]; j++) {
					crewtype = $("#form_abnorms_crew_type" + i + "" + j + "").val();
					crewname = $("#form_abnorms_crew_name" + i + "" + j + "").val();
					crewtime = $("#form_abnorms_crew_time" + i + "" + j + "").val();
					
					if (abnormsstatus == 3) {
						if (crewtype == "" && crewname == "" && crewtime == "") {
							
						} else {
							if (crewtype == "") {
								alert("Fyll i Benämning för avvikelse: " + abnormsid + " arbetsstyrka: " + (j + 1) + "!");
								return false;
							}
							if (crewname == "") {
								alert("Fyll i Namn för avvikelse: " + abnormsid + " arbetsstyrka: " + (j + 1) + "!");
								return false;
							}
							if (crewtime == "") {
								alert("Fyll i Tid för avvikelse: " + abnormsid + " arbetsstyrka: " + (j + 1) + "!");
								return false;
							}
						}
					} else {
						if (crewtype == "") {
							alert("Fyll i Benämning för avvikelse: " + abnormsid + " arbetsstyrka: " + (j + 1) + "!");
							return false;
						}
						if (crewname == "") {
							alert("Fyll i Namn för avvikelse: " + abnormsid + " arbetsstyrka: " + (j + 1) + "!");
							return false;
						}
						if (crewtime == "") {
							alert("Fyll i Tid för avvikelse: " + abnormsid + " arbetsstyrka: " + (j + 1) + "!");
							return false;
						}
					}
				}
				filledabnorms++;
			} else {
				if (abnormsheader == "" && abnormsjobsite == "" && abnormscomments == "" && abnormsstatus == "") {
					
				} else {
					if (abnormsheader == "") {
						alert("Fyll i Rubrik för avvikelse: " + abnormsid + "!");
						return false;
					}
					if (abnormsjobsite == "") {
						alert("Fyll i Plats för avvikelse: " + abnormsid + "!");
						return false;
					}
					if (abnormscomments == "") {
						alert("Fyll i Noteringar för avvikelse: " + abnormsid + "!");
						return false;
					}
					if (abnormsstatus == "") {
						alert("Fyll i Status för avvikelse: " + abnormsid + "!");
						return false;
					}
				}
			}
		}
		if (filledjobs == 0 && filledabnorms == 0) {
			alert("Antingen arbete eller avvikelse måste ha minst ett fält med rödmarkeringar ifyllt!");
			return false;
		}
		return true;
	}
	return false;
}

function addJobField(){
	var count = document.getElementById("form_work_rows").childElementCount;
	var options = $('#form_crew_type00 option');
	var values = $.map(options ,function(option) {
		return option.value;
	});
	var optioncount = values.length;
	var optionsstring = '<select name="crew_type' + count + '0" class="form_textbox" id="form_crew_type' + count + '0"><option value="">-</option>';
	
	for (var i = 1; i < optioncount; i++) {
		optionsstring += '<option value="' + values[i] + '">' + values[i] + '</option>';
	}
	optionsstring += '</select>';
	
	var statusoptions = $('#form_job_status0 option');
	values = $.map(statusoptions ,function(option) {
		return option.text;
	});
	optioncount = values.length;
	var statusstring = '<select name="job_status' + count + '" class="form_textbox form_statusbox" id="form_job_status' + count + '">';
	
	for (var i = 0; i < optioncount; i++) {
		statusstring += '<option value="' + (i + 1) + '">' + values[i] + '</option>';
	}
	statusstring += '</select>';
	
	var timestring = '<select name="crew_time' + count + '0" class="form_textbox form_crew_time_textbox" id="form_crew_time' + count + '0" onchange="updateTotalWorkTime(' + count + ')"><option value="">-</option>';
	for (var i = 0.5; i < 24.5; i += 0.5) {
		timestring += '<option value="' + i + '">' + i.toFixed(1) + '</option>';
	}
	timestring += '</select>';
	
	$('#form_work_rows').append( '<div class="form_work_row"><div class="form_work_title"><h5>Rubrik: </h5><p>*</p></div><div class="form_work_status"><h5>Status: </h5><p>*</p></div><br>' + (count + 1) + '. ' + '<input type="text" name="crew_job' + count + '" class="form_textbox form_job_textbox" id="form_crew_job' + count + '" placeholder=""/>' + statusstring + '<input type="button" onclick="removeJobField(' + count + ')" value="Ta bort arbete" class="form_job_remove_button" id="form_job_remove_button' + count + '"/><br><div class="form_work_comment"><h5>Notering: </h5></div><input type="text" name="crew_comments' + count + '" class="form_textbox form_job_comments_textbox" id="form_crew_comments' + count + '" placeholder=""/><input type="hidden" name="crewrows' + count + '" id="form_crew_count' + count + '" value="1"/><div class="form_crew_rows" id="form_crew_rows' + count + '"><div id="form_crew_row' + count + '0"><div><div class="form_crew_title"><h5>Arbetsstyrka: </h5><p>*</p></div><div class="form_crew_title"><h5>Namn: </h5><p>*</p></div><div id="form_work_time_title"><h5>Tid: </h5><p>*</p></div></div>1. ' + optionsstring + '<input type="text" name="crew_name' + count + '0" class="form_textbox" id="form_crew_name' + count + '0" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/>' + timestring + '<input type="radio" name="crew_radio' + count + '0" value="1" checked>Egen<input type="radio" name="crew_radio' + count + '0" value="0">UE<input type="button" onclick="removeCrewField(' + count + ',0)" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button' + count + '0"/></div></div><div class="crew_total_time">Summa Timmar: <input type="text" name="total_time" id="form_crew_total_time' + count + '" class="form_textbox form_borderless" readonly/></div><input type="button" onclick="addCrewField(' + count + ')" value="Lägg till rad" class="form_crew_add_button" id="form_crew_add_button' + count + '"/>' );
	
	document.getElementById("form_job_remove_button0").style.display = "inline";
	document.getElementById("form_job_remove_button" + count + "").style.display = "inline";
	$('#form_work_count').val(count + 1);
}

function removeJobField(index) {
	if (confirm('Ta bort arbete: ' + (index + 1) + ' och all dess innehåll?')) {
		var div = document.getElementById("form_work_rows");
		var children = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < children; i++) {
			next = i + 1;
			
			$("#form_crew_job" + i + "").val($("#form_crew_job" + next + "").val());
			$("#form_crew_comments" + i + "").val($("#form_crew_comments" + next + "").val());
			$("#form_job_status" + i + "").val($("#form_job_status" + next + "").val());
			
			if (i !== children - 1) {
				var crewdiv = document.getElementById("form_crew_rows" + i + "");
				var crewchildren = crewdiv.childElementCount;
				
				var nextcrewdiv = document.getElementById("form_crew_rows" + next + "");
				var nextcrewchildren = nextcrewdiv.childElementCount;
				
				if (crewchildren > nextcrewchildren) {
					for (var j = 0; j < crewchildren - nextcrewchildren; j++) {
						crewdiv.removeChild(crewdiv.lastChild);
					}
				}
				for (j = 0; j < nextcrewchildren; j++) {
					if (j < crewchildren) {
						$("#form_crew_type" + i + "" + j + "").val($("#form_crew_type" + next + "" + j + "").val());
						$("#form_crew_name" + i + "" + j + "").val($("#form_crew_name" + next + "" + j + "").val());
						$("#form_crew_time" + i + "" + j + "").val($("#form_crew_time" + next + "" + j + "").val());
						
						radios = document.getElementsByName("crew_radio" + i + "" + j + "");
						nextRadios = document.getElementsByName("crew_radio" + next + "" + j + "");
						
						for (var z = 0; z < nextRadios.length; z++) {
							if (nextRadios[z].checked) {
								radios[z].checked = true;
							}
						}
					} else {
						var options = $('#form_crew_type00 option');
						var values = $.map(options ,function(option) {
							return option.value;
						});
						var optioncount = values.length;
						var optionsstring = '<select name="crew_type' + i + '' + j + '" class="form_textbox" id="form_crew_type' + i + '' + j + '"><option value="">-</option>';
						
						for (var z = 1; z < optioncount; z++) {
							if ($("#form_crew_type" + next + "" + j + "").val() === values[z]) {
								optionsstring += '<option value="' + values[z] + '" selected>' + values[z] + '</option>';
							} else {
								optionsstring += '<option value="' + values[z] + '">' + values[z] + '</option>';
							}
						}
						optionsstring += '</select>';
						
						var timestring = '<select name="crew_time' + i + '' + j + '" class="form_textbox form_crew_time_textbox" id="form_crew_time' + i + '' + j + '"><option value="">-</option>';
						for (var z = 0.5; z < 24.5; z += 0.5) {
							if ($("#form_crew_time" + next + "" + j + "").val() === z) {
								timestring += '<option value="' + z + '" selected>' + z.toFixed(1) + '</option>';
							} else {
								timestring += '<option value="' + z + '">' + z.toFixed(1) + '</option>';
							}
						}
						timestring += '</select>';
						
						$('#form_crew_rows' + i + '').append( '<div id="form_crew_row' + i + '' + j + '">' + (j + 1) + '. ' + optionsstring + '<input type="text" name="crew_name' + i + '' + j + '" id="form_crew_name' + i + '' + j + '" class="form_textbox" onchange="isLetterOrSpaceKey(this)" value="' + $("#form_crew_name" + next + "" + j + "").val() + '" placeholder=" -"/>' + timestring + '<input type="radio" name="crew_radio' + i + '' + j + '" value="1" checked>Egen<input type="radio" name="crew_radio' + i + '' + j + '" value="0">UE<input type="button" onclick="removeCrewField(' + i + ',' + j + ')" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button' + i + '' + j + '"/></div>' );
						
						var radios = document.getElementsByName("crew_radio" + i + "" + j + "");
						var nextRadios = document.getElementsByName("crew_radio" + next + "" + j + "");
						
						for (var z = 0; z < nextRadios.length; z++) {
							if (nextRadios[z].checked) {
								radios[z].checked = true;
							}
						}
						document.getElementById("form_crew_remove_button" + i + "0").style.display = "inline";
						document.getElementById("form_crew_remove_button" + i + "" + j + "").style.display = "inline";
						$('#form_crew_count' + i + '').val(j + 1);
					}
				}
			}
			updateTotalWorkTime(i);
		}
		div.removeChild(div.lastChild);
		
		children = div.childElementCount;
		
		if (children < 2) {
			document.getElementById("form_job_remove_button0").style.display = "none";
		}
		$('#form_work_count').val(children);
	}
}

function addCrewField(id){
	var count = document.getElementById("form_crew_rows" + id + "").childElementCount;
	var options = $('#form_crew_type00 option');
	var values = $.map(options ,function(option) {
		return option.value;
	});
	var optioncount = values.length;
	var optionsstring = '<select name="crew_type' + id + '' + count + '" class="form_textbox" id="form_crew_type' + id + '' + count + '"><option value="">-</option>';
	
	for (var i = 1; i < optioncount; i++) {
		optionsstring += '<option value="' + values[i] + '">' + values[i] + '</option>';
	}
	optionsstring += '</select>';
	
	var timestring = '<select name="crew_time' + id + '' + count + '" class="form_textbox form_crew_time_textbox" id="form_crew_time' + id + '' + count + '" onchange="updateTotalWorkTime(' + id + ')"><option value="">-</option>';
	for (var i = 0.5; i < 24.5; i += 0.5) {
		timestring += '<option value="' + i + '">' + i.toFixed(1) + '</option>';
	}
	timestring += '</select>';
	
	$('#form_crew_rows' + id + '').append( '<div id="form_crew_row' + id + '' + count + '">' + (count + 1) + '. ' + optionsstring + '<input type="text" name="crew_name' + id + '' + count + '" id="form_crew_name' + id + '' + count + '" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/>' + timestring + '<input type="radio" name="crew_radio' + id + '' + count + '" value="1" checked>Egen<input type="radio" name="crew_radio' + id + '' + count + '" value="0">UE<input type="button" onclick="removeCrewField(' + id + ',' + count + ')" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button' + id + '' + count + '"/></div>' );
	
	document.getElementById("form_crew_remove_button" + id + "0").style.display = "inline";
	document.getElementById("form_crew_remove_button" + id + "" + count + "").style.display = "inline";
	$('#form_crew_count' + id + '').val(count + 1);
}

function removeCrewField(id,index) {
	if (confirm('Ta bort arbetsstyrka: ' + (index + 1) + '?')) {
		var div = document.getElementById("form_crew_rows" + id + "");
		var children = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < children; i++) {
			next = i + 1;
			
			$("#form_crew_type" + id + "" + i + "").val($("#form_crew_type" + id + "" + next + "").val());
			$("#form_crew_name" + id + "" + i + "").val($("#form_crew_name" + id + "" + next + "").val());
			$("#form_crew_time" + id + "" + i + "").val($("#form_crew_time" + id + "" + next + "").val());
			
			var radios = document.getElementsByName("crew_radio" + id + "" + i + "");
			var nextRadios = document.getElementsByName("crew_radio" + id + "" + next + "");
			
			for (var j = 0; j < nextRadios.length; j++) {
				if (nextRadios[j].checked) {
					radios[j].checked = true;
				}
			}
		}
		div.removeChild(div.lastChild);
		
		var count = document.getElementById("form_crew_rows" + id + "").childElementCount;
		
		if (count < 2) {
			document.getElementById("form_crew_remove_button" + id + "0").style.display = "none";
		}
		$('#form_crew_count' + id + '').val(count);
		updateTotalWorkTime(id);
	}
}

function addAbnormsField() {
	var count = document.getElementById("form_abnorms_rows").childElementCount;
	var previous = +$('#form_abnorms_maxid').val() + 1;
	$('#form_abnorms_maxid').val(previous);
	
	var options = $('#form_abnorms_crew_type00 option');
	var values = $.map(options ,function(option) {
		return option.value;
	});
	var optioncount = values.length;
	var optionsstring = '<select name="abnorms_crew_type' + count + '0" class="form_textbox" id="form_abnorms_crew_type' + count + '0"><option value="">-</option>';
	
	for (var i = 1; i < optioncount; i++) {
		optionsstring += '<option value="' + values[i] + '">' + values[i] + '</option>';
	}
	optionsstring += '</select>';
	
	var statusstring = '<select name="abnorms_status' + count + '" class="form_textbox form_statusbox" id="form_abnorms_status' + count + '"><option value=""></option>';	
	statusstring += '<option value="1">Påbörjad</option>';
	statusstring += '<option value="3">Avslutad</option>';
	statusstring += '</select>';
	
	var timestring = '<select name="abnorms_crew_time' + count + '0" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time' + count + '0" onchange="updateTotalAbnormsTime(' + count + ')"><option value="">-</option>';
	for (var i = 0.5; i < 24.5; i += 0.5) {
		timestring += '<option value="' + i + '">' + i.toFixed(1) + '</option>';
	}
	timestring += '</select>';
	
	$('#form_abnorms_rows').append( '<div class="form_work_row"><div class="form_abnorms_title"><h5>Rubrik: </h5></div><div class="form_abnorms_title"><h5>Plats: </h5></div><div id="form_abnorms_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>' + previous + '. <input type="hidden" name="abnorms_nr' + count + '" id="form_abnorms_numberbox' + count + '" value="' + previous + '"/>' + '<input type="text" name="abnorms_header' + count + '" id="form_abnorms_header' + count + '" class="form_textbox" maxlength="255" placeholder=""/><input type="text" name="abnorms_jobsite' + count + '" id="form_abnorms_jobsite' + count + '" class="form_textbox" placeholder=""/><input type="text" name="abnorms_comments' + count + '" class="form_abnorms_comments" maxlength="255" id="form_abnorms_comment' + count + '" placeholder=""/>' + statusstring + '<input type="button" onclick="removeAbnormsField(' + count + ')" value="Ta bort avvikelse" class="form_job_remove_button" id="form_abnorms_remove_button' + count + '"/><br><input type="checkbox" name="abnorms_economic_checkbox' + count + '" value="true" class="form_echeckbox" id="form_echeckbox' + count + '"/>Ekonomisk konsekvens<input type="checkbox" name="abnorms_time_checkbox' + count + '" value="true" id="form_tcheckbox' + count + '"/>Tidskonsekvens<input type="hidden" name="abnormscrewrows' + count + '" id="form_abnormscrew_count' + count + '" value="1"/><div class="form_crew_rows" id="form_abnorms_crew_rows' + count + '"><div id="form_abnorms_crew_row' + count + '0"><div><div class="form_crew_rev_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_rev_title"><h5>Namn: </h5></div><div id="form_work_time_rev_title"><h5>Tid: </h5></div></div>1. ' + optionsstring + '<input type="text" name="abnorms_crew_name' + count + '0" id="form_abnorms_crew_name' + count + '0" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/>' + timestring + '<input type="radio" name="abnorms_crew_radio' + count + '0" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio' + count + '0" value="0">UE<input type="button" onclick="removeAbnormsCrewField(' + count + ',0)" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button' + count + '0"/></div></div><div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time' + count + '" class="form_textbox form_abnorms form_borderless" readonly/></div><input type="button" onclick="addAbnormsCrewField(' + count + ')" value="Lägg till rad" class="form_crew_add_button" id="form_abnorms_crew_add_button' + count + '"/>' );
	
	document.getElementById("form_abnorms_remove_button0").style.display = "inline";
	document.getElementById("form_abnorms_remove_button" + count + "").style.display = "inline";
	
	$('#form_abnorms_count').val(count + 1);
}

function removeAbnormsField(index) {
	var id = $("#form_abnorms_numberbox" + index + "").val();
	if (confirm('Ta bort avvikelse: ' + id + ' och all dess innehåll?')) {
		var div = document.getElementById("form_abnorms_rows");
		var children = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < children; i++) {
			next = i + 1;
			
			$("#form_abnorms_header" + i + "").val($("#form_abnorms_header" + next + "").val());
			$("#form_abnorms_jobsite" + i + "").val($("#form_abnorms_jobsite" + next + "").val());
			$("#form_abnorms_comment" + i + "").val($("#form_abnorms_comment" + next + "").val());
			
			if (i !== children - 1) {
				if (document.getElementById("form_echeckbox" + next + "").checked) {
					document.getElementById("form_echeckbox" + i + "").checked = true;
				} else {
					document.getElementById("form_echeckbox" + i + "").checked = false;
				}
				if (document.getElementById("form_tcheckbox" + next + "").checked) {
					document.getElementById("form_tcheckbox" + i + "").checked = true;
				} else {
					document.getElementById("form_tcheckbox" + i + "").checked = false;
				}
				
				var crewdiv = document.getElementById("form_abnorms_crew_rows" + i + "");
				var crewchildren = crewdiv.childElementCount;
				
				var nextcrewdiv = document.getElementById("form_abnorms_crew_rows" + next + "");
				var nextcrewchildren = nextcrewdiv.childElementCount;
				
				if (crewchildren > nextcrewchildren) {
					for (var j = 0; j < crewchildren - nextcrewchildren; j++) {
						crewdiv.removeChild(crewdiv.lastChild);
					}
				}
				for (j = 0; j < nextcrewchildren; j++) {
					if (j < crewchildren) {
						$("#form_abnorms_crew_type" + i + "" + j + "").val($("#form_abnorms_crew_type" + next + "" + j + "").val());
						$("#form_abnorms_crew_name" + i + "" + j + "").val($("#form_abnorms_crew_name" + next + "" + j + "").val());
						$("#form_abnorms_crew_time" + i + "" + j + "").val($("#form_abnorms_crew_time" + next + "" + j + "").val());
						
						radios = document.getElementsByName("abnorms_crew_radio" + i + "" + j + "");
						nextRadios = document.getElementsByName("abnorms_crew_radio" + next + "" + j + "");
						
						for (var z = 0; z < nextRadios.length; z++) {
							if (nextRadios[z].checked) {
								radios[z].checked = true;
							}
						}
					} else {
						var options = $('#form_abnorms_crew_type00 option');
						var values = $.map(options ,function(option) {
							return option.value;
						});
						var optioncount = values.length;
						var optionsstring = '<select name="abnorms_crew_type' + i + '' + j + '" class="form_textbox" id="form_abnorms_crew_type' + i + '' + j + '"><option value="">-</option>';
						
						for (var z = 1; z < optioncount; z++) {
							if ($("#form_abnorms_crew_type" + next + "" + j + "").val() === values[z]) {
								optionsstring += '<option value="' + values[z] + '" selected>' + values[z] + '</option>';
							} else {
								optionsstring += '<option value="' + values[z] + '">' + values[z] + '</option>';
							}
						}
						optionsstring += '</select>';
						
						var timestring = '<select name="abnorms_crew_time' + i + '' + j + '" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time' + i + '' + j + '"><option value="">-</option>';
						for (var z = 0.5; z < 24.5; z += 0.5) {
							if ($("#form_abnorms_crew_time" + next + "" + j + "").val() === z) {
								timestring += '<option value="' + z + '" selected>' + z.toFixed(1) + '</option>';
							} else {
								timestring += '<option value="' + z + '">' + z.toFixed(1) + '</option>';
							}
						}
						timestring += '</select>';
						
						$('#form_abnorms_crew_rows' + i + '').append( '<div id="form_abnorms_crew_row' + i + '' + j + '">' + (j + 1) + '. ' + optionsstring + '<input type="text" name="abnorms_crew_name' + i + '' + j + '" id="form_abnorms_crew_name' + i + '' + j + '" class="form_textbox" onchange="isLetterOrSpaceKey(this)" value="' + $("#form_abnorms_crew_name" + next + "" + j + "").val() + '" placeholder=" -"/>' + timestring + '<input type="radio" name="abnorms_crew_radio' + i + '' + j + '" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio' + i + '' + j + '" value="0">UE<input type="button" onclick="removeAbnormsCrewField(' + i + ',' + j + ')" value="Ta Bort Rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button' + i + '' + j + '"/></div>' );
						
						var radios = document.getElementsByName("abnorms_crew_radio" + i + "" + j + "");
						var nextRadios = document.getElementsByName("abnorms_crew_radio" + next + "" + j + "");
						
						for (var z = 0; z < nextRadios.length; z++) {
							if (nextRadios[z].checked) {
								radios[z].checked = true;
							}
						}
						document.getElementById("form_abnorms_crew_remove_button" + i + "0").style.display = "inline";
						document.getElementById("form_abnorms_crew_remove_button" + i + "" + j + "").style.display = "inline";
						$('#form_abnorms_crew_count' + i + '').val(j + 1);
					}
				}
			}
			updateTotalAbnormsTime(i);
		}
		div.removeChild(div.lastChild);
		
		children = div.childElementCount;
		
		if (children < 2) {
			document.getElementById("form_abnorms_remove_button0").style.display = "none";
		}
		$('#form_abnorms_maxid').val($('#form_abnorms_maxid').val() - 1);
		$('#form_abnorms_count').val(children);
	}
}

function addAbnormsCrewField(id){
	var count = document.getElementById("form_abnorms_crew_rows" + id + "").childElementCount;
	var options = $('#form_abnorms_crew_type00 option');
	var values = $.map(options ,function(option) {
		return option.value;
	});
	var optioncount = values.length;
	var optionsstring = '<select name="abnorms_crew_type' + id + '' + count + '" class="form_textbox" id="form_abnorms_crew_type' + id + '' + count + '"><option value="">-</option>';
	
	for (var i = 1; i < optioncount; i++) {
		optionsstring += '<option value="' + values[i] + '">' + values[i] + '</option>';
	}
	optionsstring += '</select>';
	
	var timestring = '<select name="abnorms_crew_time' + id + '' + count + '" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time' + id + '' + count + '" onchange="updateTotalAbnormsTime(' + id + ')"><option value="">-</option>';
	for (var i = 0.5; i < 24.5; i += 0.5) {
		timestring += '<option value="' + i + '">' + i.toFixed(1) + '</option>';
	}
	timestring += '</select>';
	
	$('#form_abnorms_crew_rows' + id + '').append( '<div id="form_abnorms_crew_row' + id + '' + count + '">' + (count + 1) + '. ' + optionsstring + '<input type="text" name="abnorms_crew_name' + id + '' + count + '" id="form_abnorms_crew_name' + id + '' + count + '" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/>' + timestring + '<input type="radio" name="abnorms_crew_radio' + id + '' + count + '" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio' + id + '' + count + '" value="0">UE<input type="button" onclick="removeAbnormsCrewField(' + id + ',' + count + ')" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button' + id + '' + count + '"/></div>' );
	
	document.getElementById("form_abnorms_crew_remove_button" + id + "0").style.display = "inline";
	document.getElementById("form_abnorms_crew_remove_button" + id + "" + count + "").style.display = "inline";
	$('#form_abnorms_crew_count' + id + '').val(count + 1);
}

function removeAbnormsCrewField(id,index) {
	if (confirm('Ta bort arbetsstyrka: ' + (index + 1) + '?')) {
		var div = document.getElementById("form_abnorms_crew_rows" + id + "");
		var children = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < children; i++) {
			next = i + 1;
			
			$("#form_abnorms_crew_type" + id + "" + i + "").val($("#form_abnorms_crew_type" + id + "" + next + "").val());
			$("#form_abnorms_crew_name" + id + "" + i + "").val($("#form_abnorms_crew_name" + id + "" + next + "").val());
			$("#form_abnorms_crew_time" + id + "" + i + "").val($("#form_abnorms_crew_time" + id + "" + next + "").val());
			
			var radios = document.getElementsByName("abnorms_crew_radio" + id + "" + i + "");
			var nextRadios = document.getElementsByName("abnorms_crew_radio" + id + "" + next + "");
			
			for (var j = 0; j < nextRadios.length; j++) {
				if (nextRadios[j].checked) {
					radios[j].checked = true;
				}
			}
		}
		div.removeChild(div.lastChild);
		
		var count = document.getElementById("form_abnorms_crew_rows" + id + "").childElementCount;
		
		if (count < 2) {
			document.getElementById("form_abnorms_crew_remove_button" + id + "0").style.display = "none";
		}
		$('#form_abnorms_crew_count' + id + '').val(count);
		updateTotalAbnormsTime(id);
	}
}

function updateAbnorms() {
	var date = $('#form_date').val();
	var workday = $('#form_workday').val();
	var count = document.getElementById("form_abnorms_rows").childElementCount;

}

function addMiscField(){
	var count = document.getElementById("form_misc_rows").childElementCount;
	
	var options = $('#form_misc_category0 option');
	var values = $.map(options ,function(option) {
		return option.text;
	});
	optioncount = values.length;
	var categorystring = '<select name="misc_category' + count + '" class="form_textbox" id="form_misc_category' + count + '">';
	
	for (var i = 0; i < optioncount; i++) {
		categorystring += '<option value="' + values[i] + '">' + values[i] + '</option>';
	}
	categorystring += '</select>';
	
	$('#form_misc_rows').append( '<div>' + (count + 1) + '. ' + categorystring + '<input type="text" name="misc_comments' + count + '" id="form_misc_comments' + count + '" class="form_textbox form_misc_comments_textbox" maxlength="255" placeholder=""/><input type="button" onclick="removeMiscField(' + count + ')" value="Ta bort rad" class="form_job_remove_button" id="form_misc_remove_button' + count + '"/></div>' );

	document.getElementById("form_misc_remove_button0").style.display = "inline";
	document.getElementById("form_misc_remove_button" + count + "").style.display = "inline";
	
	$('#form_misc_count').val(count + 1);
}

function removeMiscField(index) {
	if (confirm('Ta bort övrigt rad: ' + (index + 1) + '?')) {
		var div = document.getElementById("form_misc_rows");
		var count = document.getElementById("form_misc_rows").childElementCount;
		var next = 0;
		
		for (var i = index; i < count; i++) {
			next = i + 1;
			
			$("#form_misc_category" + i + "").val($("#form_misc_category" + next + "").val());
			$("#form_misc_comments" + i + "").val($("#form_misc_comments" + next + "").val());
		}
		div.removeChild(div.lastChild);
		
		count = document.getElementById("form_misc_rows").childElementCount;
		
		if (count < 2) {
			document.getElementById("form_misc_remove_button0").style.display = "none";
		}
		$('#form_misc_count').val(div.childElementCount);
	}
}

function updateTotalWorkTime(id) {
	var count = document.getElementById("form_crew_rows" + id + "").childElementCount;
	var sum = 0;
	
	for (var i = 0; i < count; i++) {
		if ($('#form_crew_time' + id + '' + i + '').val() != '') {
			sum = sum + parseFloat($('#form_crew_time' + id + '' + i + '').val());
		}
	}
	if (sum > 0) {
		$('#form_crew_total_time' + id + '').val(sum.toFixed(1));
	} else {
		$('#form_crew_total_time' + id + '').val('');
	}
}

function updateTotalAbnormsTime(id) {
	var count = document.getElementById("form_abnorms_crew_rows" + id + "").childElementCount;
	var sum = 0;
	
	for (var i = 0; i < count; i++) {
		if ($('#form_abnorms_crew_time' + id + '' + i + '').val() != '') {
			sum = sum + parseFloat($('#form_abnorms_crew_time' + id + '' + i + '').val());
		}
	}
	if (sum > 0) {
		$('#form_abnorms_total_time' + id + '').val(sum.toFixed(1));
	} else {
		$('#form_abnorms_total_time' + id + '').val('');
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
