<?php

require_once "../includes/database.php";

session_start();

$database = new Database();
$db = $database->connect();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "Method Not Allowed. Use POST."]);
    exit;
}

if (!empty($_POST)) {
    $post_data = $_POST;
} else {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $post_data = $decoded;
    } else {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['status' => 400, 'message' => 'Invalid JSON data']);
        exit;
    }
}

if (empty($post_data['username']) || empty($post_data['password'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['status' => 400, 'message' => 'Missing required fields']);
    exit;
}

$username = $post_data['username'];
$password = $post_data['password'];

// Query the database for the user
try {
    $query = "SELECT id, username, password FROM customers WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['status' => 401, 'message' => 'Invalid username or password']);
        exit;
    }

    // Verify password using PHP's built-in function
    if (!password_verify($password, $user['password'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['status' => 401, 'message' => 'Invalid username or password']);
        exit;
    }

    // Password is correct - set session
    $_SESSION['user_authenticated'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode(['status' => 200, 'message' => 'Login successful']);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['status' => 500, 'message' => 'Database error: ' . $e->getMessage()]);
}

?>