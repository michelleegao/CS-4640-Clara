<?php
// Authors: Henna Panjshiri
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

            // Build query for timeline after filters have been applied
            $sqlTimeline = "
                SELECT log_date::date AS log_date, COUNT(*) AS breakout_count
                FROM logs
                WHERE user_id = :uid
            ";

            if ($cutoff) $sqlTimeline .= " AND log_date >= :cutoff";
            if (!empty($severity) && in_array($severity, ['Mild', 'Moderate', 'Severe'], true)) {
                $sqlTimeline .= " AND severity = :severity";
            }

            $sqlTimeline .= " GROUP BY log_date::date ORDER BY log_date::date ASC";

            $stmt = $pdo->prepare($sqlTimeline);
            $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
            if ($cutoff) $stmt->bindValue(':cutoff', $cutoff);
            if (!empty($severity)) $stmt->bindValue(':severity', $severity);
            $stmt->execute();

            $timelineData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build query for location bar graph after filters have been applied
            $sqlLocation = "
                SELECT locations, COUNT(*) AS location_count
                FROM logs
                WHERE user_id = :uid
            ";

            if ($cutoff) $sqlLocation .= " AND log_date >= :cutoff";
            if (!empty($severity)) $sqlLocation .= " AND severity = :severity";

            $sqlLocation .= " GROUP BY locations ORDER BY locations ASC";

            $stmtLoc = $pdo->prepare($sqlLocation);
            $stmtLoc->bindValue(':uid', $uid, PDO::PARAM_INT);
            if ($cutoff) $stmtLoc->bindValue(':cutoff', $cutoff);
            if (!empty($severity)) $stmtLoc->bindValue(':severity', $severity);
            $stmtLoc->execute();

            $locationData = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);

            // Build query for breakout types pie chart after filters have been applied
            $sqlTypes = "
                SELECT types, COUNT(*) AS type_count
                FROM logs
                WHERE user_id = :uid
            ";

            if ($cutoff) $sqlTypes .= " AND log_date >= :cutoff";
            if (!empty($severity)) $sqlTypes .= " AND severity = :severity";

            $sqlTypes .= " GROUP BY types ORDER BY types ASC";

            $stmtTypes = $pdo->prepare($sqlTypes);
            $stmtTypes->bindValue(':uid', $uid, PDO::PARAM_INT);
            if ($cutoff) $stmtTypes->bindValue(':cutoff', $cutoff);
            if (!empty($severity)) $stmtTypes->bindValue(':severity', $severity);
            $stmtTypes->execute();

            $typeData = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

            // Build query for breakout types pie chart after filters have been applied
            $sqlTriggers = "
                SELECT activity, COUNT(*) AS trigger_count
                FROM logs
                WHERE user_id = :uid
            ";

            if ($cutoff) $sqlTriggers .= " AND log_date >= :cutoff";
            if (!empty($severity)) $sqlTriggers .= " AND severity = :severity";

            $sqlTriggers .= " GROUP BY activity ORDER BY activity ASC";

            $stmtTriggers = $pdo->prepare($sqlTriggers);
            $stmtTriggers->bindValue(':uid', $uid, PDO::PARAM_INT);
            if ($cutoff) $stmtTriggers->bindValue(':cutoff', $cutoff);
            if (!empty($severity)) $stmtTriggers->bindValue(':severity', $severity);
            $stmtTriggers->execute();

            $triggersData = $stmtTriggers->fetchAll(PDO::FETCH_ASSOC);

            // JSON response
            echo json_encode([
                'success' => true,
                'timeline' => $timelineData,
                'locations' => $locationData,
                'types' => $typeData,
                'triggers' => $triggersData
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
