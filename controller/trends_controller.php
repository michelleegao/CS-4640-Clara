<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/Database.php';

class trends_controller {

    public function dispatch(string $action, array $data = []): void {
        // Check if user is logged in
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Please log in.']);
            return;
        }

        switch ($action) {
            case 'json':
                $this->json($_GET);
                break;

            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Unknown action.']);
        }
    }

    private function json(array $q): void {
        header('Content-Type: application/json; charset=utf-8');

        // Check if user is logged in
        $uid = $_SESSION['user_id'] ?? null;
        if (!$uid) {
            echo json_encode(['success' => false, 'error' => 'User not logged in.']);
            return;
        }

        $range = $q['range'] ?? '1-week';
        $severity = $q['severity'] ?? null;

        try {
            $pdo = Database::pdo();

            // Determine cutoff date
            $cutoff = null;
            switch ($range) {
                case '1-week': $cutoff = date('Y-m-d', strtotime('-7 days')); break;
                case '1-month': $cutoff = date('Y-m-d', strtotime('-30 days')); break;
                case '6-months': $cutoff = date('Y-m-d', strtotime('-180 days')); break;
                case '1-year': $cutoff = date('Y-m-d', strtotime('-365 days')); break;
                case 'all-time':
                default: $cutoff = null; break;
            }

            // Build query after filters have been applied
            $sql = "
                SELECT log_date::date AS log_date, COUNT(*) AS breakout_count
                FROM logs
                WHERE user_id = :uid
            ";

            if ($cutoff) $sql .= " AND log_date >= :cutoff";
            if (!empty($severity) && in_array($severity, ['Mild', 'Moderate', 'Severe'], true)) {
                $sql .= " AND severity = :severity";
            }

            $sql .= " GROUP BY log_date::date ORDER BY log_date::date ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
            if ($cutoff) $stmt->bindValue(':cutoff', $cutoff);
            if (!empty($severity)) $stmt->bindValue(':severity', $severity);
            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}

if (php_sapi_name() !== 'cli' && isset($_GET['action'])) {
    $controller = new trends_controller();
    $controller->dispatch($_GET['action'], $_POST);
}
