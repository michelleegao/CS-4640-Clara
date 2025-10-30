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
    <link rel="stylesheet" href="styles/style.css">
</head>
  
<body>
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <a href="daily_log.php" class="home-icon">🏠</a>
                <ul>
                    <li><a href="daily_log.php">Log Today</a></li>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="current_routine.html">Current Routine</a></li>
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
            <option value="Mild">Mild</option>
            <option value="Moderate">Moderate</option>
            <option value="Severe">Severe</option>
            <option value="">All</option>
        </select>
    </div>

    <section class="chart-fixed">
        <h3>Breakout Frequency Over Time</h3>
        <canvas id="breakoutChart"></canvas>
    </section>


    <script>
    document.addEventListener("DOMContentLoaded", () => {
    async function loadTrends(range = '1-week', severity = '') {
        try {
        const params = new URLSearchParams({ action: 'json', range });
        if (severity) params.append('severity', severity);

        const response = await fetch('controller/trends_controller.php?' + params.toString(), {
            credentials: 'include'
        });
        const result = await response.json();

        if (!result.success || !result.data) {
            console.warn('No trend data found:', result);
            if (window.breakoutChart instanceof Chart) window.breakoutChart.destroy();
            const ctx = document.getElementById('breakoutChart').getContext('2d');
            window.breakoutChart = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [{ data: [] }] },
            options: { plugins: { legend: { display: false } } }
            });
            return;
        }

        const labels = result.data.map(r => r.log_date);
        const counts = result.data.map(r => r.breakout_count);

        if (window.breakoutChart instanceof Chart) {
            window.breakoutChart.destroy();
        }

        const ctx = document.getElementById('breakoutChart').getContext('2d');
        window.breakoutChart = new Chart(ctx, {
            type: 'line',
            data: {
            labels,
            datasets: [{
                label: 'Breakouts',
                data: counts,
                borderColor: '#7ca982',
                backgroundColor: 'rgba(124,169,130,0.25)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#243e36'
            }]
            },
            options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Breakout Count' }, beginAtZero: true }
            },
            plugins: { legend: { display: false } }
            }
        });
        } catch (err) {
        console.error('Failed to load trend data:', err);
        }
    }

    const rangeSelect = document.getElementById('date-range');
    const severitySelect = document.getElementById('severity');
    loadTrends(rangeSelect?.value || '1-week', severitySelect?.value || '');

    rangeSelect?.addEventListener('change', (e) => {
        loadTrends(e.target.value, severitySelect?.value || '');
    });
    severitySelect?.addEventListener('change', (e) => {
        loadTrends(rangeSelect?.value || '1-week', e.target.value);
    });
    });
    </script>

</body>
</html>