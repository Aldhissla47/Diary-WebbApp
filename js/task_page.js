function validateForm() {
	$('#form_taskcount').val(document.getElementById("task_rows").childElementCount);
	if (confirm('Spara?')) {
		var count = $('#form_taskcount').val();
		var id = '';
		var category = '';
		var question = '';
		var supervisor = '';
		var deadline = '';
		
		var date = getCurrentDate();
		
		for (var i = 0; i < count; i++) {
			id = $('#form_id' + i + '').val();
			category = $('#form_category' + i + '').val();
			question = $('#form_question' + i + '').val();
			supervisor = $('#form_supervisor' + i + '0').val();
			deadline = $('#form_deadline' + i + '').val();
			answer = $('#form_answer' + i + '').val();
			
			if (category == "" && question == "" && supervisor == "" && deadline == "") {
				
			} else {
				if (category == "") {
					alert("Fyll i Kategori för uppgift: " + (i + 1) + "!");
					return false;
				}
				if (question == "") {
					alert("Fyll i Fråga/Info för uppgift: " + (i + 1) + "!");
					return false;
				}
				if (supervisor == "") {
					alert("Välj Ansvarig för uppgift: " + (i + 1) + "!");
					return false;
				}
				if (deadline != "" && deadline < date) {
					alert("Senast utförda datum kan ej vara ett redan passerat datum!");
					return false;
				}
			}
		}
		return true;
	}
	return false;
}

function validateDeadline(id) {
	var date = getCurrentDate();
	var deadline = $('#form_deadline' + id + '').val();
	
	if (deadline != "" && deadline < date) {
		$('#form_deadline' + id + '').val("");
		alert("Senast utförda datum kan ej vara ett redan passerat datum!");
	}
}

function addRow() {
	var count = document.getElementById("task_rows").childElementCount;
	var startstring = '';
	var idclassstring = '';
	var user = $('#form_user').val();
	var username = $('#form_username').val();
	var maxid = $('#form_maxid').val();
	
	var currentdate = getCurrentDate();
	
	var options = $('#form_supervisor' + (count - 1) + '0 option');
	var values = $.map(options ,function(option) {
		return option.value;
	});
	var texts = $.map(options ,function(option) {
		return option.text;
	});
	var optioncount = values.length;
	var supervisorstring = '<select name="supervisor' + count + '0" class="task_row_box task_row_dropbox" id="form_supervisor' + count + '0" onchange="supervisorUpdate(' + count + ')"><option value=""></option>';
	
	for (var i = 0; i < optioncount; i++) {
		if (values[i] != '' && texts[i] != '') {
			supervisorstring += '<option value="' + values[i] + '">' + texts[i] + '</option>';
		}
	}
	supervisorstring += '</select>';
	
	startstring = '<div id="task_row' + count + '" class="task_row">';

	if (count == 0) {
		maxid = parseInt(maxid);
	} else {
		maxid = parseInt(maxid) + 1;
	}
	$('#task_rows').append( '' + startstring + '<div class="task_column task_number_column"><input type="hidden" name="id' + count + '" id="form_id' + count + '" value="' + (maxid) + '"/><p>' + (maxid) + '</p></div><div class="task_column task_small_column"><input type="text" name="category' + count + '" class="task_row_box" id="form_category' + count + '" maxlength="40"/></div><div class="task_column task_date_column"><input type="text" name="created' + count + '" class="task_row_box" id="form_created' + count + '" value="' + currentdate + '" readonly/></div><div class="task_column task_large_column"><input type="text" name="question' + count + '" class="task_row_box_large" id="form_question' + count + '" maxlength="255"/></div><div class="task_column task_small_column">' + supervisorstring + '</div><div class="task_column task_small_column"><input type="hidden" name="author' + count + '" id="form_author' + count + '" value="' + user + '"/><input type="text" class="task_row_box" id="form_authorname' + count + '" value="' + username + '" readonly/></div><div class="task_column task_date_column"><input type="date" name="deadline' + count + '" class="task_row_box" id="form_deadline' + count + '" onchange="return validateDeadline(' + count + ')"/></div><div class="task_column task_large_column" id="task_answer_column' + count + '"></div><div class="task_column task_date_column" id="task_completed_column' + count + '"></div><div class="task_column task_small_column" id="task_worker_column' + count + '"></div><div class="task_column task_private_column"><input type="checkbox" name="private' + count + '" class="task_row_checkbox" id="form_private' + count + '" value="1"/></div><input type="button" onclick="removeRow(' + count + ')" value="Ta bort rad" class="form_remove_button" id="form_remove_button' + count + '"/></div>' );
	
	document.getElementById("form_remove_button" + count + "").style.display = "inline";
	$('#form_maxid').val(maxid);
	
	count = document.getElementById("task_rows").childElementCount;
	$('#form_taskcount').val(count);
	
	var div = document.getElementById("task_content");
	div.scrollTop = div.scrollHeight - div.clientHeight;
}

function removeRow(index) {
	if (confirm('Ta bort rad: ' + (index + 1) + '?')) {
		var div = document.getElementById("task_rows");
		var count = document.getElementById("task_rows").childElementCount;
		var next = 0;
		
		for (var i = index; i < count; i++) {
			next = i + 1;
			
			$("#form_id" + i + "").val($("#form_id" + next + "").val());
			$("#form_category" + i + "").val($("#form_category" + next + "").val());
			$("#form_created" + i + "").val($("#form_created" + next + "").val());
			$("#form_question" + i + "").val($("#form_question" + next + "").val());
			$("#form_supervisor" + i + "").val($("#form_supervisor" + next + "").val());
			$("#form_author" + i + "").val($("#form_author" + next + "").val());
			$("#form_answer" + i + "").val($("#form_answer" + next + "").val());
			$("#form_completed" + i + "").val($("#form_completed" + next + "").val());
			$("#form_worker" + i + "").val($("#form_worker" + next + "").val());
		}
		div.removeChild(div.lastChild);
		
		count = document.getElementById("task_rows").childElementCount;
		
		$('#form_taskcount').val(count);
		$('#form_maxid').val($('#form_maxid').val() - 1);
	}
}

function supervisorUpdate(id) {
	var count = document.getElementById('task_answer_column' + id + '').childElementCount;
	var value = $('#form_supervisor' + id + '0').val();
	var username = $('#form_username').val();
	var user = $('#form_user').val();
	
	var currentdate = getCurrentDate();
	
	if (value == user) {
		if (count == 0) {
			$('#task_answer_column' + id + '').append( '<input type="text" name="answer' + id + '" class="task_row_box_large" id="form_answer' + id + '" maxlength="255"/>' );
			$('#task_completed_column' + id + '').append( '<input type="text" name="completed' + id + '" class="task_row_box" id="form_completed' + id + '" value="' + currentdate + '" readonly/>' );
			$('#task_worker_column' + id + '').append( '<input type="hidden" id="form_worker' + id + '" value="' + user + '"/><input type="text" class="task_row_box" id="form_workername' + id + '" value="' + username + '" readonly/>' );
		}
	} else {
		if (count > 0) {
			document.getElementById('task_answer_column' + id + '').removeChild(document.getElementById('task_answer_column' + id + '').lastChild);
			document.getElementById('task_completed_column' + id + '').removeChild(document.getElementById('task_completed_column' + id + '').lastChild);
			document.getElementById('task_worker_column' + id + '').removeChild(document.getElementById('task_worker_column' + id + '').lastChild); // input
			document.getElementById('task_worker_column' + id + '').removeChild(document.getElementById('task_worker_column' + id + '').lastChild); // hidden
		}
	}
}
