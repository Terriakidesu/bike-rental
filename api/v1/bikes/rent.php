<?php
header('Content-Type: application/json');
session_start();
require_once "../../includes/database.php";

if (!isset($_SESSION["user_authenticated"]) || $_SESSION["user_authenticated"] !== true) {
    echo json_encode(["success" => false, "message" => "You must be logged in."]);
    exit;
}

// Database connection
$database = new Database();
$db = $database->connect();

// Collect POST data
$bike_id = isset($_POST['bike_id']) ? intval($_POST['bike_id']) : 0;
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
$units_rented = isset($_POST['units_rented']) ? intval($_POST['units_rented']) : 0;
$date_rented = isset($_POST['date_rented']) ? $_POST['date_rented'] : '';
$date_return = isset($_POST['date_return']) ? $_POST['date_return'] : '';
$rent_duration = isset($_POST['rent_duration']) ? intval($_POST['rent_duration']) : 0;

// Validate required fields
if ($bike_id <= 0 || $customer_id <= 0 || $units_rented <= 0 || !$date_rented || !$date_return || $rent_duration <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

// Check bike availability
$stmt = $db->prepare("SELECT available_units, name FROM bikes WHERE id=:id");
$stmt->bindValue(':id', $bike_id, PDO::PARAM_INT);
$stmt->execute();
$bike = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bike) {
    echo json_encode(["success" => false, "message" => "Bike not found."]);
    exit;
}

if ($units_rented > $bike['available_units']) {
    echo json_encode(["success" => false, "message" => "Not enough units available."]);
    exit;
}



$now = new DateTime();
$dateRented = new DateTime($startDate);

$status = ($now < $dateRented) ? 'pending' : 'active';

$stmt = $db->prepare("
    INSERT INTO rentals 
    (bike_id, customer_id, units_rented, date_rented, date_return, rent_duration, overdue, status)
    VALUES (:bike_id, :customer_id, :units_rented, :date_rented, :date_return, :rent_duration, 0, :status)
");

$stmt->bindValue(':status', $status);
$stmt->bindValue(':bike_id', $bike_id, PDO::PARAM_INT);
$stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->bindValue(':units_rented', $units_rented, PDO::PARAM_INT);
$stmt->bindValue(':date_rented', $date_rented, PDO::PARAM_STR);
$stmt->bindValue(':date_return', $date_return, PDO::PARAM_STR);
$stmt->bindValue(':rent_duration', $rent_duration, PDO::PARAM_INT);
$stmt->bindValue(':status', $status, PDO::PARAM_STR);

try {
    $stmt->execute();

    // Update bike available_units
    $stmt2 = $db->prepare("UPDATE bikes SET available_units = available_units - :units WHERE id=:id");
    $stmt2->bindValue(':units', $units_rented, PDO::PARAM_INT);
    $stmt2->bindValue(':id', $bike_id, PDO::PARAM_INT);
    $stmt2->execute();

    echo json_encode(["success" => true, "message" => "Successfully rented {$bike['name']}!"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Failed to rent bike: " . $e->getMessage()]);
}
