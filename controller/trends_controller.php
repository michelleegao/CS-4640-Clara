<?php
require_once __DIR__ . '/../src/Database.php';

class trends_controller {

    public function dispatch(string $action, array $params): void {
        $this->require_login();

        switch ($action) {
            case 'overview':
                $this->overview($_GET);
                break;
            case 'series':
                $this->series($_GET);
                break;
            case 'severity':
                $this->severity($_GET);
                break;
            case 'locations':
                $this->locations($_GET);
                break;
            default:
                http_response_code(404);
                echo 'Unknown trends action.';
        }
    }

    private function overview(array $q): void {
        header('Content-Type: application/json');
        $days = $this->range_to_days($q['range'] ?? '1-week');

        $pdo = Database::pdo();
        $uid = (int)$_SESSION['user_id'];

        $sql1 = "
            SELECT
              COUNT(*)                           AS total_logs,
              COUNT(DISTINCT log_date)           AS days_logged,
              COALESCE(MAX(log_date), CURRENT_DATE) AS last_log_date,
              COALESCE(AVG(NULLIF(water_cups,0)), 0) AS avg_water_cups
            FROM logs
            WHERE user_id = :uid
              AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
        ";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([':uid' => $uid]);
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

        $sql2 = "
            SELECT severity, COUNT(*) c
            FROM logs
            WHERE user_id = :uid
              AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
            GROUP BY severity
            ORDER BY c DESC, severity ASC
            LIMIT 1
        ";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([':uid' => $uid]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        $sql3 = "
            SELECT loc, COUNT(*) c
            FROM (
                SELECT UNNEST(locations) AS loc
                FROM logs
                WHERE user_id = :uid
                  AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
            ) t
            GROUP BY loc
            ORDER BY c DESC, loc ASC
            LIMIT 1
        ";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->execute([':uid' => $uid]);
        $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'summary' => [
                'total_logs'        => (int)($row1['total_logs'] ?? 0),
                'days_logged'       => (int)($row1['days_logged'] ?? 0),
                'last_log_date'     => $row1['last_log_date'] ?? null,
                'avg_water_cups'    => (float)$row1['avg_water_cups'],
                'top_severity'      => $row2['severity'] ?? null,
                'top_location'      => $row3['loc'] ?? null,
            ]
        ]);
    }

    private function series(array $q): void {
        header('Content-Type: application/json');
        $days = $this->range_to_days($q['range'] ?? '1-week');
        $sev  = $this->normalize_severity($q['severity'] ?? null);

        $pdo = Database::pdo();
        $uid = (int)$_SESSION['user_id'];

        $params = [':uid' => $uid];
        $sql = "
            SELECT log_date::date AS day, COUNT(*) AS count
            FROM logs
            WHERE user_id = :uid
              AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
        ";
        if ($sev) {
            $sql .= " AND severity = :sev";
            $params[':sev'] = $sev;
        }
        $sql .= " GROUP BY day ORDER BY day ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['series' => $rows]);
    }

    private function severity(array $q): void {
        header('Content-Type: application/json');
        $days = $this->range_to_days($q['range'] ?? '1-week');

        $pdo = Database::pdo();
        $uid = (int)$_SESSION['user_id'];

        $stmt = $pdo->prepare("
            SELECT severity, COUNT(*) AS count
            FROM logs
            WHERE user_id = :uid
              AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
            GROUP BY severity
            ORDER BY severity
        ");
        $stmt->execute([':uid' => $uid]);
        echo json_encode(['by_severity' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    private function locations(array $q): void {
        header('Content-Type: application/json');
        $days = $this->range_to_days($q['range'] ?? '1-week');

        $pdo = Database::pdo();
        $uid = (int)$_SESSION['user_id'];

        $stmt = $pdo->prepare("
            SELECT loc AS location, COUNT(*) AS count
            FROM (
                SELECT UNNEST(locations) AS loc
                FROM logs
                WHERE user_id = :uid
                  AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'
            ) x
            GROUP BY loc
            ORDER BY count DESC, loc ASC
        ");
        $stmt->execute([':uid' => $uid]);
        echo json_encode(['by_location' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }


    private function require_login(): void {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash'] = 'Please log in.';
            header('Location: /index.html');
            exit;
        }
    }

    private function range_to_days(string $range): int {
        $map = [
            '1-week'   => 7,
            '1-month'  => 30,
            '6-months' => 180,
            '1-year'   => 365,
            'all-time' => 36500
        ];
        return $map[$range] ?? 7;
    }

    private function normalize_severity(?string $s): ?string {
        if (!$s) return null;
        $allowed = ['Mild','Moderate','Severe'];
        return in_array($s, $allowed, true) ? $s : null;
    }
}
