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
<!--URL: https://cs4640.cs.virginia.edu/bnm5cm/Clara-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/login_style.css?v=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Michelle Gao">
    <title>Login Page</title>
</head>

<body>
    <div class="container">
        <!-- Left side: welcome message -->
        <div class="left-side">
            <h1>Clara</h1>
            <p class="welcome-text">Welcome back</p>
            <p class="welcome-text">Login to your account</p>
        </div>

        <!-- Right side: login form -->
        <div class="right-side">
            <form class="login-form" method="POST" action="controller/login_controller.php?action=login">
            <?php if ($message): ?>
                <div class="alert <?= htmlspecialchars($message_type) ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>
                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <button type="submit" class="submit-btn">Continue</button>
                <a href="sign_up.php" class="login-btn">Sign Up</a>
            </form>
        </div>
    </div>
</body>
</html>
