<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

header("Content-Type: application/json");

// --------- VALIDATE USER SESSION ---------
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No user logged in"]);
    exit;
}

$user_id     = $_SESSION['user_id'];
$name        = $_POST['name'] ?? null;
$type        = $_POST['type'] ?? null;
$time_of_day = $_POST['time_of_day'] ?? null;

if (!$name || !$type || !$time_of_day) {
    echo json_encode(["error" => "Missing fields"]);
    exit;
}

try {
    $pdo = Database::pdo();

    $sql = "INSERT INTO routine_products (user_id, name, product_type, time_of_day)
            VALUES (:user_id, :name, :type, :time_of_day)
            ON CONFLICT DO NOTHING;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":user_id"    => $user_id,
        ":name"       => $name,
        ":type"       => $type,
        ":time_of_day"=> $time_of_day
    ]);

    echo json_encode(["status" => "ok"]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
