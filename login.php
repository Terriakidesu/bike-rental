<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="https://kit.fontawesome.com/3d58b793b8.js" crossorigin="anonymous"></script>

</head>

<body>
    <div class="bg-dark p-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">

        <div class="card mb-3" style="width:500px">
            <div class="col">
                <div class="card-header">
                    <h1 class="card-title">
                        <i class="fa-regular fa-circle-user"></i>
                        Login
                    </h1>
                </div>
                <div class="card-body">
                    <form method="post" id="form" action="api/auth/login.php" class="pt-2 px-2 text-black col">
                        <div class="col my-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div id="username-feedback" class="invalid-feedback" style="display: none;">Username is
                                required</div>
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
                            <div id="password-feedback" class="invalid-feedback" style="display: none;">Password is
                                required</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
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
            username: {
                validate: (value) => value.trim().length > 0,
                message: "Username is required"
            },
            password: {
                validate: (value) => value.length > 0,
                message: "Password is required"
            }
        };

        // Function to validate individual field
        function validateField(fieldName, fieldValue) {
            const rule = validationRules[fieldName];
            if (!rule) return true;
            return rule.validate(fieldValue);
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

        // Password show/hide toggle
        const passwordInput = document.getElementById("password");
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
                        if (data.status === 200) {
                            // Success - redirect or show success message
                            console.log("Login successful:", data.message);
                            showSuccessModal(data.message);
                            // Redirect to dashboard or home page
                            // window.location.href = '/dashboard.php';
                        } else if (data.status === 401) {
                            // Invalid credentials
                            showErrorModal(data.message);
                        } else {
                            // Other errors
                            console.error("Login error:", data.message);
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