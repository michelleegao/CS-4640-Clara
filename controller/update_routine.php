<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../src/Database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_type = $_POST["product_type"] ?? null;
$time = $_POST["time_of_day"] ?? null;
$active = ($_POST["active"] === "true" || $_POST["active"] === true) ? 1 : 0;

if (!$product_type || !$time) {
    echo json_encode(["error" => "missing fields"]);
    exit;
}

$sql = "
    UPDATE routine_products
    SET is_active = :active
    WHERE user_id = :uid
      AND product_type = :ptype
      AND time_of_day = :tod
";

$stmt = Database::pdo()->prepare($sql);
$stmt->execute([
    "uid" => $user_id,
    "ptype" => $product_type,
    "tod" => $time,
    "active" => $active
]);

echo json_encode(["success" => true]);