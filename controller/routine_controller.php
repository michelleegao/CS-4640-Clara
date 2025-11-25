<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../src/Database.php';

// Ensure user logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

/* ----------------------------------------
   MASTER PRODUCT LIST (RIGHT SIDE OPTIONS)
----------------------------------------- */
$products = [
    ["name" => "Cleanser",       "image" => "images/cleanser.png",        "type" => "cleanser"],
    ["name" => "Toner",          "image" => "images/toner.png",           "type" => "toner"],
    ["name" => "Serum",          "image" => "images/serum.png",           "type" => "serum"],
    ["name" => "Moisturizer",    "image" => "images/moisturizer.png",     "type" => "moisturizer"],
    ["name" => "Sunscreen",      "image" => "images/sunscreen.png",       "type" => "sunscreen"],
    ["name" => "Spot treatment", "image" => "images/spot_treatment.png",  "type" => "spot treatment"],
    ["name" => "Face mask",      "image" => "images/face_mask.png",       "type" => "face mask"]
];

// get user's current routine items
$stmt = Database::pdo()->prepare("
    SELECT product_type 
    FROM routine_products
    WHERE user_id = :uid 
        AND is_active = TRUE
        AND time_of_day = :tod
");
$time_of_day = $_GET["time_of_day"] ?? "Morning";

$stmt->execute([
    "uid" => $user_id,
    "tod" => $time_of_day
]);

$user_items = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

// create lookup for quick checking
$lookup = array_flip($user_items);

// mark which products are in the routine
foreach ($products as &$p) {
    $p["in_routine"] = isset($lookup[$p["type"]]);
}

echo json_encode($products);
