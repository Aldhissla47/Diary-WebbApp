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
		var present_count = $('#form_present_count').val();
		var not_present_count = $('#form_not_present_count').val();
		
		var name = '';
		var company = '';
		var email = '';
		
		var id = $('#form_meeting_id').val();
		var type = $('#form_type').val();
		var mainheader = $('#form_mainheader').val();
		var time = $('#form_time').val();
		var time2 = $('#form_time2').val();
		var jobsite = $('#form_jobsite').val();
		
		if (!validateDate()) {
			return false;
		}
		for (var i = 0; i < present_count; i++) {
			name = $('#form_present' + i + '').val();
			company = $('#form_company' + i + '').val();
			email = $('#form_email' + i + '').val();
			
			if (name == '' && company == '' && email == '') {
				
			} else {
				if (name == '') {
					alert("Fyll i Namn för närvarande: " + (i + 1) + "!");
					return false;
				}
				if (company == '') {
					alert("Fyll i Företag för närvarande: " + (i + 1) + "!");
					return false;
				}
				if (email == '') {
					alert("Fyll i Email för närvarande: " + (i + 1) + "!");
					return false;
				} else {
					if (!validateEmail(email)) {
						alert("Ogiltig Email för närvarande: " + (i + 1) + "!");
						return false;
					}
				}
			}
		}
		for (i = 0; i < not_present_count; i++) {
			name = $('#form_not_present' + i + '').val();
			company = $('#form_not_company' + i + '').val();
			email = $('#form_not_email' + i + '').val();
			
			if (name == '' && company == '' && email == '') {
				
			} else {
				if (name == '') {
					alert("Fyll i Namn för ej närvarande: " + (i + 1) + "!");
					return false;
				}
				if (company == '') {
					alert("Fyll i Företag för ej närvarande: " + (i + 1) + "!");
					return false;
				}
				if (email == '') {
					alert("Fyll i Email för ej närvarande: " + (i + 1) + "!");
					return false;
				} else {
					if (!validateEmail(email)) {
						alert("Ogiltig Email för ej närvarande: " + (i + 1) + "!");
						return false;
					}
				}
			}
		}
		if (type == '') {
			alert("Fyll i Mötestyp!");
			return false;
		}
		if (mainheader == '') {
			alert("Fyll i Rubrik!");
			return false;
		}
		if (time == '' || time2 == '') {
			alert("Fyll i Tid!");
			return false;
		}
		if (time >= time2) {
			alert("Ogiltig Tid!");
			return false;
		}
		if (jobsite == '') {
			alert("Fyll i Plats!");
			return false;
		}
		var contentcount = $('#form_content_count').val();
		var taskcount = 0;
		var supercount = 0;
		var filledsupers = 0;
		var header = '';
		var task = '';
		var taskid = '';
		
		for (i = 0; i < contentcount; i++) {
			header = $('#form_header' + i + '').val();
			taskcount = $('#form_task_count' + i + '').val();
			
			if (header == '') {
				alert("Fyll i Rubrik nr: " + i + "!");
				return false;
			}
			for (var j = 0; j < taskcount; j++) {
				supercount = $('#form_super_count' + i + '' + j + '').val();
				task = $('#form_task' + i + '' + j + '').val();
				taskid = $('#form_task_id' + i + '' + j + '').val();
				
				filledsupers = 0;
				for (var k = 0; k < supercount; k++) {
					if ($('#form_supervisor' + i + '' + j + '' + k + '').val() != '') {
						filledsupers++;
					}
				}
				if (task == '' && filledsupers == 0) {
				
				} else {
					if (task == '') {
						alert("Fyll i Uppgift nr: " + taskid + "!");
						return false;
					}
					if (filledsupers == 0) {
						alert("Fyll i minst en ansvarig för: " + taskid + "!");
						return false;
					}
				}
			}
		}
		return true;
	}
	return false;
}

function validateDate() {
	var date = getCurrentDate();
	var value = $('#form_date').val();
	var series = $('#form_series_id').val();
	
	var pageform = document.getElementById("form").name;
	
	if (value != '') {
		$.ajax({
			url:'validate_meeting_date.php',
			type: 'POST',
			data: {series: series, date: value},
			success: function(data) {
				if (data != '0') {
					if (pageform == 'add_form') {
						if (data == '-1') {
							alert('Datum kan inte vara ett framtida datum!');
						} else {
							alert('Datum kan inte vara ett tidigare eller samma datum som föregående möte!');
						}
						$('#form_date').val(date);
						return false;
					}
				}
			}
		});
		return true;
	}
	return false;
}

function getSupervisors() {
	var supercount = document.getElementById("form_present").childElementCount;
	var notsupercount = document.getElementById("form_not_present").childElementCount;
	var supervisors = new Array();
	var arrsize = 0;
	
	for (var i = 0; i < supercount; i++) {
		if ($("#form_present" + i + "").val() != '') {
			supervisors[arrsize] = $("#form_present" + i + "").val();
			arrsize++;
		}
	}
	for (i = 0; i < notsupercount; i++) {
		if ($("#form_not_present" + i + "").val() != '') {
			supervisors[arrsize] = $("#form_not_present" + i + "").val();
			arrsize++;
		}
	}
	return supervisors;
}

function getEmails() {
	var supercount = document.getElementById("form_present").childElementCount;
	var notsupercount = document.getElementById("form_not_present").childElementCount;
	var emails = new Array();
	var arrsize = 0;
	
	for (var i = 0; i < supercount; i++) {
		if ($("#form_email" + i + "").val() != '') {
			emails[arrsize] = $("#form_email" + i + "").val();
			arrsize++;
		}
	}
	for (i = 0; i < notsupercount; i++) {
		if ($("#form_not_email" + i + "").val() != '') {
			emails[arrsize] = $("#form_not_email" + i + "").val();
			arrsize++;
		}
	}
	return emails;
}

function updateSupervisors() {
	var supervisors = getSupervisors();
	var emails = getEmails();
	var arrsize = supervisors.length;
	var value = '';
	
	$(".form_superbox").each(function() {
		value = $(this).val();
		
		$(this).empty();
		$(this).append( '<option value=""></option>' );
		$(this).append( '<option value="Info">Info</option>' );
		$(this).append( '<option value="Klart">Klart</option>' );
		$(this).val('');
		for (i = 0; i < arrsize; i++) {
			$(this).append( '<option value="' + emails[i] + '">' + supervisors[i] + '</option>' );
			$(this).val(supervisors[i]);
		}
		$(this).val(value);
    });
}

function addSuperField(id,index) {
	var count = document.getElementById("form_supervisor_col" + id + "" + index + "").childElementCount;
	
	if (count < 3) {
		var supervisors = getSupervisors();
		var emails = getEmails();
		var arrsize = supervisors.length;
		
		var superstring = '<select name="supervisor' + id + '' + index + '' + count + '" class="form_superbox" id="form_supervisor' + id + '' + index + '' + count + '"><option value=""></option><option value="Info">Info</option><option value="Klart">Klart</option>';
		
		for (i = 0; i < arrsize; i++) {
			superstring += '<option value="' + emails[i] + '">' + supervisors[i] + '</option>';
		}
		superstring += '</select>';
		
		var removestring = '<input type="button" onclick="removeSuperField(' + id + ',' + index + ',' + count + ')" value="Ta bort ansvarig" class="form_supervisor_add_button" id="form_supervisor_remove_button' + id + '' + index + '' + count + '"/>';
		
		$('#form_supervisor_col' + id + '' + index + '').append(superstring);
		$('#form_tools_col' + id + '' + index + '').append(removestring);
		
		count = document.getElementById("form_supervisor_col" + id + "" + index + "").childElementCount;
		$('#form_super_count' + id + '' + index + '').val(count);
	} else {
		alert("Max 3 st ansvariga per rubrik!");
	}
}

function removeSuperField(id,task,index) {
	if (confirm('Ta bort ansvarig?')) {
		var div = document.getElementById("form_supervisor_col" + id + "" + task + "");
		var toolsdiv = document.getElementById("form_tools_col" + id + "" + task + "");
		var count = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < count; i++) {
			next = i + 1;
			
			$("#form_supervisor" + id + "" + task + "" + i + "").val($("#form_supervisor" + id + "" + task + "" + next + "").val());
		}
		div.removeChild(div.lastChild);
		toolsdiv.removeChild(toolsdiv.lastChild);
		
		count = div.childElementCount;
		$('#form_super_count' + id + '' + task + '').val(count);
	}
}

function addPresentField() {
	var count = document.getElementById("form_present").childElementCount;
	
	$('#form_present').append( '<div>' + (count + 1) + '. <input type="text" name="present' + count + '" class="form_textbox" id="form_present' + count + '" onchange="isLetterOrSpaceKey(this); updateSupervisors();"/><input type="text" name="company' + count + '" class="form_textbox" id="form_company' + count + '"/><input type="text" name="email' + count + '" class="form_textbox" id="form_email' + count + '" onchange="updateSupervisors()"/><input type="button" onclick="removePresentField(' + count + ')" value="Ta bort rad" class="form_remove_button" id="form_present_remove_button' + count + '"/></div>' );

	document.getElementById("form_present_remove_button" + count + "").style.display = "inline";
	
	count = document.getElementById("form_present").childElementCount;
	$('#form_present_count').val(count);
}

function removePresentField(index) {
	if (confirm('Ta bort närvarande rad: ' + (index + 1) + '?')) {
		var div = document.getElementById("form_present");
		var count = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < count; i++) {
			next = i + 1;
			
			$("#form_present" + i + "").val($("#form_present" + next + "").val());
			$("#form_company" + i + "").val($("#form_company" + next + "").val());
			$("#form_email" + i + "").val($("#form_email" + next + "").val());
		}
		div.removeChild(div.lastChild);
		
		count = div.childElementCount;
		$('#form_present_count').val(count);
		
		updateSupervisors();
	}
}

function addNotPresentField() {
	var count = document.getElementById("form_not_present").childElementCount;
	
	$('#form_not_present').append( '<div>' + (count + 1) + '. <input type="text" name="notpresent' + count + '" class="form_textbox" id="form_not_present' + count + '" onchange="isLetterOrSpaceKey(this); updateSupervisors();"/><input type="text" name="notcompany' + count + '" class="form_textbox" id="form_not_company' + count + '"/><input type="text" name="notemail' + count + '" class="form_textbox" id="form_not_email' + count + '" onchange="updateSupervisors()"/><input type="button" onclick="removeNotPresentField(' + count + ')" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button' + count + '"/></div>' );

	document.getElementById("form_not_present_remove_button0").style.display = "inline";
	document.getElementById("form_not_present_remove_button" + count + "").style.display = "inline";
	
	count = document.getElementById("form_not_present").childElementCount;
	$('#form_not_present_count').val(count);
}

function removeNotPresentField(index) {
	if (confirm('Ta bort ej närvarande rad: ' + (index + 1) + '?')) {
		var div = document.getElementById("form_not_present");
		var count = div.childElementCount;
		var next = 0;
		
		for (var i = index; i < count; i++) {
			next = i + 1;
			
			$("#form_not_present" + i + "").val($("#form_not_present" + next + "").val());
			$("#form_not_company" + i + "").val($("#form_not_company" + next + "").val());
			$("#form_not_email" + i + "").val($("#form_not_email" + next + "").val());
		}
		if (count > 1) {
			div.removeChild(div.lastChild);
		} else {
			$("#form_not_present" + i + "").val('');
			$("#form_not_company" + i + "").val('');
			$("#form_not_email" + i + "").val('');
		}
		count = div.childElementCount;
		$('#form_not_present_count').val(count);
		
		updateSupervisors();
	}
}

function moveToPresent(id) {
	var div = document.getElementById("form_not_present");
	var count = div.childElementCount;
	
	var presentdiv = document.getElementById("form_present");
	var presentcount = presentdiv.childElementCount;
	
	var movename = $("#form_not_present" + id + "").val();
	var movecompany = $("#form_not_company" + id + "").val();
	var moveemail = $("#form_not_email" + id + "").val();
	
	var name = '';
	var company = '';
	var email = '';
	
	var next = 0;
	
	$('#form_present').append( '<div>' + (presentcount + 1) + '. <input type="text" name="present' + presentcount + '" class="form_textbox" id="form_present' + presentcount + '" value="' + movename + '" onchange="isLetterOrSpaceKey(this); updateSupervisors();" /><input type="text" name="company' + presentcount + '" class="form_textbox" id="form_company' + presentcount + '" value="' + movecompany + '"/><input type="text" name="email' + presentcount + '" class="form_textbox" id="form_email' + presentcount + '" value="' + moveemail + '" onchange="updateSupervisors()"/><input type="button" onclick="moveToNotPresent(' + presentcount + ')" value="Ej närvarande" class="form_move_super_button" id="form_move_not_present_button' + presentcount + '"/><input type="button" onclick="removePresentField(' + presentcount + ')" value="Ta bort rad" class="form_remove_button" id="form_present_remove_button' + presentcount + '" style="display: inline;"/></div>' );

	for (var i = id; i < count; i++) {
		next = i + 1;
		
		$("#form_not_present" + i + "").val($("#form_not_present" + next + "").val());
		$("#form_not_company" + i + "").val($("#form_not_company" + next + "").val());
		$("#form_not_email" + i + "").val($("#form_not_email" + next + "").val());
	}
	if (count > 1) {
		div.removeChild(div.lastChild);
	} else {
		$("#form_not_present0").val('');
		$("#form_not_company0").val('');
		$("#form_not_email0").val('');
		document.getElementById("form_move_present_button0").style.display = "none";
	}
	$("#form_present_count").val(presentdiv.childElementCount);
	$("#form_not_present_count").val(div.childElementCount);
}

function moveToNotPresent(id) {
	var div = document.getElementById("form_present");
	var count = div.childElementCount;
	
	var notpresentdiv = document.getElementById("form_not_present");
	var notpresentcount = notpresentdiv.childElementCount;
	
	var movename = $("#form_present" + id + "").val();
	var movecompany = $("#form_company" + id + "").val();
	var moveemail = $("#form_email" + id + "").val();
	
	var name = '';
	var company = '';
	var email = '';
	
	var next = 0;
	
	$('#form_not_present').append( '<div>' + (notpresentcount + 1) + '. <input type="text" name="notpresent' + notpresentcount + '" class="form_textbox" id="form_not_present' + notpresentcount + '" value="' + movename + '" onchange="isLetterOrSpaceKey(this); updateSupervisors();" /><input type="text" name="notcompany' + notpresentcount + '" class="form_textbox" id="form_not_company' + notpresentcount + '" value="' + movecompany + '"/><input type="text" name="notemail' + notpresentcount + '" class="form_textbox" id="form_not_email' + notpresentcount + '" value="' + moveemail + '" onchange="updateSupervisors()"/><input type="button" onclick="moveToPresent(' + notpresentcount + ')" value="Närvarande" class="form_move_super_button" id="form_move_present_button' + notpresentcount + '"/><input type="button" onclick="removeNotPresentField(' + notpresentcount + ')" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button' + notpresentcount + '" style="display: inline;"/></div>' );

	for (var i = id; i < count; i++) {
		next = i + 1;
		
		$("#form_present" + i + "").val($("#form_present" + next + "").val());
		$("#form_company" + i + "").val($("#form_company" + next + "").val());
		$("#form_email" + i + "").val($("#form_email" + next + "").val());
	}
	if (count > 1) {
		div.removeChild(div.lastChild);
	}
	notpresentcount = notpresentdiv.childElementCount;
	if (notpresentcount > 1) {
		document.getElementById("form_not_present_remove_button0").style.display = "inline";
	}
	$("#form_present_count").val(div.childElementCount);
	$("#form_not_present_count").val(notpresentdiv.childElementCount);
}

function addTaskField(id) {
	var count = document.getElementById("form_content_task_rows" + id + "").childElementCount;
	var meeting = $("#form_meeting_id").val();
	var maxid = +$("#form_maxid" + id + "").val();
	var string = '' + meeting + '_' + id + '.' + maxid + '';
	
	var supervisors = getSupervisors();
	var emails = getEmails();
	var arrsize = supervisors.length;

	var superstring = '<select name="supervisor' + id + '' + count + '0" class="form_superbox" id="form_supervisor' + id + '' + count + '0"><option value=""></option><option value="Info">Info</option><option value="Klart">Klart</option>';
	
	for (i = 0; i < arrsize; i++) {
		superstring += '<option value="' + emails[i] + '">' + supervisors[i] + '</option>';
	}
	superstring += '</select>';
	
	$('#form_content_task_rows' + id + '').append( '<div class="content_row"><div class="content_col content_small_col"><input type="hidden" id="form_task_id' + id + '' + count + '" value="' + string + '"/>' + string + '</div><div class="content_col"><input type="text" name="task' + id + '' + count + '" class="form_textbox form_headerbox" id="form_task' + id + '' + count + '" maxlength="255"/></div><input type="hidden" name="superrows' + id + '' + count + '" id="form_super_count' + id + '' + count + '" value="1"/><div class="content_col content_supervisor_col" id="form_supervisor_col' + id + '' + count + '">' + superstring + '</div><div class="content_col content_tools_col" id="form_tools_col' + id + '' + count + '"><input type="button" onclick="removeTaskField(' + id + ',' + count + ')" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button' + id + '' + count + '"/><input type="button" onclick="addSuperField(' + id + ',' + count + ')" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button' + id + '' + count + '"/></div></div>' );

	document.getElementById("form_task_remove_button" + id + "0").style.display = "inline";
	document.getElementById("form_task_remove_button" + id + "" + count + "").style.display = "inline";
	
	count = document.getElementById("form_content_task_rows" + id + "").childElementCount;
	$('#form_task_count' + id + '').val(count);
	$("#form_maxid" + id + "").val(maxid + 1);
}

function removeTaskField(id,index) {
	if (confirm('Ta bort rad: ' + (index + 1) + '?')) {
		var div = document.getElementById("form_content_task_rows" + id + "");
		var count = div.childElementCount;
		var next = 0;
		
		var supervisors = getSupervisors();
		var emails = getEmails();
		var arrsize = supervisors.length;
		
		var tasksupercount = 0;
		var nexttasksupercount = 0;
		
		var superstring = '';
		
		for (var i = index; i < count; i++) {
			next = i + 1;
			
			$("#form_task" + id + "" + i + "").val($("#form_task" + id + "" + next + "").val());
			
			if (i !== count - 1) {
				tasksupercount = $("#form_super_count" + id + "" + i + "").val();
				nexttasksupercount = $("#form_super_count" + id + "" + next + "").val();
				superdiv = document.getElementById("form_supervisor_col" + id + "" + i + "");
				toolsdiv = document.getElementById("form_tools_col" + id + "" + i + "");
				
				if (tasksupercount > nexttasksupercount) {
					for (var j = 0; j < tasksupercount - nexttasksupercount; j++) {
						superdiv.removeChild(superdiv.lastChild);
						toolsdiv.removeChild(toolsdiv.lastChild);
					}
				}
				for (var j = 0; j < nexttasksupercount; j++) {
					if (j < tasksupercount) {
						$("#form_supervisor" + id + "" + i + "" + j + "").val($("#form_supervisor" + id + "" + next + "" + j + "").val());
					} else {
						superstring = '<select name="supervisor' + id + '' + i + '' + j + '" class="form_superbox" id="form_supervisor' + id + '' + i + '' + j + '"><option value=""></option>';
	
						if ($("#form_supervisor" + id + "" + next + "" + j + "").val() == 'Info') {
							superstring += '<option value="Info" selected>Info</option>';
							superstring += '<option value="Klart">Klart</option>';
						} else if ($("#form_supervisor" + id + "" + next + "" + j + "").val() == 'Klart') {
							superstring += '<option value="Info">Info</option>';
							superstring += '<option value="Klart" selected>Klart</option>';
						} else {
							superstring += '<option value="Info">Info</option>';
							superstring += '<option value="Klart">Klart</option>';
						}
						for (var z = 0; z < arrsize; z++) {
							if ($("#form_supervisor" + id + "" + next + "" + j + "").val() == emails[z]) {
								superstring += '<option value="' + emails[z] + '" selected>' + supervisors[z] + '</option>';
							} else {
								superstring += '<option value="' + emails[z] + '">' + supervisors[z] + '</option>';
							}
						}
						superstring += '</select>';
						$('#form_supervisor_col' + id + '' + i + '').append(superstring);
					}
				}
				$("#form_super_count" + id + "" + i + "").val(nexttasksupercount);
			}
		}
		div.removeChild(div.lastChild);
		
		count = div.childElementCount;
		
		if (count < 2) {
			document.getElementById("form_task_remove_button" + id + "0").style.display = "none";
		}
		$('#form_task_count' + id + '').val(count);
		$("#form_maxid" + id + "").val(+$("#form_maxid" + id + "").val() - 1);
	}
}

function addHeaderField() {
	var count = document.getElementById("form_content_rows").childElementCount;
	var meeting = $("#form_meeting_id").val();
	var string = '' + meeting + '_' + count + '.1';
	
	var supervisors = getSupervisors();
	var emails = getEmails();
	var arrsize = supervisors.length;

	var superstring = '<select name="supervisor' + count + '00" class="form_superbox" id="form_supervisor' + count + '00"><option value=""></option><option value="Info">Info</option><option value="Klart">Klart</option>';
	
	for (i = 0; i < arrsize; i++) {
		superstring += '<option value="' + emails[i] + '">' + supervisors[i] + '</option>';
	}
	superstring += '</select>';
	
	$('#form_content_rows').append( '<div class="task_row" id="form_content_row' + count + '"><div class="content_row content_row_header"><div class="content_col content_small_col">' + count + '</div><div class="content_col"><input type="text" name="header' + count + '" class="form_textbox form_headerbox" id="form_header' + count + '" maxlength="80"/></div><div class="content_col content_supervisor_col"></div><div class="content_col content_tools_col"><input type="button" onclick="removeHeaderField(' + count + ')" value="Ta Bort Rubrik" class="form_task_remove_button" id="form_header_remove_button' + count + '"/></div></div><input type="hidden" name="taskrows' + count + '" id="form_task_count' + count + '" value="1"/><input type="hidden" name="maxid' + count + '" id="form_maxid' + count + '" value="2"/><div id="form_content_task_rows' + count + '"><div class="content_row"><div class="content_col content_small_col"><input type="hidden" id="form_task_id' + count + '0" value="' + string + '"/>' + string + '</div><div class="content_col"><input type="text" name="task' + count + '0" class="form_textbox form_headerbox" id="form_task' + count + '0" maxlength="255"/></div><input type="hidden" name="superrows' + count + '0" id="form_super_count' + count + '0" value="1"/><div class="content_col content_supervisor_col" id="form_supervisor_col' + count + '0">' + superstring + '</div><div class="content_col content_tools_col" id="form_tools_col' + count + '0"><input type="button" onclick="removeTaskField(' + count + ',0)" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button' + count + '0"/><input type="button" onclick="addSuperField(' + count + ',0)" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button' + count + '0"/></div></div></div><input type="button" onclick="addTaskField(' + count + ')" value="Ny rad" class="form_task_add_button" id="form_task_add_button' + count + '"/></div>' );

	document.getElementById("form_header_remove_button0").style.display = "inline";
	document.getElementById("form_header_remove_button" + count + "").style.display = "inline";
	
	count = document.getElementById("form_content_rows").childElementCount;
	$('#form_content_count').val(count);
}

function removeHeaderField(id) {
	if (confirm('Ta bort rubrik: ' + id + ' och alla dess rader?')) {
		var div = document.getElementById("form_content_rows");
		var count = div.childElementCount;
		var meeting = $("#form_meeting_id").val();
		
		var taskdiv = '';
		var taskcount = 0;
		
		var nexttaskdiv = '';
		var nexttaskcount = 0;
		
		var next = 0;
		
		var string = '';
		
		var supervisors = getSupervisors();
		var emails = getEmails();
		var arrsize = supervisors.length;
		
		var superdiv = '';
		var toolsdiv = '';
		var tasksupercount = 0;
		var nexttasksupercount = 0;
		
		var mainsuperstring = '';
		var superstring = '';
		
		for (var i = id; i < count; i++) {
			next = i + 1;

			$("#form_header" + i + "").val($("#form_header" + next + "").val());
			$("#form_maxid" + i + "").val($("#form_maxid" + next + "").val());
			
			if (i !== count - 1) {
				taskdiv = document.getElementById("form_content_task_rows" + i + "");
				taskcount = taskdiv.childElementCount;
				
				nexttaskdiv = document.getElementById("form_content_task_rows" + next + "");
				nexttaskcount = nexttaskdiv.childElementCount;
				
				if (taskcount > nexttaskcount) {
					for (var j = 0; j < taskcount - nexttaskcount; j++) {
						taskdiv.removeChild(taskdiv.lastChild);
					}
				}
				for (var j = 0; j < nexttaskcount; j++) {
					tasksupercount = $("#form_super_count" + i + "" + j + "").val();
					nexttasksupercount = $("#form_super_count" + next + "" + j + "").val();
					superdiv = document.getElementById("form_supervisor_col" + i + "" + j + "");
					toolsdiv = document.getElementById("form_tools_col" + i + "" + j + "");
					
					if (tasksupercount > nexttasksupercount) {
						for (var z = 0; z < tasksupercount - nexttasksupercount; z++) {
							superdiv.removeChild(superdiv.lastChild);
							toolsdiv.removeChild(toolsdiv.lastChild);
						}
					}
					if (j < taskcount) {
						$("#form_task" + i + "" + j + "").val($("#form_task" + next + "" + j + "").val());
						
						for (var z = 0; z < nexttasksupercount; z++) {
							if (z < tasksupercount) {
								$("#form_supervisor" + i + "" + j + "" + z + "").val($("#form_supervisor" + next + "" + j + "" + z + "").val());
							} else {
								superstring = '<select name="supervisor' + i + '' + j + '' + z + '" class="form_superbox" id="form_supervisor' + i + '' + j + '' + z + '"><option value=""></option>';
								
								if ($("#form_supervisor" + next + "" + j + "" + z + "").val() == 'Info') {
									superstring += '<option value="Info" selected>Info</option>';
									superstring += '<option value="Klart">Klart</option>';
								} else if ($("#form_supervisor" + next + "" + j + "" + z + "").val() == 'Klart') {
									superstring += '<option value="Info">Info</option>';
									superstring += '<option value="Klart" selected>Klart</option>';
								} else {
									superstring += '<option value="Info">Info</option>';
									superstring += '<option value="Klart">Klart</option>';
								}
								for (var y = 0; y < arrsize; y++) {
									if ($("#form_supervisor" + next + "" + j + "" + y + "").val() == emails[y]) {
										superstring += '<option value="' + emails[y] + '" selected>' + supervisors[y] + '</option>';
									} else {
										superstring += '<option value="' + emails[y] + '">' + supervisors[y] + '</option>';
									}
								}
								superstring += '</select>';
								$('#form_supervisor_col' + i + '' + j + '').append(superstring);
							}
						}
						$("#form_super_count" + i + "" + j + "").val(nexttasksupercount);
					} else {
						mainsuperstring = '';
						string = '' + meeting + '_' + i + '.' + (j + 1) + '';
						
						for (var z = 0; z < nexttasksupercount; z++) {
							superstring = '<select name="supervisor' + i + '' + j + '' + z + '" class="form_superbox" id="form_supervisor' + i + '' + j + '' + z + '"><option value=""></option>';
							
							if ($("#form_supervisor" + next + "" + j + "" + z + "").val() == 'Info') {
								superstring += '<option value="Info" selected>Info</option>';
								superstring += '<option value="Klart">Klart</option>';
							} else if ($("#form_supervisor" + next + "" + j + "" + z + "").val() == 'Klart') {
								superstring += '<option value="Info">Info</option>';
								superstring += '<option value="Klart" selected>Klart</option>';
							} else {
								superstring += '<option value="Info">Info</option>';
								superstring += '<option value="Klart">Klart</option>';
							}
							for (var y = 0; y < arrsize; y++) {
								if ($("#form_supervisor" + next + "" + j + "" + z + "").val() == emails[y]) {
									superstring += '<option value="' + emails[y] + '" selected>' + supervisors[y] + '</option>';
								} else {
									superstring += '<option value="' + emails[y] + '">' + supervisors[y] + '</option>';
								}
							}
							superstring += '</select>';
							mainsuperstring += superstring;
						}
						$('#form_content_task_rows' + i + '').append( '<div class="content_row"><div class="content_col content_small_col"><input type="hidden" id="form_task_id' + i + '' + j + '" value="' + string + '"/>' + string + '</div><div class="content_col"><input type="text" name="task' + i + '' + j + '" class="form_textbox form_headerbox" id="form_task' + i + '' + j + '" maxlength="255" value="' + $("#form_task" + next + "" + j + "").val() + '"/></div><input type="hidden" name="superrows' + i + '' + j + '" id="form_super_count' + i + '' + j + '" value="' + z + '"/><div class="content_col content_supervisor_col" id="form_supervisor_col' + i + '' + j + '">' + mainsuperstring + '</div><div class="content_col content_tools_col" id="form_tools_col' + i + '' + j + '"><input type="button" onclick="removeTaskField(' + i + ',' + j + ')" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button' + i + '' + j + '"/><input type="button" onclick="addSuperField(' + i + ',' + j + ')" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button' + i + '' + j + '"/></div></div>' );
						
						document.getElementById("form_task_remove_button" + i + "0").style.display = "inline";
						document.getElementById("form_task_remove_button" + i + "" + j + "").style.display = "inline";

						$('#form_task_count' + i + '').val(j + 1);
					}
				}
			}
		}
		div.removeChild(div.lastChild);
		
		count = div.childElementCount;
		
		if (count < 2) {
			document.getElementById("form_header_remove_button" + id + "").style.display = "none";
		}
		$('#form_content_count').val(count);
	}
}

function updateSend(value) {
	if (value == 0) {
		$('#form_send_button_value').val("no");
	} else if (value == 1) {
		$('#form_send_button_value').val("yes");
	} else {
		$('#form_send_button_value').val("locked");
	}
}
