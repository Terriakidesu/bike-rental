<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="https://kit.fontawesome.com/3d58b793b8.js" crossorigin="anonymous"></script>

</head>

<body>
    <div class="bg-dark p-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">

        <div class="card mb-3" style="width:700px">
            <div class="col">
                <div class="card-header">
                    <h1 class="card-title">
                        <i class="fa-regular fa-circle-user"></i>
                        Register
                    </h1>
                </div>
                <div class="card-body">
                    <form method="post" id="form" action="api/auth/register.php" class="pt-2 px-2 text-black col">
                        <div class="row">
                            <div class="col">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" required>
                                <div id="firstname-feedback" class="invalid-feedback" style="display: none;">First name
                                    must be at least 2 characters long</div>
                            </div>
                            <div class="col">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" required>
                                <div id="lastname-feedback" class="invalid-feedback" style="display: none;">Last name
                                    must
                                    be at least 2 characters long</div>
                            </div>
                        </div>
                        <div class="col my-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div id="username-feedback" class="invalid-feedback" style="display: none;">Username must be
                                3-20 characters and contain only letters, numbers, and underscores</div>
                        </div>
                        <div class="col my-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div id="password-feedback" class="invalid-feedback" style="display: none;">Password must
                                meet
                                all requirements below</div>
                            <div id="passwordHelpBlock" class="form-text mt-2">
                                <small>
                                    <div id="password-length" class="mb-1" style="color: #6c757d;">
                                        <i class="fa-regular fa-circle"></i> 8-20 characters
                                    </div>
                                    <div id="password-alphanumeric" class="mb-1" style="color: #6c757d;">
                                        <i class="fa-regular fa-circle"></i> Contains letters and numbers
                                    </div>
                                    <div id="password-no-special" class="mb-1" style="color: #6c757d;">
                                        <i class="fa-regular fa-circle"></i> Only letters, numbers, and _@#$!.-
                                    </div>
                                </small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Register
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="errorModalLabel">Error</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                    <!-- Error message will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="successModalLabel">Success</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="successModalBody">
                    <!-- Success message will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        let formElement = document.getElementById("form");
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));

        // Function to show error modal
        function showErrorModal(message) {
            document.getElementById('errorModalBody').textContent = message;
            errorModal.show();
        }

        // Function to show success modal
        function showSuccessModal(message) {
            document.getElementById('successModalBody').textContent = message;
            successModal.show();
        }

        // Validation rules
        const validationRules = {
            firstname: {
                validate: (value) => value.trim().length >= 2,
                message: "First name must be at least 2 characters long"
            },
            lastname: {
                validate: (value) => value.trim().length >= 2,
                message: "Last name must be at least 2 characters long"
            },
            username: {
                validate: (value) => /^[a-zA-Z0-9_]{3,20}$/.test(value),
                message: "Username must be 3-20 characters and contain only letters, numbers, and underscores"
            },
            password: {
                validate: (value) => /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9_@#$!.\-]{8,20}$/.test(value),
                message: "Password must be 8-20 characters long and contain letters and numbers"
            }
        };

        // Password validation checks
        const passwordChecks = {
            length: (value) => value.length >= 8 && value.length <= 20,
            alphanumeric: (value) => /(?=.*[a-zA-Z])(?=.*\d)/.test(value),
            noSpecial: (value) => /^[a-zA-Z0-9_@#$!.\-]*$/.test(value)
        };

        // Function to validate individual field
        function validateField(fieldName, fieldValue) {
            const rule = validationRules[fieldName];
            if (!rule) return true;
            return rule.validate(fieldValue);
        }

        // Function to update password requirement indicators
        function updatePasswordIndicators(value) {
            const lengthCheck = document.getElementById("password-length");
            const alphanumericCheck = document.getElementById("password-alphanumeric");
            const noSpecialCheck = document.getElementById("password-no-special");

            if (lengthCheck) {
                if (passwordChecks.length(value)) {
                    lengthCheck.style.color = "#198754";
                    lengthCheck.innerHTML = '<i class="fa-solid fa-circle-check"></i> 8-20 characters';
                } else {
                    lengthCheck.style.color = value.length > 0 ? "#dc3545" : "#6c757d";
                    lengthCheck.innerHTML = '<i class="fa-regular fa-circle"></i> 8-20 characters';
                }
            }

            if (alphanumericCheck) {
                if (passwordChecks.alphanumeric(value)) {
                    alphanumericCheck.style.color = "#198754";
                    alphanumericCheck.innerHTML = '<i class="fa-solid fa-circle-check"></i> Contains letters and numbers';
                } else {
                    alphanumericCheck.style.color = value.length > 0 ? "#dc3545" : "#6c757d";
                    alphanumericCheck.innerHTML = '<i class="fa-regular fa-circle"></i> Contains letters and numbers';
                }
            }

            if (noSpecialCheck) {
                if (passwordChecks.noSpecial(value)) {
                    noSpecialCheck.style.color = "#198754";
                    noSpecialCheck.innerHTML = '<i class="fa-solid fa-circle-check"></i> Only letters, numbers, and _@#$!.-';
                } else {
                    noSpecialCheck.style.color = value.length > 0 ? "#dc3545" : "#6c757d";
                    noSpecialCheck.innerHTML = '<i class="fa-regular fa-circle"></i> Only letters, numbers, and _@#$!.-';
                }
            }
        }

        // Function to show error message
        function showError(inputElement, message) {
            inputElement.classList.add("is-invalid");
            inputElement.classList.remove("is-valid");
            const feedbackId = inputElement.id + "-feedback";
            const feedback = document.getElementById(feedbackId);
            if (feedback) {
                feedback.style.display = "block";
            }
        }

        // Function to clear error message
        function clearError(inputElement) {
            inputElement.classList.remove("is-invalid");
            inputElement.classList.add("is-valid");
            const feedbackId = inputElement.id + "-feedback";
            const feedback = document.getElementById(feedbackId);
            if (feedback) {
                feedback.style.display = "none";
            }
        }

        // Real-time validation on input
        Object.keys(validationRules).forEach((fieldName) => {
            const inputElement = document.getElementById(fieldName);
            if (inputElement) {
                inputElement.addEventListener("blur", () => {
                    const isValid = validateField(fieldName, inputElement.value);
                    if (isValid) {
                        clearError(inputElement);
                    } else {
                        showError(inputElement, validationRules[fieldName].message);
                    }
                });
            }
        });

        // Password real-time input validation
        const passwordInput = document.getElementById("password");
        if (passwordInput) {
            passwordInput.addEventListener("input", () => {
                updatePasswordIndicators(passwordInput.value);
                const isValid = validateField("password", passwordInput.value);
                if (isValid) {
                    clearError(passwordInput);
                } else {
                    showError(passwordInput, validationRules["password"].message);
                }
            });
        }

        // Password show/hide toggle
        const togglePasswordBtn = document.getElementById("togglePassword");
        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener("click", () => {
                const isPassword = passwordInput.type === "password";
                passwordInput.type = isPassword ? "text" : "password";
                togglePasswordBtn.innerHTML = isPassword ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
            });
        }

        formElement.addEventListener("submit", (event) => {
            event.preventDefault();

            // Validate all fields
            let isFormValid = true;
            const formData = new FormData(event.target);

            for (let [fieldName, fieldValue] of formData.entries()) {
                const isValid = validateField(fieldName, fieldValue);
                const inputElement = document.getElementById(fieldName);

                if (!inputElement) continue;

                if (isValid) {
                    clearError(inputElement);
                } else {
                    showError(inputElement, validationRules[fieldName].message);
                    isFormValid = false;
                }
            }

            if (isFormValid) {
                // Submit form via AJAX to handle JSON response
                const formDataObj = Object.fromEntries(formData);

                console.log(formDataObj)

                fetch(formElement.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formDataObj)
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {

                        if (data.status === 201) {
                            // Success - redirect or show success message
                            console.log("Registration successful:", data.message);
                            showSuccessModal(data.message);
                            // Redirect to login or home page

                            setTimeout(() => {
                                window.location.href = "login.php";
                            }, 1000);
                        } else if (data.status === 500 && data.message.includes('duplicate')) {
                            // Duplicate username error
                            const usernameInput = document.getElementById('username');
                            showError(usernameInput, data.message);
                        } else {
                            // Other errors
                            console.error("Registration error:", data.message);
                            showErrorModal(data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Request error:", error);
                        showErrorModal("An error occurred: " + error.message);
                    });
            } else {
                console.log("Form has validation errors");
            }
        });

    </script>

    <?php
    session_start();

    if (isset($_SESSION["user_authenticated"])) {
        header("Location: /");

        exit;
    }
    ?>

</body>

</html>