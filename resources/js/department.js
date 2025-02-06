import '/node_modules/jquery-ui/dist/jquery-ui'

$(document).ready(function () {
    $('#createDepartmentForm').on('submit', function (event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
	
	var departmentCards = document.querySelectorAll('.department-card');
    
    departmentCards.forEach(function(card, index) {
        if (index % 2 === 0) {
            card.classList.add('gray-background');
        } else {
            card.classList.add('white-background');
        }
    });
    
    var departmentFilter = document.getElementById('departmentFilter');
    var departmentItems = document.querySelectorAll('.department-item');

    // Event listener for input change
    departmentFilter.addEventListener('input', filterDepartments);

    // Filtering function
    function filterDepartments() {
        var filterValue = departmentFilter.value.trim().toLowerCase();

        departmentItems.forEach(function (item) {
            var departmentName = item.querySelector('.card-title').textContent.trim().toLowerCase();

            // Check if the department matches the search filter
            var isVisible = departmentName.includes(filterValue);

            // Adjust visibility based on the filter
            item.style.display = isVisible ? '' : 'none';
        });
    }

        
    document.querySelectorAll('.add-department-card').forEach((card) => {
        card.addEventListener('click', function() {
            var projectName = card.getAttribute('data-project');
            document.getElementById('project-name').value = projectName;
            document.getElementById('create-emp-projet').value = projectName;
        });
    });
    
    // Handle "Next" button click in the service creation modal
    document.getElementById('nextButton').addEventListener('click', function() {
        // Get service name and set the project name in the hidden field
        const serviceName = document.getElementById('nomService').value;
        document.getElementById('user-service-name').value = serviceName;
        
        // Hide the create department modal
        const createDepartmentModal = new bootstrap.Modal(document.getElementById('createDepartmentModal'));
        createDepartmentModal.hide();
        
        // Show the create user modal
        const createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
        createUserModal.show();
    });
    // Handle deletion button click
    const deleteButtons = document.querySelectorAll('.delete-department-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const project = this.getAttribute('data-project');
            const department = this.getAttribute('data-department');

            // Set values in hidden inputs
            document.getElementById('projectToDelete').value = project;
            document.getElementById('departmentToDelete').value = department;
        });
    });


    // create user 
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
});