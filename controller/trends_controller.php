<?php
session_start();
require_once __DIR__ . '/../src/Database.php';

class trends_controller {

    public function dispatch(string $action, array $params = []): void {
        $this->require_login();

        switch ($action) {
            case 'json':
                $this->json($params);
                break;

            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Unknown trends action.']);
        }
    }

    private function json(array $q): void {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'User not logged in']);
            return;
        }

        // --- Handle filters ---
        $range = $q['range'] ?? '1-month';
        $severity = $q['severity'] ?? 'all';

        $rangeToDays = [
            '1-week'   => 7,
            '1-month'  => 30,
            '6-months' => 180,
            '1-year'   => 365,
            'all-time' => 36500
        ];
        $days = $rangeToDays[$range] ?? 30;

        $pdo = Database::pdo();
        $sql = "
            SELECT log_date, COUNT(*) AS breakout_count
            FROM logs
            WHERE user_id = :uid
              AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
        ";

        $params = [':uid' => $user_id];
        if ($severity !== 'all') {
            $sql .= " AND severity = :severity";
            $params[':severity'] = ucfirst($severity); // Match DB case
        }

        $sql .= " GROUP BY log_date ORDER BY log_date ASC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function require_login(): void {
        if (empty($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Please log in.']);
            exit;
        }
    }
}

if (php_sapi_name() !== 'cli' && isset($_GET['action'])) {
    $controller = new trends_controller();
    $controller->dispatch($_GET['action'], $_POST);
}