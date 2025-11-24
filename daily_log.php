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
    <title>Clara: Current Routine</title>
    <link rel="stylesheet" href="styles/style.css?v=3">
    <meta name="author" content="Henna Panjshiri, kew4bd">
    <style>
        .popup-message {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #7ca982;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            opacity: 0.95;
            animation: fadeOut 3s forwards;
        }
        @keyframes fadeOut {
            0% {opacity: 1;}
            80% {opacity: 1;}
            100% {opacity: 0;}
        }

        .submit-btn {
            background-color: #7ca982;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #6a9175;
        }
    </style>
</head>
  
<body>
    <header>
        <!-- Navigation Bar -->
        <nav class="navbar">
            <!-- Navigation Left -->
            <div class="nav-left">
                <a href="daily_log.php" class="home-icon">🏠</a>
                <ul>
                    <li><a href="daily_log.php" class="active">Log Today</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="current_routine.html">Current Routine</a></li>
                </ul>
            </div>

            <!-- Navigation Right -->
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

    <!-- Page Content -->
    <main class="page">
        <section class="grid">
            <!-- Left Column -->
            <div class="left-col">
                <!-- Weekly section -->
                <div class="week-card">
                    <div class="week-grid">
                        <div class="week-col">
                            <span class="weekday" data-index="0"></span>
                            <button class="date-dot" data-index="0"></button>
                        </div>
                        <div class="week-col">
                            <span class="weekday" data-index="1"></span>
                            <button class="date-dot" data-index="1"></button>
                        </div>
                        <div class="week-col">
                            <span class="weekday" data-index="2"></span>
                            <button class="date-dot" data-index="2"></button>
                        </div>
                        <div class="week-col">
                            <span class="weekday" data-index="3"></span>
                            <button class="date-dot" data-index="3"></button>
                        </div>
                        <div class="week-col">
                            <span class="weekday" data-index="4"></span>
                            <button class="date-dot" data-index="4"></button>
                        </div>
                        <div class="week-col">
                            <span class="weekday" data-index="5"></span>
                            <button class="date-dot" data-index="5"></button>
                        </div>
                        <div class="week-col">
                            <span class="weekday" data-index="6"></span>
                            <button class="date-dot" data-index="6"></button>
                        </div>
                    </div>
                </div>

                <!-- Prompt section -->
                <div class="prompt-card">
                    <h3>What Skincare did you do today?</h3>
                    <div class="prompt-actions">
                        <button class="btn btn-secondary">I did my Morning routine</button>
                        <button class="btn btn-secondary">I did my Night routine</button>
                    </div>
                    <button class="btn btn-primary wide">Log Breakouts today</button>
                </div>

                <!-- Breakout logging form -->
                <form id="logForm" class="log-form" aria-labelledby="log-title">
                    <h3 id="log-title">Breakout Log</h3>

                    <div class="field-row">
                        <label for="where">Location</label>
                        <div class="pill-group" id="where">
                            <label><input type="checkbox" name="locations[]" value="nose"> Nose</label>
                            <label><input type="checkbox" name="locations[]" value="chin"> Chin</label>
                            <label><input type="checkbox" name="locations[]" value="t-zone"> T-zone</label>
                            <label><input type="checkbox" name="locations[]" value="cheeks"> Cheeks</label>
                            <label><input type="checkbox" name="locations[]" value="back"> Back</label>
                        </div>
                    </div>

                    <div class="field">
                        <label for="severity">Severity</label>
                            <select id="severity" name="severity" required>
                                <option value="" disabled selected>Select severity</option>
                                <option value="Mild">Mild</option>
                                <option value="Moderate">Moderate</option>
                                <option value="Severe">Severe</option>
                            </select>
                    </div>

                    <div class="field-row">
                        <label for="type">Type</label>
                        <div class="pill-group" id="type">
                            <label><input type="checkbox" name="types[]" value="whiteheads"> Whiteheads</label>
                            <label><input type="checkbox" name="types[]" value="blackheads"> Blackheads</label>
                            <label><input type="checkbox" name="types[]" value="papules"> Papules</label>
                            <label><input type="checkbox" name="types[]" value="pustules"> Pustules</label>
                            <label><input type="checkbox" name="types[]" value="oily"> Cystic</label>
                        </div>
                    </div>

                    <div class="two-col">
                        <div class="field">
                            <label for="water">Water Intake (cups)</label>
                            <input id="water" name="water_cups" type="number" min="0" placeholder="e.g, 3">
                        </div>
                        <div class="field">
                            <label for="activity">Habits/Activity</label>
                            <select id="activity">
                                <option>Select</option>
                                <option name="activity[]" value="workout">Workout</option>
                                <option name="activity[]" value="high stress">High stress</option>
                                <option name="activity[]" value="good sleep">Good sleep</option>
                                <option name="activity[]" value="new product">New product</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="notes">Optional notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Diet, routine changes, cycle, travel, etc."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Log</button>
                        <button class="btn btn-ghost" type="reset">Clear</button>
                    </div>
                </form>
            </div>


            <!-- Right Column: Routine tracker -->
            <aside class="right-col">
                <div class="routine-card">
                    <!-- Theme toggle (Sun / Moon) -->
                    <div class="theme-toggle" style="margin-bottom: 15px;">
                        <img src="images/sun.png" alt="Sun Icon" id="sun-icon" class="theme-icon active" style="width:40px;">
                        <img src="images/moon.png" alt="Moon Icon" id="moon-icon" class="theme-icon" style="width:37px;">
                    </div>

                    <!-- Morning Routine Section -->
                    <div class="routine-section">
                        <div class="routine-header">
                            <div class="checks">
                                <label><input type="checkbox"> All</label>
                                <label><input type="checkbox"> None</label>
                            </div>
                        </div>

                        <ul class="product-grid">
                            <li>
                                <div class="product-ghost"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                            <li>
                                <div class="product-ghost tall"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                            <li>
                                <div class="product-ghost"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                            <li>
                                <div class="product-ghost short"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                        </ul>
                    </div>

                    <div class="divider"></div>

                    <!-- Night Routine Section -->
                    <div class="routine-section">
                        <div class="routine-header">
                            <div class="checks">
                                <label><input type="checkbox"> All</label>
                                <label><input type="checkbox"> None</label>
                            </div>
                        </div>

                        <ul class="product-grid">
                            <li>
                                <div class="product-ghost tall"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                            <li>
                                <div class="product-ghost"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                            <li>
                                <div class="product-ghost short"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                            <li>
                                <div class="product-ghost"></div>
                                <label class="under"><input type="checkbox"> used</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <script>
        document.getElementById('logForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch('controller/log_controller.php?action=create', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            const result = await response.json();

            if (result.success) {
                showPopup("Log successfully saved!");
                this.reset();
            } else {
                showPopup((result.error || "Failed to save log."));
            }
        } catch (err) {
            showPopup("Network or server error.");
        }
    });

    function showPopup(message) {
        const popup = document.createElement('div');
        popup.textContent = message;
        popup.className = 'popup-message';
        document.body.appendChild(popup);
        setTimeout(() => popup.remove(), 3000);
    }
    
    // dynamically update the dates of the logger
    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const weekdayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        // fill in weekdays and dates for each column
        document.querySelectorAll(".week-col").forEach((col, i) => {
            // offset so current date is at the center/index 3
            const offset = i - 3;
            const date = new Date(today);
            date.setDate(today.getDate() + offset);

            // weekday
            const weekdaySpan = col.querySelector(".weekday");
            weekdaySpan.textContent = weekdayNames[date.getDay()];

            // date
            const dateButton = col.querySelector(".date-dot");
            dateButton.textContent = date.getDate();
            dateButton.dataset.dateValue = date.toISOString().split("T")[0];

            // apply stylistic changes to today's date/weekday
            if (offset === 0) {
                weekdaySpan.classList.add("today");
                dateButton.classList.add("day-selected");
                dateButton.setAttribute("aria-current", "date");
            }
        });
    });

    // toggle sun and moon buttons
    document.addEventListener("DOMContentLoaded", () => {
        const sun  = document.getElementById("sun-icon");
        const moon = document.getElementById("moon-icon");

        if (!sun || !moon) {
            console.log("Sun or moon icons not found.");
            return;
        }

        // START STATE
        sun.classList.add("active");
        moon.classList.remove("active");

        sun.onclick = () => {
            sun.classList.add("active");
            moon.classList.remove("active");
        };

        moon.onclick = () => {
            moon.classList.add("active");
            sun.classList.remove("active");
        };
    });
    </script>
</body>
</html>