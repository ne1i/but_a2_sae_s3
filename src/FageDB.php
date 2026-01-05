<?php

namespace ButA2SaeS3;

use PDO;

class FageDB
{
    private $db;
    private $db_path;

    function __construct()
    {
        if (isset($_ENV["DB_PATH"])) {
            $this->db_path = $_ENV["DB_PATH"];
        } else {
            $this->db_path = __DIR__ . "/../data/fage.db";
        }

        $db_dir = dirname($this->db_path);
        if (!is_dir($db_dir)) {
            mkdir($db_dir, 0755, true);
        }

        if (file_exists($this->db_path)) {
            $this->db = new PDO("sqlite:" . $this->db_path);
        } else {
            $this->db = new PDO("sqlite:" . $this->db_path);
            $this->init_db();
        }
    }

    public function getConnection(): PDO
    {
        return $this->db;
    }

    function init_db()
    {
        $init_script = file_get_contents(__DIR__ . "/db/init_script.sql");
        $this->db->exec($init_script);
        $this->add_user("admin", "admin", "admin");
    }

    function check_login_creds($username, $password)
    {
        $user_stmt = $this->db->prepare("SELECT password_hash FROM users WHERE username = :username");
        $user_stmt->execute([":username" => $username]);
        $p_hash = $user_stmt->fetchColumn();
        return password_verify($password, $p_hash);
    }

    function add_user($username, $password, $role, $poles = [])
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $admin_stmt = $this->db->prepare("INSERT INTO users(username, password_hash) VALUES(:username, :password_hash)");
        $admin_stmt->execute([
            ":username" => $username,
            ":password_hash" => $hash
        ]);

        if ($role === "admin") {
            $this->add_role_to_user($username, $role);
            return;
        }

        foreach ($poles as $pole) {
            $this->add_role_to_user($username, $pole);
        }
    }

    function get_user_id($username)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([
            ":username" => $username
        ]);
        return $stmt->fetchColumn() ?? false;
    }

    function add_role_to_user($username, $role_str)
    {
        $user_id = $this->get_user_id($username);
        $role_id = $this->get_role_id_from_role($role_str);
        if ($user_id === false || $role_id === false) {
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO user_roles(user_id, role_id) VALUES(:user_id, :role_id)");
        $stmt->execute([
            ":user_id" => $user_id,
            ":role_id" => $role_id,
        ]);
    }

    function get_role_id_from_role($role_str)
    {
        $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = :role");
        $stmt->execute([
            ":role" => $role_str
        ]);
        return $stmt->fetchColumn() ?? false;
    }

    function create_session($username, $session_id, $expiration_date)
    {
        $stmt = $this->db->prepare("INSERT INTO sessions(session_id, user_id, expires_at) VALUES(:session_id, :user_id, :expiration_date)");
        $user_id = $this->get_user_id($username);
        if ($user_id === false) {
            return;
        }
        $stmt->execute([
            ":session_id" => $session_id,
            ":user_id" => $user_id,
            ":expiration_date" => $expiration_date,
        ]);
    }

    function get_user_id_from_session($session_id)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM sessions WHERE session_id = :session_id AND expires_at > CURRENT_TIMESTAMP");
        $stmt->execute([
            ":session_id" => $session_id,
        ]);
        $user_id = $stmt->fetchColumn();
        return $user_id ? (int)$user_id : null;
    }

    function get_adherent_statistics()
    {
        $stats = [];

        $stmt = $this->db->prepare("SELECT COUNT(id) FROM adherents WHERE is_active = 1");
        $stmt->execute();
        $stats['total_active'] = $stmt->fetchColumn();

        $year_start = strtotime(date('Y-01-01'));
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM adherents WHERE joined_at >= :year_start");
        $stmt->bindValue(":year_start", $year_start, PDO::PARAM_INT);
        $stmt->execute();
        $stats['new_this_year'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT 
                CASE 
                    WHEN age < 18 THEN 'Moins de 18'
                    WHEN age BETWEEN 18 AND 25 THEN '18-25'
                    WHEN age BETWEEN 26 AND 35 THEN '26-35'
                    WHEN age BETWEEN 36 AND 50 THEN '36-50'
                    WHEN age > 50 THEN 'Plus de 50'
                    ELSE 'Non spécifié'
                END as age_group,
                COUNT(*) as count
            FROM adherents 
            WHERE is_active = 1 AND age IS NOT NULL
            GROUP BY age_group
            ORDER BY count DESC
        ");
        $stmt->execute();
        $stats['age_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
            SELECT city, COUNT(*) as count 
            FROM adherents 
            WHERE is_active = 1 AND city IS NOT NULL AND city != ''
            GROUP BY city 
            ORDER BY count DESC 
            LIMIT 10
        ");
        $stmt->execute();
        $stats['city_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
            SELECT profession, COUNT(*) as count 
            FROM adherents 
            WHERE is_active = 1 AND profession IS NOT NULL AND profession != ''
            GROUP BY profession 
            ORDER BY count DESC 
            LIMIT 10
        ");
        $stmt->execute();
        $stats['profession_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    function get_mission_statistics()
    {
        $stats = [];

        $stmt = $this->db->prepare("SELECT COUNT(id) FROM missions");
        $stmt->execute();
        $stats['total_missions'] = $stmt->fetchColumn();

        $year_start = strtotime(date('Y-01-01'));
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM missions WHERE start_at >= :year_start");
        $stmt->bindValue(":year_start", $year_start, PDO::PARAM_INT);
        $stmt->execute();
        $stats['missions_this_year'] = $stmt->fetchColumn();

        $current_time = time();
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM missions WHERE start_at > :current_time");
        $stmt->bindValue(":current_time", $current_time, PDO::PARAM_INT);
        $stmt->execute();
        $stats['upcoming_missions'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT adherent_id) FROM mission_participants");
        $stmt->execute();
        $stats['total_participants'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT AVG(participant_count) as avg_participants 
            FROM (
                SELECT COUNT(*) as participant_count 
                FROM mission_participants 
                GROUP BY mission_id
            )
        ");
        $stmt->execute();
        $col = $stmt->fetchColumn();
        if (!empty($col)) {
            $stats['avg_participants_per_mission'] = round($col, 1);
        } else {
            $stats['avg_participants_per_mission'] = 0;
        }

        return $stats;
    }

    function get_financial_statistics()
    {
        $stats = [];

        $year_start = strtotime(date('Y-01-01'));
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount_cents), 0) as total 
            FROM contributions 
            WHERE paid_at >= :year_start
        ");
        $stmt->bindValue(":year_start", $year_start, PDO::PARAM_INT);
        $stmt->execute();
        $stats['contributions_this_year'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM donations");
        $stmt->execute();
        $stats['total_donations'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM subsidies");
        $stmt->execute();
        $stats['total_subsidies'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT adherents_id) FROM contributions");
        $stmt->execute();
        $stats['contributor_count'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT donor_id) FROM donations");
        $stmt->execute();
        $stats['donor_count'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT 
                strftime('%Y-%m', datetime(paid_at, 'unixepoch')) as month,
                SUM(amount_cents) as amount
            FROM contributions 
            WHERE paid_at >= :months_ago
            GROUP BY month 
            ORDER BY month DESC
            LIMIT 12
        ");
        $months_ago = strtotime('-12 months');
        $stmt->bindValue(":months_ago", $months_ago, PDO::PARAM_INT);
        $stmt->execute();
        $stats['monthly_contributions'] = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

        return $stats;
    }

    function get_participation_statistics()
    {
        $stats = [];

        $stmt = $this->db->prepare("
            SELECT a.first_name, a.last_name, COUNT(mp.id) as mission_count
            FROM adherents a
            JOIN mission_participants mp ON a.id = mp.adherent_id
            WHERE a.is_active = 1
            GROUP BY a.id
            ORDER BY mission_count DESC
            LIMIT 10
        ");
        $stmt->execute();
        $stats['most_active_adherents'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT COUNT(id) FROM adherents WHERE is_active = 1");
        $stmt->execute();
        $total_adherents = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT adherent_id) FROM mission_participants");
        $stmt->execute();
        $participants_count = $stmt->fetchColumn();

        $stats['participation_rate'] = $total_adherents > 0 ? round(($participants_count / $total_adherents) * 100, 1) : 0;

        return $stats;
    }
}
