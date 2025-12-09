<?php
header('Content-Type: application/json');
session_start();
require_once "../../includes/database.php";

// Only logged-in users can pay
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$rental_id = isset($data['rental_id']) ? intval($data['rental_id']) : 0;
$payment = isset($data['payment']) ? floatval($data['payment']) : 0;

if (!$rental_id || $payment <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid rental ID or payment amount']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();

    // Check rental exists and belongs to current user
    $stmt = $db->prepare("SELECT * FROM rentals WHERE id = :id AND customer_id = :user_id");
    $stmt->bindValue(':id', $rental_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $rental = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rental) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Rental not found']);
        exit;
    }

    // Calculate total due
    $start = new DateTime($rental['date_rented']);
    $end = new DateTime($rental['date_return']);
    $hours = ceil(($end->getTimestamp() - $start->getTimestamp()) / 3600);
    $totalDue = $hours * $rental['units_rented'] * $rental['price_per_hour'] ?? 0;

    if ($payment < $totalDue) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Insufficient payment']);
        exit;
    }

    // Insert payment record
    $stmt = $db->prepare("INSERT INTO payments (rental_id, payment) VALUES (:rental_id, :payment)");
    $stmt->bindValue(':rental_id', $rental_id, PDO::PARAM_INT);
    $stmt->bindValue(':payment', $payment);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Payment successful']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
