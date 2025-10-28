<?php
function a_array($maybe): array {
    if (is_array($maybe)) return array_values(array_filter(array_map('trim',$maybe)));
    $t = trim((string)$maybe);
    return $t === '' ? [] : [$t];
}
function validate_log_input(array $p): array {
    $errors = [];
    $log_date = $p['log_date'] ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $log_date)) $errors['log_date'] = 'Invalid date.';
    $severity = $p['severity'] ?? '';
    if (!in_array($severity, ['Mild','Moderate','Severe'], true)) $errors['severity'] = 'Choose severity.';
    $locations = a_array($p['locations'] ?? []);
    $types     = a_array($p['types'] ?? []);
    if (!$locations) $errors['locations'] = 'Pick at least one location.';
    if (!$types)     $errors['types']     = 'Pick at least one type.';
    $water_cups = max(0, (int)($p['water_cups'] ?? 0));
    $activity = trim($p['activity'] ?? '');
    $notes = trim($p['notes'] ?? '');
    $clean = compact('log_date','severity','locations','types','water_cups','activity','notes');
    return [$errors, $clean];
}
