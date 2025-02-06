document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('edit-button').addEventListener('click', function () {
        document.getElementById('emailSection').classList.add('d-none');
        document.getElementById('email-form-container').classList.remove('d-none');
    });

    document.getElementById('cancel-button').addEventListener('click', function () {
        document.getElementById('email-form-container').classList.add('d-none');
        document.getElementById('emailSection').classList.remove('d-none');
    });

        const fileInput = document.getElementById('file');
        const preview = document.querySelector('.custom-file-upload');
        const profilePictureForm = document.getElementById('profilePictureForm');

        fileInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                // Automatically submit the form
                profilePictureForm.submit();
            }
        });

    // Change password functionality
    const form = document.getElementById('changePasswordForm');
    const newPasswordField = document.getElementById('newPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    const togglePasswordVisibilityBtn = document.getElementById('togglePasswordVisibility');

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity() || !validatePasswordField() || !validateConfirmPasswordField() || !validatePasswordMatch()) {
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