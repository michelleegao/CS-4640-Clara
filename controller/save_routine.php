<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../src/Database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST["name"] ?? null;
$type = $_POST["type"] ?? null;
$time = $_POST["time_of_day"] ?? null;

if (!$name || !$type || !$time) {
    echo json_encode(["error" => "Missing fields"]);
    exit;
}

$sql = "
    INSERT INTO routine_products (user_id, name, time_of_day, product_type)
    VALUES (:uid, :name, :time, :type)
";

try {
    $stmt = Database::pdo()->prepare($sql);
    $stmt->execute([
        ":uid" => $user_id,
        ":name" => $name,
        ":time" => $time,
        ":type" => $type
    ]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}