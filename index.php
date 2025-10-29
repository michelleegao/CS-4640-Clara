<?php
session_start();

/*
Author: Michelle Gao and Henna Panjshiri
URL: https://cs4640.cs.virginia.edu/bnm5cm/Clara/
*/

$command = $_GET['command'] ?? 'home';

switch ($command) {
    case 'login':
        // redirect login POST to login_controller
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controller/login_controller.php';
        } else {
            header("Location: index.html");
        }
        break;

    case 'signup':
        // redirect signup POST to login_controller
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controller/login_controller.php';
        } else {
            header("Location: sign_up.html");
        }
        break;

    case 'logout':
        // Destroy session and go back to login
        session_unset();
        session_destroy();
        header("Location: index.html");
        break;

    case 'dailylog':
        // Protect daily log page (user must be logged in)
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.html");
            exit();
        }
        header("Location: daily_log.html");
        break;

    default:
        // default view
        header("Location: index.html");
        break;
}
?>