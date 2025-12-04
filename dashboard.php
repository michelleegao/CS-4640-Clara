<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="authors" content="Michelle Gao and Henna Panjshiri">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Clara: Dashboard</title>
    <link rel="stylesheet" href="styles/style.css?v=2">
</head>
  
<body>
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <a href="daily_log.php" class="home-icon">🏠</a>
                <ul>
                    <li><a href="daily_log.php">Log Today</a></li>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="current_routine.php">Current Routine</a></li>
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

    <div class="dropdown-container">
        <!--date range filter-->
        <label for="date-range">Date Range</label>
        <select id="date-range" name="date-range">
            <option value="1-week">1 Week</option>
            <option value="1-month">1 Month</option>
            <option value="6-months">6 Months</option>
            <option value="1-year">1 Year</option>
            <option value="all-time">All Time</option>
        </select>

        <!--breakout severity filter-->
        <label for="severity">Severity</label>
        <select id="severity" name="severity">
            <option value="">All</option>
            <option value="Mild">Mild</option>
            <option value="Moderate">Moderate</option>
            <option value="Severe">Severe</option>
        </select>
    </div>

    <!--Breakout frequency line chart-->
    <section class="chart-wide">
        <h3>Breakout Frequency Over Time</h3>
        <canvas id="breakoutChart" aria-label="Line chart showing breakout frequency over time." role="img"></canvas>
    </section>

    <!--Smaller breakout charts under line chart-->
    <div class="chart-row">
        <!--Location frequency bar graph-->
        <section class="chart-small">
            <h3>Breakout Location</h3>
            <canvas id="breakoutLocationChart" aria-label="Bar graph showing breakout frequency by body/face location." role="img"></canvas>
        </section>
        <!--Acne type-->
        <section class="chart-small">
            <h3>Breakout Types</h3>
            <canvas id="breakoutTypeChart" aria-label="Pie chart showing breakout types distribution." role="img"></canvas>
        </section>
        <!---->
        <section class="chart-small">
            <h3>Breakout Location</h3>
            <canvas id="breakoutTriggerChart" aria-label="Pie Chart showing breakout triggers distribution." role="img"></canvas>
        </section>
    </div>


    <script>
    document.addEventListener("DOMContentLoaded", () => {

        let timelineChart = null;
        let locationChart = null;
        let typeChart = null;
        let triggerChart = null;

        async function loadTrends(range = '1-week', severity = '') {
            try {
                const params = new URLSearchParams({ action: 'json', range });
                if (severity) params.append('severity', severity);

                const response = await fetch('controller/trends_controller.php?' + params.toString(), {
                    credentials: 'include'
                });

                const result = await response.json();

                if (!result.success) {
                    console.warn("No trend data found:", result);
                    return;
                }

                // Timeline line chart
                const timeline = result.timeline || [];

                const labelsTimeline = timeline.map(r => r.log_date);
                const countsTimeline = timeline.map(r => r.breakout_count);

                if (timelineChart instanceof Chart) timelineChart.destroy();

                const ctxTimeline = document.getElementById('breakoutChart').getContext('2d');

                timelineChart = new Chart(ctxTimeline, {
                    type: 'line',
                    data: {
                        labels: labelsTimeline,
                        datasets: [{
                            label: 'Breakouts Over Time',
                            data: countsTimeline,
                            borderColor: '#7ca982',
                            backgroundColor: 'rgba(124,169,130,0.25)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 6,
                            pointBackgroundColor: '#243e36'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { title: { display: true, text: 'Date' }},
                            y: { 
                                title: { display: true, text: 'Breakout Count' }, 
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        },
                        plugins: { legend: { display: false } }
                    }
                });

                // Location bar chart
                const locations = result.locations || [];

                const labelsLocation = locations.map(r => {
                    if (!r.locations) return "Unknown";
                    // Remove curly braces from Postgres array text
                    let cleaned = r.locations.replace(/[{}]/g, "");
                    // Capitalize first letter
                    cleaned = cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
                    return cleaned;
                });
                const countsLocation = locations.map(r => r.location_count);

                if (locationChart instanceof Chart) locationChart.destroy();

                const ctxLoc = document.getElementById('breakoutLocationChart').getContext('2d');

                locationChart = new Chart(ctxLoc, {
                    type: 'bar',
                    data: {
                        labels: labelsLocation,
                        datasets: [{
                            label: 'Breakouts by Location',
                            data: countsLocation,
                            backgroundColor: 'rgba(124,169,130,0.5)',
                            borderColor: '#7ca982',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { title: { display: true, text: 'Location' }},
                            y: { 
                                title: { display: true, text: 'Breakout Count' },
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        },
                        plugins: { legend: { display: false } }
                    }
                });

                // Breakout types pie chart
                const types = result.types || [];

                const labelsTypes = types.map(r => {
                    if (!r.types) return "Unknown";
                    // Remove curly braces from Postgres array text
                    let cleaned = r.types.replace(/[{}]/g, "");
                    // Capitalize first letter
                    cleaned = cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
                    return cleaned;
                });
                const countsType = types.map(r => r.type_count);

                // Colors for each slice
                const typeColors = [
                    "#7ca982",
                    "#243e36",
                    "#c9ddc9",
                    "#a4c3b2",
                    "#8d957d",
                    "#b2bc9f"
                ];

                if (typeChart instanceof Chart) typeChart.destroy();

                const ctxType = document.getElementById('breakoutTypeChart').getContext('2d');

                typesChart = new Chart(ctxType, {
                    type: 'pie',
                    data: {
                        labels: labelsTypes,
                        datasets: [{
                            label: 'Breakouts by Type',
                            data: countsType,
                            backgroundColor: typeColors.slice(0, countsType.length),
                            borderColor: '#7ca982',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: true } }
                    }
                });

                // Breakout triggers pie chart
                const triggers = result.triggers || [];

                const labelsTriggers = triggers.map(r => {
                    if (!r.activity) return "Unknown";
                    // Remove curly braces from Postgres array text
                    let cleaned = r.activity.replace(/[{}]/g, "");
                    // Capitalize first letter
                    cleaned = cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
                    return cleaned;
                });
                const countsTrigger = triggers.map(r => r.trigger_count);

                // Colors for each slice
                const triggerColors = [
                    "#7ca982",
                    "#243e36",
                    "#c9ddc9",
                    "#a4c3b2",
                    "#8d957d",
                    "#b2bc9f"
                ];

                if (triggerChart instanceof Chart) triggerChart.destroy();

                const ctxTrigger = document.getElementById('breakoutTriggerChart').getContext('2d');

                triggerChart = new Chart(ctxTrigger, {
                    type: 'pie',
                    data: {
                        labels: labelsTriggers,
                        datasets: [{
                            label: 'Breakouts by Type',
                            data: countsTrigger,
                            backgroundColor: triggerColors.slice(0, countsTrigger.length),
                            borderColor: '#7ca982',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: true } }
                    }
                });

            } catch (err) {
                console.error("Failed to load trend data:", err);
            }
        }

        // Filters
        const rangeSelect = document.getElementById('date-range');
        const severitySelect = document.getElementById('severity');

        // Initial load
        loadTrends(rangeSelect.value, severitySelect.value);

        // Update charts when filters change
        rangeSelect.addEventListener('change', () => {
            loadTrends(rangeSelect.value, severitySelect.value);
        });

        severitySelect.addEventListener('change', () => {
            loadTrends(rangeSelect.value, severitySelect.value);
        });

    });
    </script>

</body>
</html>