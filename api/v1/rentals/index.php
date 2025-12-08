<?php
require_once "../auth.php";
require_once "../../includes/database.php";

$database = new Database();
$db = $database->connect();

$sort_orders = [
    "id" => "id",
    "bike_id" => "bike_id",
    "rent_time" => "rent_time",
    "due_date" => "due_date",
    "hours" => "hours_used",
    "quantity" => "units_rented"
];

$sort_directions = [
    "asc" => "ASC",
    "desc" => "DESC"
];

if (isset($_GET["id"])) {

    $rental_id = isset($_GET["id"]) ? intval($_GET["id"]) : die();

    $query = "SELECT * FROM rentals WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$rental_id]);
    $rental = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!isset($rental)) {
        http_response_code(404);
        header('Content-Type: application/json');

        $error_message = [
            'status' => 404,
            'message' => 'Resource Not Found'
        ];

        echo json_encode($error_message);

        die();
    }

    echo json_encode($rental);

} else {

    $sort_order_param = isset($_GET["sort"]) ? $_GET["sort"] : "id";
    $sort_dir_param = isset($_GET["sort_dir"]) ? $_GET["sort_dir"] : "asc";

    $sort_order = isset($sort_orders[$sort_order_param]) ? $sort_orders[$sort_order_param] : "id";
    $sort_dir = isset($sort_directions[$sort_dir_param]) ? $sort_directions[$sort_dir_param] : "ASC";

    $query = "SELECT * FROM rentals ORDER BY $sort_order $sort_dir";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rentals);
}

?>