<?php
header('Content-Type: application/json');

$products = [
    ["name" => "Cleanser", "image" => "images/cleanser.png", "type" => "cleanser"],
    ["name" => "Toner", "image" => "images/toner.png", "type" => "toner"],
    ["name" => "Serum", "image" => "images/serum.png", "type" => "serum"],
    ["name" => "Moisturizer", "image" => "images/moisturizer.png", "type" => "moisturizer"],
    ["name" => "Sunscreen", "image" => "images/sunscreen.png", "type" => "sunscreen"],
    ["name" => "Spot treatment", "image" => "images/spot_treatment.png", "type" => "spot treatment"],
    ["name" => "Face mask", "image" => "images/face_mask.png", "type" => "face mask"]
];

echo json_encode($products);
