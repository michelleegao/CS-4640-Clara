<?php
function valid_email(string $s): bool {
  return filter_var($s, FILTER_VALIDATE_EMAIL) && preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $s);
}
function valid_password(string $s): bool {
  return preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $s);
}
function a_array($maybe): array {
  if (is_array($maybe)) return array_values(array_filter(array_map('trim',$maybe)));
  $t = trim((string)$maybe);
  return $t === '' ? [] : [$t];
}

