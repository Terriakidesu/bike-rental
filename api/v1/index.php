<?php
session_start();

// Define valid admin credentials (use hashed passwords in production)
$valid_credentials = [
    'admin' => password_hash('password123', PASSWORD_BCRYPT)
];

// Check if user is already authenticated
if (isset($_SESSION['admin_authenticated'])) {
    echo "<p>Welcome back {$_SESSION['admin_user']}!</p>";
    exit;
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        isset($valid_credentials[$username]) &&
        password_verify($password, $valid_credentials[$username])
    ) {
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_user'] = $username;
        echo "<p>Login successful! Hello $username.</p>";
    } else {
        http_response_code(401);
        echo "<p>Invalid credentials.</p>";
    }
    exit;
}

// Show login form if not authenticated
?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
