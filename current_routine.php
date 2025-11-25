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
                        <hr>
                        <li class="product-row">
                            <span>Toner</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Toner","image":"images/toner.png", "type":"toner"}'>
                        </li>
                        <hr>
                        <li class="product-row">
                            <span>Serum</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Serum","image":"images/serum.png", "type":"serum"}'>
                        </li>
                        <hr>
                        <li class="product-row">
                            <span>Moisturizer</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Moisturizer","image":"images/moisturizer.png", "type":"moisturizer"}'>
                        </li>
                        <hr>
                        <li class="product-row">
                            <span>Sunscreen</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Sunscreen","image":"images/sunscreen.png", "type":"sunscreen"}'>
                        </li>
                        <hr>
                        <li class="product-row">
                            <span>Spot treatment</span>
                            <img src="images/add.png" alt="Add Icon" class="add-icon" data-product='{"name":"Spot Treatment","image":"images/spot_treatment.png", "type":"spot treatment"}'>
                        </li>
                        <hr>
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

    /* -------------------------------------------
    Load morning/night routine items (LEFT SIDE)
    -------------------------------------------- */
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

                // Clear left-side grid
                $(".products-list").empty();

                items.forEach(item => {
                    $(".products-list").append(`
                        <div class="product-item">
                            <img class="product-rect" src="images/${item.product_type.replace(' ', '_')}.png">
                            <label class="product-label">${item.name}</label>
                        </div>
                    `);
                });
            }
        });
    }

    /* -------------------------------------------
    Toggle Sun/Moon + Reload Routine
    -------------------------------------------- */
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

    /* -------------------------------------------
    Search filter (unchanged)
    -------------------------------------------- */
    function searchFunction() {
        let input = document.getElementById('myInput');
        let filter = input.value.toUpperCase();
        let ul = document.getElementById('productsUL');
        let li = ul.getElementsByTagName('li');

        for (i = 0; i < li.length; i++) {
            txtSpan = li[i].getElementsByTagName("span")[0];
            txtValue = txtSpan.textContent || txtSpan.innerText;
            
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }

    /* -------------------------------------------
    Load RIGHT-SIDE PRODUCT LIST
    (Hide items already saved in routine)
    -------------------------------------------- */
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

    /* -------------------------------------------
    Document Ready
    -------------------------------------------- */
    $(document).ready(function () {

        loadRoutineItems();   // left side
        loadProductList();    // right side

        /* ---------------- add products to routine ---------------- */
        $("#productsUL").on("click", ".add-icon", function () {
            let product = JSON.parse($(this).attr("data-product"));

            $.ajax({
                url: "controller/save_routine.php",
                method: "POST",
                dataType: "json",
                data: {
                    name: product.name,
                    type: product.type,
                    time_of_day: routineTime
                },
                success: function(res) {
                    console.log("Save response:", res);

                    if (res.error === "duplicate") {
                        alert("This product is already in your routine.");
                        return;
                    }

                    // reload both sides from DB so UI matches current time (Morning/Night)
                    loadRoutineItems();
                    loadProductList();
                }
            });
        });

    });
    </script>
</body>
</html>