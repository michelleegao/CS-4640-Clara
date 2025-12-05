<?php
session_start();
$message = '';
$message_type = '';

if (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    $message_type = 'error';
    unset($_SESSION['error']);
} elseif (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    $message_type = 'success';
    unset($_SESSION['success']);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/login_style.css?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Michelle Gao">
    <title>Sign Up</title>
</head>

<body>
    <div class="container">
        <!-- Left side welcome text -->
        <div class="left-side">
            <h1>Clara</h1>

            <div class="welcome-container">
                <p class="welcome-text">Welcome back</p>
                <p class="welcome-text">Create an account today</p>
            </div>
        </div>

        <!-- Right side sign-up form -->
        <div class="right-side">
            <form class="login-form" method="POST" action="controller/login_controller.php?action=signup">
            <?php if ($message): ?>
                <div class="alert <?= htmlspecialchars($message_type) ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="First Name" required>

                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <button type="submit" class="submit-btn">Sign Up</button>
                <a href="index.php" class="login-btn">Back to Login</a>
            </form>
        </div>
    </div>
</body>
</html>
