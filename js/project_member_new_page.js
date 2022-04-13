function validateForm() {
	if (confirm("Spara?")) {
		var fname = document.forms["form"]["name"].value;
		var sname = document.forms["form"]["surname"].value;
		var pnumber1 = document.forms["form"]["phonenumber1"].value;
		var pnumber2 = document.forms["form"]["phonenumber2"].value;
		var email = document.forms["form"]["email"].value;
		var company = document.forms["form"]["company"].value;
		
		if (fname == "") {
			alert("Fyll i Förnamn!");
			return false;
		}
		if (sname == "") {
			alert("Fyll i Efternamn!");
			return false;
		}
		if (pnumber1 == "" || pnumber2 == "") {
			alert("Fyll i Telefonnummer!");
			return false;
		}
		if (email == "") {
			alert("Fyll i Email!");
			return false;
		} else {
			if (!validateEmail(email)) {
				alert("Ogiltig Email!");
				return false;
			}
		}
		if (company == "") {
			alert("Fyll i Företag!");
			return false;
		} else {
			if (!validateCompany(company)) {
				alert("Ogiltigt Organisationsnummer!");
				return false;
			}
		}
		return true;
	}
	return false;
}

function validateCompanyNumber() {
	var company = $('#form_company').val();
	
	$.ajax({
		url:'get_company_info.php',
		type: 'POST',
		data: {orgnumber: company, info: 'name'},
		success: function(data) {
			if (data == 'null') {
				alert('Kunde ej hitta företag med detta organisationsnummer registrerat!');
				$('#form_company').val('');
				$('#form_company_name').val('');
			} else {
				$('#form_company_name').val(data);
			}
		}
	});
}
