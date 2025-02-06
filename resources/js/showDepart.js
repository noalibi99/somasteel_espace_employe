import '/node_modules/jquery-ui/dist/jquery-ui'

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createEmployeeForm');
    const fields = form.querySelectorAll('input:not([id="fonction"], [id="solde_conge"], [id="responsable_hiarchique"], [id="directeur"], [id="service"], [id="password"], [id="password_confirmation"])');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');

    fields.forEach(field => {
        field.addEventListener('input', () => validateField(field));
    });

    passwordField.addEventListener('input', () => validatePasswordField());
    confirmPasswordField.addEventListener('input', () => validateConfirmPasswordField());

    form.addEventListener('submit', (event) => {
        if (!form.checkValidity() || !validatePasswordMatch()) {
            event.preventDefault();
            event.stopPropagation();
            fields.forEach(field => validateField(field));
            validatePasswordField();
            validateConfirmPasswordField();
        }
        form.classList.add('was-validated');
    });

    function validateField(field) {
        if (field.checkValidity()) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }
    }

    function validatePasswordField() {
        if (passwordField.value.length >= 8) {
            passwordField.classList.remove('is-invalid');
            passwordField.classList.add('is-valid');
        } else {
            passwordField.classList.remove('is-valid');
            passwordField.classList.add('is-invalid');
        }
    }

    function validateConfirmPasswordField() {
        if (confirmPasswordField.value === passwordField.value) {
            confirmPasswordField.classList.remove('is-invalid');
            confirmPasswordField.classList.add('is-valid');
        } else {
            confirmPasswordField.classList.remove('is-valid');
            confirmPasswordField.classList.add('is-invalid');
        }
    }

    function validatePasswordMatch() {
        return confirmPasswordField.value === passwordField.value;
    }

    const showPasswordCheckbox = document.getElementById('showPassword');
    const togglePasswordVisibilityBtn = document.getElementById('togglePasswordVisibility');

    togglePasswordVisibilityBtn.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        confirmPasswordField.setAttribute('type', type);
        // Change eye icon based on password visibility
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});
// document.addEventListener('DOMContentLoaded', function () {
//     const showPasswordCheckbox = document.getElementById('showPassword');
//     const passwordField = document.getElementById('password');
//     const confirmPasswordField = document.getElementById('password_confirmation');

    
// });
$(document).ready(function(){
    
    //convert data to map, key value

    var responsableMap = {};
    var directeurMap = {};

    // Populate the maps
    responsables.forEach(function (responsable) {
        responsableMap[responsable.matricule] = responsable.nom + ' ' + responsable.prénom;
    });
    directeurs.forEach(function (directeur) {
        directeurMap[directeur.matricule] = directeur.nom + ' ' + directeur.prénom;
    });

    // Autocomplete for Responsable Hiérarchique
    $("#responsable_hiarchique").autocomplete({
        source: Object.values(responsableMap),
        minLength: 0,
        select: function (event, ui) {
            var selectedName = ui.item.value;
            var selectedMatricule = Object.keys(responsableMap).find(function (matricule) {
                return responsableMap[matricule] === selectedName;
            });
            $("#responsable_hiarchique").val(selectedName);
            $("#responsable_hiarchique_matricule").val(selectedMatricule || null);
            return false;
        }
    });

    $("#directeur").autocomplete({
        source: Object.values(directeurMap),
        minLength: 0,
        select: function (event, ui) {
            var selectedName = ui.item.value;
            var selectedMatricule = Object.keys(directeurMap).find(function (matricule) {
                return directeurMap[matricule] === selectedName;
            });
            // Set the selected matricule as the value of the input field
            $("#directeur").val(selectedName);
            $("#directeur_matricule").val(selectedMatricule || null);
            return false; // Prevent the default behavior of the widget
        },
    });
    
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var employeeId = button.data('employee-id');
        var employeeName = button.data('employee-name');
        var modal = $(this);
        modal.find('.modal-body #employeeNameToDelete').text(employeeName);
        $('#deleteForm').attr('action', '/Annuaire/delete/' + employeeId);
    });

    $('.employee-table').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/French.json",
        },
        dom: '<"top"fi>',
        paging: false,        // Enable pagination
        searching: true,     // Enable search/filter functionality
        ordering: true,      // Enable sorting
    });

});