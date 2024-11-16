document.addEventListener('DOMContentLoaded', function() {
    // Password Policy Section - Enforce Minimum Password Length
    const passwordMinLengthInput = document.querySelector('input[name="min_password_length"]');
    passwordMinLengthInput.addEventListener('change', function() {
        const minLength = parseInt(passwordMinLengthInput.value);
        if (minLength < 8) {
            alert("Minimum password length should be at least 8 characters.");
            passwordMinLengthInput.value = 8;
        }
    });

    // Encryption Section - Warn if encryption is not enabled
    const encryptionCheckbox = document.querySelector('input[name="enable_encryption"]');
    encryptionCheckbox.addEventListener('change', function() {
        if (!encryptionCheckbox.checked) {
            alert("It is highly recommended to enable data encryption for security purposes.");
        }
    });

    // Account Lockout Section - Ensure lockout duration is appropriate
    const lockoutAttemptsInput = document.querySelector('input[name="max_failed_attempts"]');
    const lockoutDurationInput = document.querySelector('input[name="lockout_duration"]');
    lockoutAttemptsInput.addEventListener('change', function() {
        if (parseInt(lockoutAttemptsInput.value) < 3) {
            alert("Max failed login attempts should not be less than 3 to prevent accidental lockouts.");
            lockoutAttemptsInput.value = 3;
        }
    });
    lockoutDurationInput.addEventListener('change', function() {
        if (parseInt(lockoutDurationInput.value) < 10) {
            alert("Lockout duration should be at least 10 minutes.");
            lockoutDurationInput.value = 10;
        }
    });

    // Compliance Management Section - Ensure at least one compliance standard is selected
    const complianceCheckboxes = document.querySelectorAll('input[name="compliance[]"]');
    const complianceForm = document.querySelector('form[action="update_compliance.php"]');
    complianceForm.addEventListener('submit', function(event) {
        let isComplianceSelected = false;
        complianceCheckboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                isComplianceSelected = true;
            }
        });
        if (!isComplianceSelected) {
            alert("Please select at least one compliance standard.");
            event.preventDefault();  // Prevent form submission
        }
    });

    // Role-Based Access Control Section - Ensure at least one permission is selected
    const permissionCheckboxes = document.querySelectorAll('input[name="permission[]"]');
    const accessControlForm = document.querySelector('form[action="update_access_control.php"]');
    accessControlForm.addEventListener('submit', function(event) {
        let isPermissionSelected = false;
        permissionCheckboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                isPermissionSelected = true;
            }
        });
        if (!isPermissionSelected) {
            alert("Please select at least one permission.");
            event.preventDefault();  // Prevent form submission
        }
    });

    // Password Expiration - Ensure it's not too short
    const passwordExpirationInput = document.querySelector('input[name="password_expiration"]');
    passwordExpirationInput.addEventListener('change', function() {
        if (parseInt(passwordExpirationInput.value) < 30) {
            alert("Password expiration should be at least 30 days.");
            passwordExpirationInput.value = 30;
        }
    });
});
