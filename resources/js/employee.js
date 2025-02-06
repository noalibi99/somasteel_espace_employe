//import library
import '/node_modules/jquery-ui/dist/jquery-ui'

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('edit-button').addEventListener('click', function () {
        toggleEditMode(true);
    });

    document.getElementById('cancel-button').addEventListener('click', function () {
        toggleEditMode(false);
    });

    // Check if URL contains edit parameter and trigger edit mode if it does
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('edit')) {
        toggleEditMode(true);
    }

    //change pass
    const form = document.getElementById('changePasswordForm');
    const newPasswordField = document.getElementById('newPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    const togglePasswordVisibilityBtn = document.getElementById('togglePasswordVisibility');

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity() || !validatePasswordMatch() || !validatePasswordField() || !validateConfirmPasswordField()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    newPasswordField.addEventListener('input', function () {
        validatePasswordField();
        validatePasswordMatch();
    });

    confirmPasswordField.addEventListener('input', function () {
        validateConfirmPasswordField();
        validatePasswordMatch();
    });

    togglePasswordVisibilityBtn.addEventListener('click', function () {
        const type = newPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        newPasswordField.setAttribute('type', type);
        confirmPasswordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    function toggleEditMode(editMode) {
        const displayElements = document.querySelectorAll('.field-display');
        const editElements = document.querySelectorAll('.field-edit');
        displayElements.forEach(element => {
            element.classList.toggle('d-none', editMode);
        });
        editElements.forEach(element => {
            element.classList.toggle('d-none', !editMode);
        });
        document.getElementById('edit-button').classList.toggle('d-none', editMode);
        document.getElementById('save-button').classList.toggle('d-none', !editMode);
        document.getElementById('cancel-button').classList.toggle('d-none', !editMode);
    }

    function validatePasswordField() {
        const password = newPasswordField.value;
        if (password.length < 8) {
            newPasswordField.setCustomValidity('Password must be at least 8 characters long.');
        } else {
            newPasswordField.setCustomValidity('');
        }
        return newPasswordField.checkValidity();
    }

    function validateConfirmPasswordField() {
        const confirmPassword = confirmPasswordField.value;
        if (confirmPassword.length < 8) {
            confirmPasswordField.setCustomValidity('Confirm Password must be at least 8 characters long.');
        } else {
            confirmPasswordField.setCustomValidity('');
        }
        return confirmPasswordField.checkValidity();
    }

    function validatePasswordMatch() {
        const password = newPasswordField.value;
        const confirmPassword = confirmPasswordField.value;
        if (password !== confirmPassword) {
            confirmPasswordField.setCustomValidity('Passwords do not match.');
        } else {
            confirmPasswordField.setCustomValidity('');
        }
        return confirmPasswordField.checkValidity();
    }
});

// jQuery code
$(document).ready(function () {
    // const responsables = ["John Doe", "Jane Smith", "Emily Johnson"];
    
    //convert data to map, key value
    
    var responsableMap = {};
    var directeurMap = {};
    //mapage key value
    responsables.forEach(function (responsable) {
        responsableMap[responsable.matricule] = responsable.nom + ' ' + responsable.prénom;
    });
    directeurs.forEach(function (directeur) {
        directeurMap[directeur.matricule] = directeur.nom + ' ' + directeur.prénom;
    });

    // Now you have a key-value array where the key is matricule and the value is the concatenated nom prénom
    
    $("#responsable_hiarchique").autocomplete({
    	source: Object.values(responsableMap),
    	minLength: 0,
    	select: function (event, ui) {
	        var selectedName = ui.item.value;
    	    var selectedMatricule = Object.keys(responsableMap).find(function (matricule) {
            return responsableMap[matricule] === selectedName;
        });
        // Set the selected matricule as the value of the input field
        $("#responsable_hiarchique").val(selectedName);
        $("#responsable_hiarchique_matricule").val(selectedMatricule || '');

        	return false; // Prevent the default behavior of the widget
    	},
	}).on('change', function () {
    	if ($(this).val() == '') {
        	$("#responsable_hiarchique_matricule").val(null);
    	}
	});
    $("#directeur").autocomplete({
        source: Object.values(directeurMap),
        minLength: 0,
        select: function (event, ui){
            var selectedName = ui.item.value;
            var selectedMatricule = Object.keys(directeurMap).find(function (matricule) {
                return directeurMap[matricule] === selectedName;
            });
            // Set the selected matricule as the value of the input field
            $("#directeur").val(selectedName);
            $("#directeur_matricule").val(selectedMatricule ? selectedMatricule : null);
            return false; // Prevent the default behavior of the widget
        },
    }).on('change', function () {
        if ($(this).val() == '') {
            $("#directeur_matricule").val(null);
        }
    });
    // .on('focus', function () {
    //     // Trigger the search event to display the dropdown when the input gains focus
    //     $(this).autocomplete("search", "");
    // });

    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var employeeId = button.data('employee-id');
        var employeeName = button.data('employee-name');
        var modal = $(this);
        modal.find('.modal-body #employeeNameToDelete').text(employeeName);
        $('#deleteForm').attr('action', '/Annuaire/delete/' + employeeId);
    });
});
