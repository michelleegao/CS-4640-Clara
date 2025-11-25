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
                    <li><a href="current_routine.php">Current Routine</a></li>
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

                <!-- Breakout logging form -->
                <form id="logForm" class="log-form" aria-labelledby="log-title">
                    <input type="hidden" name="log_date" id="log_date">
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
                <!-- Prompt section -->
                <div class="prompt-card">
                    <h3>What Skincare did you do today?</h3>
                    <div class="prompt-actions">
                        <button class="btn btn-secondary">I did my Morning routine</button>
                        <button class="btn btn-secondary">I did my Night routine</button>
                    </div>
                    <button class="btn btn-primary wide">Log Breakouts today</button>
                </div>
            </aside>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        /* submit handler for breakout log */
        const form = document.getElementById('logForm');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // client-side validation
            let hasError = false;

            const severity = document.getElementById('severity').value;
            if (!severity) {
                showPopup("Select a severity level.");
                hasError = true;
            }

            const anyLocationChecked = !!document.querySelector('input[name="locations[]"]:checked');
            const anyTypeChecked     = !!document.querySelector('input[name="types[]"]:checked');

            if (!anyLocationChecked) {
                showPopup("Select at least one location for your breakout.");
                hasError = true;
            }

            if (!anyTypeChecked) {
                showPopup("Select at least one type for your breakout.");
                hasError = true;
            }

            if (hasError) return;

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
                    showPopup(result.error || "Failed to save log.");
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

        /* week slider */
        let selectedDate = null;
        let realToday = new Date();

        const weekdayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        const hiddenDateInput = document.getElementById("log_date");
        const weekGrid = document.querySelector(".week-grid");
        const weekCols = document.querySelectorAll(".week-col");

        function sameDay(d1, d2) {
            return d1.getFullYear() === d2.getFullYear() &&
                d1.getMonth() === d2.getMonth() &&
                d1.getDate() === d2.getDate();
        }

        function renderWeek(baseDate) {
            weekCols.forEach((col, i) => {
                const offset = i - 3;
                const date = new Date(baseDate);
                date.setDate(baseDate.getDate() + offset);

                const iso = date.toISOString().split("T")[0];

                const weekdaySpan = col.querySelector(".weekday");
                const dateButton  = col.querySelector(".date-dot");

                // weekday label
                weekdaySpan.textContent = weekdayNames[date.getDay()];

                // underline current real date
                if (sameDay(date, realToday)) {
                    weekdaySpan.classList.add("today");
                } else {
                    weekdaySpan.classList.remove("today");
                }

                // date circle
                dateButton.textContent = date.getDate();
                dateButton.dataset.dateValue = iso;

                // selected (middle)
                if (offset === 0) {
                    dateButton.classList.add("day-selected");
                    dateButton.setAttribute("aria-current", "date");
                } else {
                    dateButton.classList.remove("day-selected");
                    dateButton.removeAttribute("aria-current");
                }
            });

            hiddenDateInput.value = baseDate.toISOString().split("T")[0];
        }

        selectedDate = new Date();
        renderWeek(selectedDate);

        if (weekGrid) {
            weekGrid.addEventListener("click", (evt) => {
                const btn = evt.target.closest(".date-dot");
                if (!btn) return;

                const newDateStr = btn.dataset.dateValue;
                const newDate = new Date(newDateStr);

                if (sameDay(newDate, selectedDate)) return;

                // animation direction
                weekGrid.classList.remove("slide-left", "slide-right");
                void weekGrid.offsetWidth;

                if (newDate > selectedDate) {
                    weekGrid.classList.add("slide-left");
                } else {
                    weekGrid.classList.add("slide-right");
                }

                // update center date
                selectedDate = newDate;
                renderWeek(selectedDate);
            });

            weekGrid.addEventListener("animationend", () => {
                weekGrid.classList.remove("slide-left", "slide-right");
            });
        }

        /* sun/moon toggle */
        const sun  = document.getElementById("sun-icon");
        const moon = document.getElementById("moon-icon");

        if (sun && moon) {
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
        }
    });
    </script>
</body>
</html>