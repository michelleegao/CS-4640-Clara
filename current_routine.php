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
    // GLOBAL variable for morning/night mode
    let routineTime = "Morning";

    // GLOBAL FUNCTION: Load morning/night routine items into left panel
    function loadRoutineItems() {

        $.ajax({
            url: "controller/get_routine.php",
            method: "GET",
            data: { time_of_day: routineTime },
            success: function(data) {
                let items = [];

                try {
                    items = JSON.parse(data);
                } catch (e) {
                    console.log("JSON parse error:", data);
                    return;
                }

                // Clear current grid
                $(".products-list").empty();

                // Insert items
                items.forEach(item => {
                    $(".products-list").append(`
                        <div class="product-item">
                            <img class="product-rect" src="images/${item.product_type}.png">
                            <label class="product-label">${item.name}</label>
                        </div>
                    `);
                });
            }
        });
    }
    
    // toggle sun and moon buttons
    document.addEventListener("DOMContentLoaded", () => {
        // const sun  = document.getElementById("sun-icon");
        // const moon = document.getElementById("moon-icon");

        // if (!sun || !moon) {
        //     console.log("Sun or moon icons not found.");
        //     return;
        // }

        // // START STATE
        // sun.classList.add("active");
        // moon.classList.remove("active");

        // sun.onclick = () => {
        //     sun.classList.add("active");
        //     moon.classList.remove("active");
        // };

        // moon.onclick = () => {
        //     moon.classList.add("active");
        //     sun.classList.remove("active");
        // };
        const sun  = document.getElementById("sun-icon");
        const moon = document.getElementById("moon-icon");

        function activateMorning() {
            routineTime = "Morning";
            sun.classList.add("active");
            moon.classList.remove("active");
            loadRoutineItems();
        }

        function activateNight() {
            routineTime = "Night";
            moon.classList.add("active");
            sun.classList.remove("active");
            loadRoutineItems();
        }

        // initial mode
        activateMorning();

        sun.onclick = () => activateMorning();
        moon.onclick = () => activateNight();
    });

    function searchFunction() {
        let input = document.getElementById('myInput');
        let filter = input.value.toUpperCase();
        let ul = document.getElementById('productsUL');
        let li = ul.getElementsByTagName('li');

        // loop through list items, and hide those who don't match search query
        for (i = 0; i < li.length; i++) {
            txtSpan = li[i].getElementsByTagName("span")[0];
            txtValue = txtSpan.textContent || txtSpan.innerText;
            
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
                li[i].classList.add("highlight");
            } else {
                li[i].style.display = "none";
                li[i].classList.remove("highlight");
            }
        }
    }

    // $(document).ready(function () {
    //     let routineManager = {
    //         addedProducts: [],

    //         addProduct(product) {
    //             this.addedProducts.push(product);
    //         }
    //     };

    //     // ajax loading
    //     $.ajax({
    //         url: "controller/get_products.php",
    //         method: "GET",
    //         dataType: "json",
    //         success: function(products) {

    //             let ul = $("#productsUL");
    //             ul.empty();

    //             products.forEach(product => {
    //                 ul.append(`
    //                     <li class="product-row">
    //                         <span>${product.name}</span>
    //                         <img src="images/add.png" 
    //                             class="add-icon"
    //                             data-product='${JSON.stringify(product)}'>
    //                     </li>
    //                     <hr>
    //                 `);
    //             });
    //         }
    //     });

    //     // adding products to the right panel
    //     $("#productsUL").on("click", ".add-icon", function () {

    //         let product = JSON.parse($(this).attr("data-product"));
    //         routineManager.addProduct(product);

    //         // remove item from the right panel
    //         $(this).closest("li").next("hr").remove(); 
    //         $(this).closest("li").remove();

    //         // add product to left panel
    //         $(".products-list").append(`
    //             <div class="product-item">
    //                 <img class="product-rect" 
    //                     src="${product.image}" 
    //                     alt="${product.name}">
    //                 <label class="product-label">${product.name}</label>
    //             </div>
    //         `);
    //     });

    //     // search bar filtering logic
    //     window.searchFunction = function() {
    //         let filter = $("#myInput").val().toUpperCase();

    //         $("#productsUL li.product-row").each(function () {
    //             let txtValue = $(this).find("span").text().toUpperCase();

    //             if (txtValue.indexOf(filter) > -1) {
    //                 $(this).show().addClass("highlight");
    //             } else {
    //                 $(this).hide().removeClass("highlight");
    //             }
    //         });
    //     };
    // });
    $(document).ready(function () {

        let routineTime = "Morning"; // synced with DOMContentLoaded
        const routineManager = { addedProducts: [] };

        /* ---------------- LOAD PRODUCTS LIST (right side) ---------------- */
        $.ajax({
            url: "controller/routine_controller.php",
            method: "GET",
            dataType: "json",
            success: function(products) {
                let ul = $("#productsUL");
                ul.empty();

                products.forEach(product => {
                    ul.append(`
                        <li class="product-row">
                            <span>${product.name}</span>
                            <img src="images/add.png" 
                                class="add-icon"
                                data-product='${JSON.stringify(product)}'>
                        </li>
                        <hr>
                    `);
                });
            }
        });

        /* ---------------- ADD PRODUCT TO ROUTINE ---------------- */
        $("#productsUL").on("click", ".add-icon", function () {
            let product = JSON.parse($(this).attr("data-product"));
            routineManager.addProducts?.push(product);

            // remove from right list
            $(this).closest("li").next("hr").remove();
            $(this).closest("li").remove();

            // 1. SAVE TO DATABASE
            $.ajax({
                url: "controller/save_routine.php",
                method: "POST",
                data: {
                    name: product.name,
                    type: product.type,     // MUST exist in product JSON
                    time_of_day: routineTime
                },
                success: function(res) {
                    console.log("Saved product:", res);
                }
            });

            // 2. ADD TO LEFT PANEL VISUALLY
            $(".products-list").append(`
                <div class="product-item">
                    <img class="product-rect" src="${product.image}" alt="${product.name}">
                    <label class="product-label">${product.name}</label>
                </div>
            `);
        });

        window.loadRoutineItems = loadRoutineItems;

    });
    </script>
</body>
</html>