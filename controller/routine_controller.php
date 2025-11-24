<?php
header("Content-Type: application/json");

$products = [
    ["name" => "Cleanser", "image" => "images/cleanser.png"],
    ["name" => "Toner", "image" => "images/toner.png"],
    ["name" => "Serum", "image" => "images/serum.png"],
    ["name" => "Moisturizer", "image" => "images/moisturizer.png"],
    ["name" => "Sunscreen", "image" => "images/sunscreen.png"],
    ["name" => "Spot Treatment", "image" => "images/spot-treatment.png"],
    ["name" => "Face Mask", "image" => "images/face-mask.png"]
];

echo json_encode($products);
?>