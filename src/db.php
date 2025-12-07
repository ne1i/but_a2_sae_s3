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
        $p_hash = $user_stmt->fetch()["password_hash"];
        return password_verify($password, $p_hash);
    }

    function exists($value, $table_name, $attribute_name)
    {
        $sql = "SELECT 1 FROM {$table_name} WHERE {$attribute_name} = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":value" => $value,
        ]);
        $result = $stmt->fetch();
        return isset($result[0]);
    }

    function add_user($username, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $admin_stmt = $this->db->prepare("INSERT INTO users(username, password_hash) VALUES(:username, :password_hash)");
        $admin_stmt->execute([
            ":username" => $username,
            ":password_hash" => $hash
        ]);
    }
}
