<?php
require_once "../../includes/database.php";

$database = new Database();
$db = $database->connect();

$sort_orders = [
    "id" => "id",
    "price" => "price_per_hour",
    "name" => "name",
    "units" => "available_units"
];

$sort_directions = [
    "asc" => "ASC",
    "desc" => "DESC"
];

if (isset($_GET["id"])) {

    $bike_id = isset($_GET["id"]) ? intval($_GET["id"]) : die();

    $query = "SELECT * FROM bikes WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$bike_id]);
    $bike = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!isset($bike)) {
        http_response_code(404);
        header('Content-Type: application/json');

        $error_message = [
            'status' => 404,
            'message' => 'Resource Not Found'
        ];

        echo json_encode($error_message);

        die();
    }
    
    echo json_encode($bike);

} else {

    $sort_order_param = isset($_GET["sort"]) ? $_GET["sort"] : "id";
    $sort_dir_param = isset($_GET["sort_dir"]) ? $_GET["sort_dir"] : "asc";

    $sort_order = isset($sort_orders[$sort_order_param]) ? $sort_orders[$sort_order_param] : "id";
    $sort_dir = isset($sort_directions[$sort_dir_param]) ? $sort_directions[$sort_dir_param] : "ASC";

    $query = "SELECT * FROM bikes ORDER BY $sort_order $sort_dir";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $bikes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($bikes);
}

?>