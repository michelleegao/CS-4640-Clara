<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="authors" content="Michelle Gao and Henna Panjshiri">
    <title>Clara: Current Routine</title>
    <link rel="stylesheet" href="styles/style.css?v=6">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <a href="daily_log.php" class="home-icon">🏠</a>
                <ul>
                    <li><a href="daily_log.php">Log Today</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="current_routine.html" class="active">Current Routine</a></li>
                </ul>
            </div>

            <div class="nav-right">
                <span>
                    <?php 
                        echo isset($_SESSION['display_name']) 
                            ? "Hello, " . htmlspecialchars($_SESSION['display_name']) . "!" 
                            : "Hello!";
                    ?>
                 </span>
                <a href="index.php" class="profile-icon">👤</a>
            </div>
        </nav>
    </header>

    <!--overall products container-->
    <div class="products-container">

        <div class="left-products-container">
            
            <!--daytime nighttime icons-->
            <img class="product-icons theme-icon active" src="images/sun.png" id="sun-icon" alt="Sun Icon" style="width:40px">
            <img class="product-icons theme-icon" src="images/moon.png" id="moon-icon" alt="Moon Icon" style="width:37px">
            <hr style="position: relative; top: 20px; height: 3px; color: #243e36; background-color: #243e36">
            
            <!--sub container for left side-->
            <div class="left-products-inner">
                
                <!--edit text and icons-->
                <div class="edit-btn product-icons">
                    <img class="edit-icon" src="images/pencil.png" alt="Edit">
                    <span>Edit</span>
                </div>

                <!--skincare products display-->
                <div class="products-list">
                </div>
            </div>
        </div>

        <div class="right-products-container">
            <!--sub container for right side side-->
            <div class="right-products-inner">
                <div class="product-search-bar">
                    <input type="text" id="myInput" onkeyup="searchFunction()" placeholder="🔍Find a product...">
                    <ul id="productsUL">
                        <li class="product-row">
                            <span>Cleanser</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Cleanser","image":"images/cleanser.png", "type":"cleanser"}'>
                        </li>
                        <li class="divider-li"></li>

                        <li class="product-row">
                            <span>Toner</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Toner","image":"images/toner.png", "type":"toner"}'>
                        </li>
                        <li class="divider-li"></li>

                        <li class="product-row">
                            <span>Serum</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Serum","image":"images/serum.png", "type":"serum"}'>
                        </li>
                        <li class="divider-li"></li>

                        <li class="product-row">
                            <span>Moisturizer</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Moisturizer","image":"images/moisturizer.png", "type":"moisturizer"}'>
                        </li>
                        <li class="divider-li"></li>

                        <li class="product-row">
                            <span>Sunscreen</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Sunscreen","image":"images/sunscreen.png", "type":"sunscreen"}'>
                        </li>
                        <li class="divider-li"></li>

                        <li class="product-row">
                            <span>Spot treatment</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Spot Treatment","image":"images/spot_treatment.png", "type":"spot treatment"}'>
                        </li>
                        <li class="divider-li"></li>
                        
                        <li class="product-row">
                            <span>Face mask</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Face mask","image":"images/face_mask.png", "type":"face mask"}'>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    // global variable for morning/night mode
    let routineTime = "Morning";

    // load left-side routine items (morning/night)
    function loadRoutineItems() {
        $.ajax({
            url: "controller/get_routine.php",
            method: "GET",
            data: { time_of_day: routineTime },
            success: function(data) {
                let items = [];

                try {
                    items = typeof data === "string" ? JSON.parse(data) : data;
                } catch (e) {
                    console.log("JSON parse error:", data);
                    return;
                }

                // clear left-side grid
                $(".products-list").empty();

                items.forEach(item => {
                    $(".products-list").append(`
                        <div class="product-item" data-product-type="${item.product_type}">
                            <img class="product-rect" src="images/${item.product_type.replace(' ', '_')}.png">
                            <label class="product-label">${item.name}</label>

                            <!-- hidden until edit mode -->
                            <button class="remove-btn" data-type="${item.product_type}">
                                -
                            </button>
                        </div>
                    `);
                });
            }
        });
    }

    // load right side product list (hide products if already in database)
    function loadProductList() {
        $.ajax({
            url: "controller/routine_controller.php",
            method: "GET",
            data: { time_of_day: routineTime },
            dataType: "json",
            success: function(products) {
                let ul = $("#productsUL");
                ul.empty();

                products.forEach(product => {
                    if (!product.in_routine) {
                        ul.append(`
                            <li class="product-row">
                                <span>${product.name}</span>
                                <img src="images/add.png" 
                                    class="add-icon"
                                    data-product='${JSON.stringify(product)}'>
                            </li>
                            <hr>
                        `);
                    }
                });
            }
        });
    }

    // toggle sun and moon
    document.addEventListener("DOMContentLoaded", () => {
        const sun  = document.getElementById("sun-icon");
        const moon = document.getElementById("moon-icon");

        function activateMorning() {
            routineTime = "Morning";
            sun.classList.add("active");
            moon.classList.remove("active");
            loadRoutineItems();
            loadProductList();
        }

        function activateNight() {
            routineTime = "Night";
            moon.classList.add("active");
            sun.classList.remove("active");
            loadRoutineItems();
            loadProductList();
        }

        activateMorning(); // initial load
        sun.onclick = () => activateMorning();
        moon.onclick = () => activateNight();
    });

    // search filter
    function searchFunction() {
        let input = document.getElementById('myInput');
        let filter = input.value.toUpperCase();
        let ul = document.getElementById('productsUL');
        let li = ul.getElementsByTagName('li');

        for (let i = 0; i < li.length; i++) {
            let txtSpan = li[i].getElementsByTagName("span")[0];
            let txtValue = txtSpan.textContent || txtSpan.innerText;
            
            li[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1)
                ? ""
                : "none";
        }
    }

    // document ready
    $(document).ready(function () {

        // toggle edit mode
        let editMode = false;

        $(".edit-btn").on("click", function () {
            editMode = !editMode;

            if (editMode) {
                $(".products-list").addClass("edit-mode");
                $(".edit-btn span").text("Done");
            } else {
                $(".products-list").removeClass("edit-mode");
                $(".edit-btn span").text("Edit");
            }
        });

        // initial loads
        loadRoutineItems();
        loadProductList();

        // add products to routine
        $("#productsUL").on("click", ".add-icon", function () {
            let product = JSON.parse($(this).attr("data-product"));

            $.ajax({
                url: "controller/update_routine.php", // reactivate instead of insert
                method: "POST",
                dataType: "json",
                data: {
                    product_type: product.type,
                    time_of_day: routineTime,
                    active: true
                },
                success: function(res) {
                    // Reload both sides
                    loadRoutineItems();
                    loadProductList();
                }
            });
        });

        // remove product from routine
        $(".products-list").on("click", ".remove-btn", function () {
            const productType = $(this).data("type");

            $.ajax({
                url: "controller/update_routine.php",
                method: "POST",
                dataType: "json",
                data: {
                    product_type: productType,
                    time_of_day: routineTime,
                    active: false
                },
                success: function(res) {
                    loadRoutineItems();
                    loadProductList();
                }
            });
        });

    });
    </script>
</body>
</html>