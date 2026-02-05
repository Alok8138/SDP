document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // HELPER FUNCTIONS
    // =========================================

    // Regex for Email Validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Regex for Password (min 8 chars, 1 uppercase, 1 number)
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
    // Regex for Name (min 3 chars, no numbers/special chars)
    const nameRegex = /^[a-zA-Z\s]{3,}$/;

    /**
     * Shows an error message for a specific input field
     * @param {HTMLElement} input - The input element
     * @param {string} message - The error message
     */
    function showError(input, message) {
        const formGroup = input.parentElement; // Assuming inputs are wrapped or direct children

        // Remove existing error if any
        clearError(input);

        // Add error class to input
        input.classList.add('input-error');

        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-text';
        errorDiv.innerText = message;

        // Insert error message after input
        input.insertAdjacentElement('afterend', errorDiv);
    }

    /**
     * Clears error message and styles from an input
     * @param {HTMLElement} input 
     */
    function clearError(input) {
        input.classList.remove('input-error');
        const nextSibling = input.nextElementSibling;
        if (nextSibling && nextSibling.classList.contains('error-text')) {
            nextSibling.remove();
        }
    }

    /**
     * Adds 'input' event listener to clear errors when user types
     * @param {HTMLElement} form 
     */
    function setupClearErrorOnInput(form) {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                clearError(this);
            });
        });
    }

    // =========================================
    // LOGIN FORM VALIDATION
    // =========================================
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        setupClearErrorOnInput(loginForm);

        loginForm.addEventListener('submit', function (e) {
            let isValid = true;

            // Validate Email
            const email = document.getElementById('loginEmail');
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!emailRegex.test(email.value.trim())) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            }

            // Validate Password
            const password = document.getElementById('loginPassword');
            if (!password.value.trim()) {
                showError(password, 'Password is required');
                isValid = false;
            } else if (password.value.trim().length < 6) {
                showError(password, 'Password must be at least 6 characters');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // =========================================
    // SIGNUP FORM VALIDATION
    // =========================================
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        setupClearErrorOnInput(signupForm);

        signupForm.addEventListener('submit', function (e) {
            let isValid = true;

            // Validate First Name
            const firstname = document.getElementById('signupFirstname');
            if (!firstname.value.trim()) {
                showError(firstname, 'First Name is required');
                isValid = false;
            } else if (!nameRegex.test(firstname.value.trim())) {
                showError(firstname, 'Name must be at least 3 letters and contain no numbers');
                isValid = false;
            }

            // Validate Last Name
            const lastname = document.getElementById('signupLastname');
            if (!lastname.value.trim()) {
                showError(lastname, 'Last Name is required');
                isValid = false;
            } else if (!nameRegex.test(lastname.value.trim())) {
                showError(lastname, 'Name must be at least 3 letters and contain no numbers');
                isValid = false;
            }

            // Validate Email
            const email = document.getElementById('signupEmail');
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!emailRegex.test(email.value.trim())) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            }

            // Validate Password
            const password = document.getElementById('signupPassword');
            if (!password.value.trim()) {
                showError(password, 'Password is required');
                isValid = false;
            } else if (!passwordRegex.test(password.value.trim())) {
                showError(password, 'Password must be 8+ chars, 1 uppercase, 1 number');
                isValid = false;
            }

            // Validate Confirm Password
            const confirmPassword = document.getElementById('signupConfirmPassword');
            if (confirmPassword) {
                if (!confirmPassword.value.trim()) {
                    showError(confirmPassword, 'Please confirm your password');
                    isValid = false;
                } else if (confirmPassword.value.trim() !== password.value.trim()) {
                    showError(confirmPassword, 'Passwords do not match');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
