<?php

namespace ButA2SaeS3;

use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\dto\AdherentDto;
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

        if (file_exists($this->db_path)) {
            $this->db = new PDO("sqlite:" . $this->db_path);
        } else {
            $this->db = new PDO("sqlite:" . $this->db_path);
            $this->init_db();
        }
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

    function add_adherent(AddAdherentDto $new_adherent)
    {
        $stmt = $this->db->prepare("INSERT INTO adherents(first_name, last_name, address, postal_code, city, phone, email, age, profession)
        VALUES(:prenom, :nom, :adresse, :code_postal, :ville, :tel, :email, :age, :profession)");

        $stmt->execute([
            'prenom' => $new_adherent->prenom,
            'nom' => $new_adherent->nom,
            'adresse' => $new_adherent->adresse,
            'code_postal' => $new_adherent->code_postal,
            'ville' => $new_adherent->ville,
            'tel' => $new_adherent->tel,
            'email' => $new_adherent->email,
            'age' => $new_adherent->age,
            'profession' => $new_adherent->profession
        ]);
    }
    function adherent_exists($prenom, $nom, $email)
    {
        $stmt = $this->db->prepare("SELECT 1 FROM adherents WHERE first_name = :prenom AND last_name = :nom AND email = :email");

        $stmt->execute([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
        ]);
        $result = $stmt->fetchColumn();
        return $result !== false;
    }

    function get_adherents($count = 50, $page = 1, $filter_ville = "", $filter_age = "", $filter_profession = "")
    {
        $page = max(1, $page);
        $count = max(1, $count);

        $sql = "SELECT * FROM adherents WHERE 1=1";
        $params = [];

        if (!empty($filter_ville)) {
            $sql .= " AND LOWER(city) = LOWER(:filter_ville)";
            $params[":filter_ville"] = $filter_ville;
        }

        if (!empty($filter_age)) {
            $sql .= " AND age >= :filter_age";
            $params[":filter_age"] = $filter_age;
        }

        if (!empty($filter_profession)) {
            $sql .= " AND profession LIKE :filter_profession";
            $params[":filter_profession"] = "%$filter_profession%";
        }

        $sql .= " LIMIT :count OFFSET :offset";
        $params[":count"] = $count;
        $params[":offset"] = $count * ($page - 1);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        $adherentsDTOs = [];
        foreach ($result as $rows) {
            $adherent = new AdherentDto(
                $rows["id"],
                $rows["first_name"],
                $rows["last_name"],
                $rows["address"],
                $rows["postal_code"],
                $rows["city"],
                $rows["phone"],
                $rows["email"],
                $rows["age"],
                $rows["profession"]
            );
            $adherentsDTOs[] = $adherent;
        }
        return $adherentsDTOs;
    }

    function adherents_count()
    {
        return $this->db->query("SELECT COUNT(id) FROM adherents")->fetchColumn();
    }

    function get_distinct_cities()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT city FROM adherents ORDER BY city");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function get_distinct_professions()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT profession FROM adherents WHERE profession IS NOT NULL AND profession != '' ORDER BY profession");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function get_adherent_by_id($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM adherents WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new AdherentDto(
            $row["id"],
            $row["first_name"],
            $row["last_name"],
            $row["address"],
            $row["postal_code"],
            $row["city"],
            $row["phone"],
            $row["email"],
            $row["age"],
            $row["profession"]
        );
    }

    function update_adherent($id, AddAdherentDto $updated_adherent)
    {
        $stmt = $this->db->prepare("UPDATE adherents SET 
            first_name = :prenom, 
            last_name = :nom, 
            address = :adresse, 
            postal_code = :code_postal, 
            city = :ville, 
            phone = :tel, 
            email = :email, 
            age = :age, 
            profession = :profession 
            WHERE id = :id");

        return $stmt->execute([
            ':prenom' => $updated_adherent->prenom,
            ':nom' => $updated_adherent->nom,
            ':adresse' => $updated_adherent->adresse,
            ':code_postal' => $updated_adherent->code_postal,
            ':ville' => $updated_adherent->ville,
            ':tel' => $updated_adherent->tel,
            ':email' => $updated_adherent->email,
            ':age' => $updated_adherent->age,
            ':profession' => $updated_adherent->profession,
            ':id' => $id
        ]);
    }

    function get_adherent_by_email($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM adherents WHERE email = :email");
        $stmt->execute([":email" => $email]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new AdherentDto(
            $row["id"],
            $row["first_name"],
            $row["last_name"],
            $row["address"],
            $row["postal_code"],
            $row["city"],
            $row["phone"],
            $row["email"],
            $row["age"],
            $row["profession"]
        );
    }

    function get_adherents_count($filter_ville = "", $filter_age = "", $filter_profession = "")
    {
        $sql = "SELECT COUNT(id) FROM adherents WHERE 1=1";
        $params = [];

        if (!empty($filter_ville)) {
            $sql .= " AND LOWER(city) = LOWER(:filter_ville)";
            $params[":filter_ville"] = $filter_ville;
        }

        if (!empty($filter_age)) {
            $sql .= " AND age >= :filter_age";
            $params[":filter_age"] = $filter_age;
        }

        if (!empty($filter_profession)) {
            $sql .= " AND profession LIKE :filter_profession";
            $params[":filter_profession"] = "%$filter_profession%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function delete_adherent($id)
    {
        $stmt = $this->db->prepare("DELETE FROM adherents WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }
}
