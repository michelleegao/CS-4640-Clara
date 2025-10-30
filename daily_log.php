<?php
session_start();
require_once 'Database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clara: Current Routine</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta name="author" content="Henna Panjshiri, kew4bd">
</head>
  
<body>
    <header>
        <!-- Navigation Bar -->
        <nav class="navbar">
            <!-- Navigation Left -->
            <div class="nav-left">
                <a href="daily_log.html" class="home-icon">🏠</a>
                <ul>
                    <li><a href="daily_log.html" class="active">Log Today</a></li>
                    <li><a href="dashboard.html">Dashboard</a></li>
                    <li><a href="current_routine.php">Current Routine</a></li>
                </ul>
            </div>

            <!-- Navigation Right -->
            <div class="nav-right">
                <!-- <span>Hello, [Name!]</span> -->
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
                    <div class="week-header">
                        <span>Tue</span><span>Wed</span><span class="today">Thu</span><span>Fri</span><span>Sat</span>
                    </div>
                    <div class="week-dots">
                        <button aria-label="12">12</button>
                        <button aria-label="13">13</button>
                        <button class="day-selected" aria-current="date" aria-label="14">14</button>
                        <button aria-label="15">15</button>
                        <button aria-label="16">16</button>
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

                <!-- Breakout logging form (simple structure) -->
                <form action="log_controller.php?action=create" method="POST" class="log-form">
                    <form class="log-form" aria-labelledby="log-title">
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

                        <div class="field-row">
                            <label for="severity">Severity</label>
                            <div class="pill-group" id="severity">
                                <label><input type="radio" name="sev"> Mild</label>
                                <label><input type="radio" name="sev"> Moderate</label>
                                <label><input type="radio" name="sev"> Severe</label>
                            </div>
                        </div>

                        <div class="field-row">
                            <label for="type">Type</label>
                            <div class="pill-group" id="type">
                                <label><input type="checkbox"> Whiteheads</label>
                                <label><input type="checkbox"> Blackheads</label>
                                <label><input type="checkbox"> Papules</label>
                                <label><input type="checkbox"> Pustules</label>
                                <label><input type="checkbox"> Cystic</label>
                            </div>
                        </div>

                        <div class="two-col">
                            <div class="field">
                                <label for="water">Water Intake (cups)</label>
                                <input id="water" type="number" min="0" placeholder="e.g, 3">
                            </div>
                            <div class="field">
                                <label for="activity">Habits/Activity</label>
                                <select id="activity">
                                    <option>Select</option>
                                    <option>Workout</option>
                                    <option>High stress</option>
                                    <option>Good sleep</option>
                                    <option>New product</option>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label for="notes">Optional notes</label>
                            <textarea id="notes" rows="3" placeholder="Diet, routine changes, cycle, travel, etc."></textarea>
                        </div>

                        <div class="form-actions">
                            <button class="btn btn-primary">Save Log</button>
                            <button class="btn btn-ghost" type="reset">Clear</button>
                        </div>
                    </form>
                </form>
            </div>

            <!-- Right Column: Routine tracker -->
            <aside class="right-col">
                <div class="routine-card">
                    <!-- Theme toggle (Sun / Moon) -->
                    <div class="theme-toggle" style="margin-bottom: 15px;">
                        <img src="images/sun.png" alt="Sun Icon" class="theme-icon" style="width:40px;">
                        <img src="images/moon.png" alt="Moon Icon" class="theme-icon" style="width:37px;">
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
</body>
</html>