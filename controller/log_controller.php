<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../inc/util.php';

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
                http_response_code(404);
                echo 'Unknown log action.';
        }
    }


    private function create(array $p): void {
        list($errors, $clean) = validate_log_input($p);

        if ($errors) {
            $this->flash('Please fix: ' . implode(', ', array_keys($errors)));
            header('Location: /daily_log.php');
            exit;
        }

        $sql = "INSERT INTO logs
                (user_id, log_date, locations, severity, types, water_cups, activity, notes, created_at)
                VALUES (:uid, :d, :loc, :sev, :typ, :w, :act, :notes, NOW())";

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

        $this->flash('Log saved.');
        header('Location: /daily_log.php');
        exit;
    }

    private function delete(array $q): void {
        $id = (int)($q['id'] ?? 0);
        if ($id <= 0) {
            $this->flash('Bad id.');
            header('Location: /daily_log.php');
            exit;
        }

        $stmt = Database::pdo()->prepare(
            "DELETE FROM logs WHERE id = :id AND user_id = :uid"
        );
        $stmt->execute([':id' => $id, ':uid' => $_SESSION['user_id']]);

        $this->flash('Log deleted.');
        header('Location: /daily_log.php');
        exit;
    }

    private function json(array $q): void {
        header('Content-Type: application/json');

        $range = $q['range'] ?? '1-week';
        $rangeToDays = [
            '1-week'   => 7,
            '1-month'  => 30,
            '6-months' => 180,
            '1-year'   => 365,
            'all-time' => 36500
        ];
        $days = $rangeToDays[$range] ?? 7;

        $severity = $q['severity'] ?? null;
        $params = [':uid' => $_SESSION['user_id']];

        $sql = "SELECT id, log_date, locations, severity, types, water_cups, activity, notes
                FROM logs
                WHERE user_id = :uid
                  AND log_date >= CURRENT_DATE - INTERVAL '{$days} day'";

        if ($severity && in_array($severity, ['Mild', 'Moderate', 'Severe'], true)) {
            $sql .= " AND severity = :sev";
            $params[':sev'] = $severity;
        }

        $sql .= " ORDER BY log_date DESC, id DESC LIMIT 200";

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['logs' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }


    private function require_login(): void {
        if (empty($_SESSION['user_id'])) {
            $this->flash('Please log in.');
            header('Location: /index.html');
            exit;
        }
    }

    private function flash(string $msg): void {
        $_SESSION['flash'] = $msg;
    }

    public static function take_flash(): ?string {
        if (!empty($_SESSION['flash'])) {
            $m = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $m;
        }
        return null;
    }
}
