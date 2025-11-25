<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

header("Content-Type: application/json");

// Valid session?
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$time_of_day = $_GET['time_of_day'] ?? "Morning";

try {
    $pdo = Database::pdo();

    $sql = "SELECT name, product_type
            FROM routine_products
            WHERE user_id = :user_id
              AND time_of_day = :time_of_day
              AND is_active = TRUE
            ORDER BY created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":user_id" => $user_id,
        ":time_of_day" => $time_of_day
    ]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);

} catch (Exception $e) {
    echo json_encode([]);
}
