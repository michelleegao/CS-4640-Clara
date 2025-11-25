<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . '/../src/Database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$time = $_GET['time_of_day'] ?? 'Morning';

$sql = "
    SELECT name, product_type
    FROM routine_products
    WHERE user_id = :uid
      AND time_of_day = :time
      AND is_active = TRUE
";

$stmt = Database::pdo()->prepare($sql);
$stmt->execute([
    ":uid" => $user_id,
    ":time" => $time
]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
