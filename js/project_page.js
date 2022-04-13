function validateRemoveForm(id) {
	if (confirm('Vill du verkligen ta bort ' + $('#project_member_remove_form_name' + id + '').val() + ' från detta projekt?')) {
		return true;
	}
	return false;
}
