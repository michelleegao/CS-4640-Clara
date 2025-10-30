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

    // sign up handler
    public function handleSignup() {
        $first = trim($_POST['first_name'] ?? '');
        $last = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $errors = [];

        // validation
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
            $errors[] = "Password must be at least 6 characters and include an uppercase letter and a number.";
        }

        // if errors found, send them back to sign up page
        if (!empty($errors)) {
            $_SESSION['error'] = implode("<br>", $errors);
            header("Location: ../sign_up.php");
            exit();
        }

        // check for existing email
        $check = $this->pdo->prepare("SELECT id FROM users_clara WHERE email = :email");
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Email already registered. Please log in instead.";
            header("Location: ../index.php");
            exit();
        }

        // insert new user
        $display_name = ucwords(strtolower("$first $last"));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $this->pdo->prepare("
            INSERT INTO users_clara (email, password_hash, display_name)
            VALUES (:e, :p, :d)
        ");
        $insert->execute(['e' => $email, 'p' => $hash, 'd' => $display_name]);

        $_SESSION['success'] = "Account created successfully! You can now log in.";
        header("Location: ../index.php");
        exit();
    }

    // login handler
    public function handleLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Please fill in both email and password.";
            header("Location: ../index.php");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            header("Location: ../index.php");
            exit();
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users_clara WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['display_name'] = $user['display_name'];
            header("Location: ../daily_log.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect email or password.";
            header("Location: ../index.php");
            exit();
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
