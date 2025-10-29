<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../src/Config.php';
require_once __DIR__ . '/../src/Database.php';

class login_controller {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::pdo();
    }

    // handling sign up
    public function handleSignup() {
        $first = trim($_POST['first_name'] ?? '');
        $last = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $errors = [];

        // form validation
        if (!preg_match("/^[A-Za-z'-]+$/", $first)) {
            $errors[] = "First name can only contain letters, hyphens, or apostrophes.";
        }
        if (!preg_match("/^[A-Za-z'-]+$/", $last)) {
            $errors[] = "Last name can only contain letters, hyphens, or apostrophes.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (!preg_match("/^(?=.*[A-Z])(?=.*\d).{6,}$/", $password)) {
            $errors[] = "Password must be at least 6 characters, include a number and an uppercase letter.";
        }

        if (!empty($errors)) {
            $this->displayErrors($errors, '../sign_up.php');
            return;
        }

        // concatenate display name
        $display_name = ucwords(strtolower("$first $last"));

        // check for existing email
        $check = $this->pdo->prepare("SELECT id FROM users_clara WHERE email = :email");
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            echo "<h3>Email already registered. Please log in instead.</h3>";
            echo "<a href='../index.php'>Go to login</a>";
            return;
        }

        // new user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $this->pdo->prepare("
            INSERT INTO users_clara (email, password_hash, display_name)
            VALUES (:e, :p, :d)
        ");
        $insert->execute(['e' => $email, 'p' => $hash, 'd' => $display_name]);

        echo "<h3>Account created successfully!</h3>";
        echo "<a href='../index.php'>Login now</a>";
    }

    // handling login
    public function handleLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo "<h3>Please fill in both email and password fields.</h3>";
            echo "<a href='../index.php'>Back to Login</a>";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<h3>Invalid email format.</h3>";
            echo "<a href='../index.php'>Try again</a>";
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users_clara WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['display_name'] = $user['display_name'];
            header("Location: ../daily_log.html");
            exit();
        } else {
            echo "<h3>Invalid email or password.</h3>";
            echo "<a href='../index.php'>Try again</a>";
        }
    }

    // helper function
    private function displayErrors($errors, $redirect) {
        echo "<h3>Sign-up Error(s):</h3><ul>";
        foreach ($errors as $e) echo "<li>$e</li>";
        echo "</ul><a href='$redirect'>Go back</a>";
    }
}

// router
$action = $_GET['action'] ?? '';
$auth = new login_controller();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'signup') {
        $auth->handleSignup();
    } elseif ($action === 'login') {
        $auth->handleLogin();
    }
}
?>
