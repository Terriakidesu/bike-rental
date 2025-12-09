<?php
require_once "../../includes/database.php";

$database = new Database();
$db = $database->connect();

$search_query = isset($_GET["q"]) ? $_GET["q"] : '';
$search_limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 6;
$search_page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$search_page = max(1, $search_page);
$offset = ($search_page - 1) * $search_limit;

// SORT OPTIONS
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

$sort_col = isset($_GET["sort"]) && isset($sort_orders[$_GET["sort"]]) ? $sort_orders[$_GET["sort"]] : "id";
$sort_dir = isset($_GET["dir"]) && isset($sort_directions[strtolower($_GET["dir"])]) ? $sort_directions[strtolower($_GET["dir"])] : "ASC";

// COUNT TOTAL ROWS MATCHING SEARCH
$count_sql = "SELECT COUNT(*) AS total FROM bikes WHERE name LIKE :name";
$count_stmt = $db->prepare($count_sql);
$count_stmt->bindValue(':name', "%$search_query%", PDO::PARAM_STR);
$count_stmt->execute();
$total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)["total"];
$total_pages = ceil($total_rows / $search_limit);

// FETCH CURRENT PAGE
$query = "SELECT * FROM bikes WHERE name LIKE :name ORDER BY $sort_col $sort_dir LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindValue(':name', "%$search_query%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $search_limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// RETURN JSON WITH TOTAL PAGES
echo json_encode([
    "bikes" => $result,
    "totalPages" => $total_pages
]);
?>