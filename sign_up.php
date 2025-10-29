<?php
// require_once 'Config.php';
// require_once 'Database.php';
// require_once 'controller/login_controller.php';

// // Establish database connection
// try {
//     $pdo = new PDO(
//         "pgsql:host=" . Config::$db['host'] .
//         ";port=" . Config::$db['port'] .
//         ";dbname=" . Config::$db['database'],
//         Config::$db['user'],
//         Config::$db['pass']
//     );
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Database connection failed: " . $e->getMessage());
// }

// $controller = new login_controller($pdo);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/login_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Michelle Gao">
    <title>Sign Up</title>
</head>

<body>
    <div class="container">
        <!-- Left side welcome text -->
        <div class="left-side">
            <h1>Clara</h1>
            <p class="welcome-text">Welcome to Clara</p>
            <p class="welcome-text">Create an account today</p>
        </div>

        <!-- Right side sign-up form -->
        <div class="right-side">
            <form class="login-form" method="POST" action="controller/login_controller.php?action=signup">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="First Name" required>

                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <button type="submit" class="login-btn">Sign Up</button>
            </form>
            
            <a href="index.php" class="login-btn">Back to Login</a>
        </div>
    </div>
</body>
</html>
