<?php
require_once '../../includes/database.php';

$database = new Database();
$db = $database->connect();

# form post data - support application/x-www-form-urlencoded and raw JSON
$post_data = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$bike_id = $post_data['bike_id'];
$quantity = $post_data['quantity'];

$customer_id = $post_data['customer_id'];

$dt_now = new DateTime();
$dt_now_str = $dt_now->format('Y-m-d H:i:s');
$dt_due = date('Y-m-d H:i:s', strtotime($dt_now_str . ' + 5 days'));
$dt_due = new DateTime($dt_due);

$dt_now_posix = $dt_now->getTimestamp();
$dt_due_posix = $dt_due->getTimestamp();

echo "{'now' : $dt_now_posix, 'due' : $dt_due_posix}";
?>