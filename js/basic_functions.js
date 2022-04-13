function getCurrentDate() {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth() + 1; // January is 0!
	var yyyy = today.getFullYear();

	if (dd < 10) {
		dd = '0'+dd
	} 
	if (mm < 10) {
		mm = '0'+mm
	} 
	var currentdate = yyyy + '-' + mm + '-' + dd;
	return currentdate;
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}     
    return true;
}

function isFloatNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode != 46 && (charCode < 48 || charCode > 57))) {
		return false;
	} else {
		var parts = evt.srcElement.value.split('.');
		if (parts.length > 1 && charCode == 46) {
			return false;
		}
	}       
    return true;
}

function isHyphenOrNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode != 45 && (charCode < 48 || charCode > 57))) {
		return false;
	} else {
		var parts = evt.srcElement.value.split('-');
		if (parts.length > 1 && charCode == 45) {
			return false;
		}
	}       
    return true;
}

function is2HyphenOrNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode != 45 && (charCode < 48 || charCode > 57))) {
		return false;
	} else {
		var parts = evt.srcElement.value.split('-');
		if (parts.length > 2 && charCode == 45) {
			return false;
		}
	}       
    return true;
}

function isLetterKey(t) {	
	t.value = t.value.replace(/[^a-zA-ZåäöÅÄÖ]/g, "");
}

function isLetterOrSpaceKey(t) {
    t.value = t.value.replace(/[^a-zA-ZåäöÅÄÖ\s]/g, "");
}

function validateEmail(email) {
	var re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
	return re.test(email);
}

function validateCompany(company) {
	var re = /^([0-9]{6})-([0-9]{4})$/;
	return re.test(company);
}

function homebutton() {
	window.location.href = "index.php";
}

function header_projects_dropdown() {
	document.getElementById("header_projects_dropdown_content").classList.toggle("header_dropdown_content_show");
}

function header_tools_dropdown() {
	document.getElementById("header_tools_dropdown_content").classList.toggle("header_dropdown_content_show");
}

window.onclick = function(event) {
	if (!event.target.matches('.header_dropbutton')) {
		var dropdowns = document.getElementsByClassName("header_dropdown_content");
		
		for (var i = 0; i < dropdowns.length; i++) {
			var openDropdown = dropdowns[i];
			
			if (openDropdown.classList.contains('header_dropdown_content_show')) {
				openDropdown.classList.remove('header_dropdown_content_show');
			}
		}
	}
}
