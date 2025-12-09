<?php
header('Content-Type: application/json');
session_start();

require_once "../../includes/database.php";

// Check authentication
if (!isset($_SESSION["user_authenticated"]) || $_SESSION["user_authenticated"] !== true) {
    echo json_encode(["success" => false, "message" => "You must be logged in."]);
    exit;
}

$database = new Database();
$db = $database->connect();

try {
    $stmt = $db->prepare("
        SELECT r.id as rental_id, r.units_rented, r.date_rented, r.date_return, r.rent_duration, r.status,
               b.id as bike_id, b.name, b.description, b.image_path, b.price_per_hour
        FROM rentals r
        JOIN bikes b ON r.bike_id = b.id
        WHERE r.customer_id = :customer_id
        ORDER BY r.date_rented DESC
    ");
    $stmt->bindValue(':customer_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "rentals" => $rentals,
        "total" => count($rentals)
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error fetching rentals: " . $e->getMessage()]);
}