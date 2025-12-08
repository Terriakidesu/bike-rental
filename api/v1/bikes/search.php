<?php

require_once "../../includes/database.php";

$database = new Database();
$db = $database->connect();


if (isset($_GET["q"])) {

    $search_query = isset($_GET["q"]) ? $_GET["q"] : die();
    $search_limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 5;

    $search_page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
    $search_page = ($search_page >= 1) ? $search_page : 1;

    $offset = ($search_page - 1) * $search_limit;

    $query = "SELECT * FROM bikes WHERE name LIKE :name LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':name', "%$search_query%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int) $search_limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
}
?>