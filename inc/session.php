<?php
session_start();

function require_login() {
  if (empty($_SESSION['user_id'])) {
    $_SESSION['flash'] = 'Please log in.';
    header('Location: index.html'); exit;
  }
}

function flash(string $msg) { $_SESSION['flash'] = $msg; }
function take_flash(): ?string {
  if (!empty($_SESSION['flash'])) { $m=$_SESSION['flash']; unset($_SESSION['flash']); return $m; }
  return null;
}
