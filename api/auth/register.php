<?php

require_once "../includes/database.php";


$database = new Database();
$db = $database->connect();

function username_exists($username)
{
    global $db;

    $query = "SELECT COUNT(*) FROM customers WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);

    $count = $stmt->fetchColumn();

    return $count > 0;
}


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
    }
}

$firstname = $post_data['firstname'];
$lastname = $post_data['lastname'];
$username = $post_data['username'];
$password = $post_data['password'];


if (username_exists($username)) {
    header('Content-Type: application/json');
    http_response_code(409);

    echo json_encode([
        'status' => '409',
        'message' => 'Username already exists'
    ]);
    exit;
}

// Hash password using PHP's built-in function
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Insert user into database
try {
    $insert_query = "INSERT INTO customers (first_name, last_name, username, password) VALUES (?, ?, ?, ?)";
    $insert_stmt = $db->prepare($insert_query);
    $insert_stmt->execute([$firstname, $lastname, $username, $password_hash]);

    header('Content-Type: application/json');
    http_response_code(201);
    echo json_encode(['status' => 201, 'message' => 'User registered successfully']);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['status' => 500, 'message' => 'Database error: ' . $e->getMessage()]);
}



?>