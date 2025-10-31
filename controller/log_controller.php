<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

class log_controller {

    public function dispatch(string $action, array $data): void {
        $this->require_login();

        switch ($action) {
            case 'create':
                $this->create($data);
                break;

            case 'delete':
                $this->delete($_GET);
                break;

            case 'json':
                $this->json($_GET);
                break;

            default:
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Unknown log action.']);
        }
    }

    private function create(array $p): void {
        header('Content-Type: application/json');

        $errors = [];
        $clean = [];

        // Severity (required)
        // Severity (radio)
        $allowedSeverity = ['Mild', 'Moderate', 'Severe'];
        if (empty($p['severity']) || !in_array($p['severity'], $allowedSeverity, true)) {
            $errors['severity'] = true;
        } else {
            $clean['severity'] = $p['severity'];
        }

        // Locations (checkbox array)
        if (!empty($p['locations']) && is_array($p['locations'])) {
            // Convert PHP array to Postgres text array format
            $clean['locations'] = '{' . implode(',', array_map('trim', $p['locations'])) . '}';
        } else {
            $clean['locations'] = '{}';
        }

        // Types (checkbox array)
        if (!empty($p['types']) && is_array($p['types'])) {
            $clean['types'] = '{' . implode(',', array_map('trim', $p['types'])) . '}';
        } else {
            $clean['types'] = '{}';
        }

        // Activity (checkbox array)
        if (!empty($p['activity']) && is_array($p['activity'])) {
            $clean['activity'] = implode(', ', array_map('trim', $p['activity']));
        } else {
            $clean['activity'] = null;
        }

        // Water cups
        $clean['water_cups'] = max(0, (int)($p['water_cups'] ?? 0));

        // Notes (optional)
        $clean['notes'] = trim($p['notes'] ?? '');

        // Log date
        $clean['log_date'] = date('Y-m-d');

        // If validation failed
        if ($errors) {
            echo json_encode(['success' => false, 'error' => 'Please fix: ' . implode(', ', array_keys($errors))]);
            exit;
        }

        // Prepare SQL insert statements
        try {
            $sql = "INSERT INTO logs
                    (user_id, log_date, locations, severity, types, water_cups, activity, notes, created_at)
                    VALUES (:uid, :d, :loc::text[], :sev, :typ::text[], :w, :act, :notes, NOW())";

            $stmt = Database::pdo()->prepare($sql);
            $stmt->execute([
                ':uid'   => $_SESSION['user_id'],
                ':d'     => $clean['log_date'],
                ':loc'   => $clean['locations'],
                ':sev'   => $clean['severity'],
                ':typ'   => $clean['types'],
                ':w'     => $clean['water_cups'],
                ':act'   => $clean['activity'],
                ':notes' => $clean['notes']
            ]);

            echo json_encode(['success' => true, 'message' => 'Log successfully saved!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // Function to delete log
    private function delete(array $q): void {
        header('Content-Type: application/json');
        $id = (int)($q['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid log ID.']);
            return;
        }

        $stmt = Database::pdo()->prepare("DELETE FROM logs WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $id, ':uid' => $_SESSION['user_id']]);
        echo json_encode(['success' => true, 'message' => 'Log deleted successfully']);
    }

    private function json(array $q): void {
        header('Content-Type: application/json');
        $range = $q['range'] ?? '1-week';
        $rangeToDays = ['1-week' => 7, '1-month' => 30, '6-months' => 180, '1-year' => 365];
        $days = $rangeToDays[$range] ?? 7;

        $stmt = Database::pdo()->prepare("
            SELECT id, log_date, locations, severity, types, water_cups, activity, notes
            FROM logs
            WHERE user_id = :uid
              AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
            ORDER BY log_date DESC, id DESC LIMIT 200
        ");
        $stmt->execute([':uid' => $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'logs' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    private function require_login(): void {
        if (empty($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Please log in.']);
            exit;
        }
    }
}

if (php_sapi_name() !== 'cli') {
    $controller = new log_controller();
    $controller->dispatch($_GET['action'] ?? '', $_POST);
}
