<?php
class FageDB
{
    private $db;
    private $db_path = __DIR__ . "/../data/fage.sqlite";

    function __construct()
    {
        if (file_exists($this->db_path)) {
            $this->db = new PDO("sqlite:" . $this->db_path);
        } else {
            $this->db = new PDO("sqlite:" . $this->db_path);
            $this->init_db();
        }
    }

    function init_db()
    {
        $init_script = file_get_contents(__DIR__ . "/../data/init_script.sql");
        $this->db->exec($init_script);
        $hash = password_hash("admin", PASSWORD_DEFAULT);
        $admin_stmt = $this->db->prepare("INSERT INTO users(username, password_hash) VALUES(:username, :password_hash)");
        $admin_stmt->execute([
            ":username" => "admin",
            ":password_hash" => $hash
        ]);
    }

    function check_login_creds($username, $password)
    {
        $user_stmt = $this->db->prepare("SELECT password_hash FROM users WHERE username = :username");
        $user_stmt->execute([":username" => $username]);
        $p_hash = $user_stmt->fetchColumn();
        return password_verify($password, $p_hash);
    }

    function exists($value, $table_name, $attribute_name)
    {
        $sql = "SELECT 1 FROM {$table_name} WHERE {$attribute_name} = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":value" => $value,
        ]);
        $result = $stmt->fetchColumn();
        return $result !== false;
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

    function is_correct_session_id($session_id)
    {
        $stmt = $this->db->prepare("SELECT session_id FROM sessions WHERE session_id = :session_id AND expires_at > CURRENT_TIMESTAMP");
        $stmt->execute([
            ":session_id" => $session_id,
        ]);
        return $stmt->fetchColumn() !== false;
    }
}
