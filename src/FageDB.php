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

    function get_last_insert_id()
    {
        return $this->db->lastInsertId();
    }

    function get_all_roles()
    {
        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    function get_user_id_from_session($session_id)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM sessions WHERE session_id = :session_id AND expires_at > CURRENT_TIMESTAMP");
        $stmt->execute([
            ":session_id" => $session_id,
        ]);
        $user_id = $stmt->fetchColumn();
        return $user_id ? (int)$user_id : null;
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

    // Mission Management Methods
    function add_mission($title, $description, $location, $start_at, $end_at, $capacity, $budget_cents, $created_by)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO missions (title, description, location, start_at, end_at, capacity, budget_cents, created_by) 
             VALUES (:title, :description, :location, :start_at, :end_at, :capacity, :budget_cents, :created_by)"
        );
        return $stmt->execute([
            ":title" => $title,
            ":description" => $description,
            ":location" => $location,
            ":start_at" => $start_at,
            ":end_at" => $end_at,
            ":capacity" => $capacity,
            ":budget_cents" => $budget_cents,
            ":created_by" => $created_by
        ]);
    }

    function get_missions($limit = 20, $page = 1, $filter_title = "", $filter_location = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT m.*, u.username as created_by_username 
            FROM missions m 
            LEFT JOIN users u ON m.created_by = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_title)) {
            $sql .= " AND LOWER(m.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filter_title%";
        }

        if (!empty($filter_location)) {
            $sql .= " AND LOWER(m.location) LIKE LOWER(:filter_location)";
            $params[":filter_location"] = "%$filter_location%";
        }

        $sql .= " ORDER BY m.start_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_missions_count($filter_title = "", $filter_location = "")
    {
        $sql = "SELECT COUNT(m.id) FROM missions m WHERE 1=1";
        $params = [];

        if (!empty($filter_title)) {
            $sql .= " AND LOWER(m.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filter_title%";
        }

        if (!empty($filter_location)) {
            $sql .= " AND LOWER(m.location) LIKE LOWER(:filter_location)";
            $params[":filter_location"] = "%$filter_location%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function get_mission_by_id($id)
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.username as created_by_username 
             FROM missions m 
             LEFT JOIN users u ON m.created_by = u.id 
             WHERE m.id = :id"
        );
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update_mission($id, $title, $description, $location, $start_at, $end_at, $capacity, $budget_cents)
    {
        $stmt = $this->db->prepare(
            "UPDATE missions 
             SET title = :title, description = :description, location = :location, 
                 start_at = :start_at, end_at = :end_at, capacity = :capacity, budget_cents = :budget_cents 
             WHERE id = :id"
        );
        return $stmt->execute([
            ":id" => $id,
            ":title" => $title,
            ":description" => $description,
            ":location" => $location,
            ":start_at" => $start_at,
            ":end_at" => $end_at,
            ":capacity" => $capacity,
            ":budget_cents" => $budget_cents
        ]);
    }

    function delete_mission($id)
    {
        $stmt = $this->db->prepare("DELETE FROM missions WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    function add_mission_participant($mission_id, $adherent_id, $role)
    {
        $stmt = $this->db->prepare(
            "INSERT OR IGNORE INTO mission_participants (mission_id, adherent_id, role) 
             VALUES (:mission_id, :adherent_id, :role)"
        );
        return $stmt->execute([
            ":mission_id" => $mission_id,
            ":adherent_id" => $adherent_id,
            ":role" => $role
        ]);
    }

    function remove_mission_participant($mission_id, $adherent_id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM mission_participants WHERE mission_id = :mission_id AND adherent_id = :adherent_id"
        );
        return $stmt->execute([
            ":mission_id" => $mission_id,
            ":adherent_id" => $adherent_id
        ]);
    }

    function get_mission_participants($mission_id)
    {
        $stmt = $this->db->prepare(
            "SELECT mp.*, a.first_name, a.last_name, a.email, a.phone 
             FROM mission_participants mp 
             JOIN adherents a ON mp.adherent_id = a.id 
             WHERE mp.mission_id = :mission_id 
             ORDER BY mp.registered_at"
        );
        $stmt->execute([":mission_id" => $mission_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_upcoming_missions($limit = 10)
    {
        $current_time = time();
        $stmt = $this->db->prepare(
            "SELECT m.*, u.username as created_by_username 
             FROM missions m 
             LEFT JOIN users u ON m.created_by = u.id 
             WHERE m.start_at > :current_time 
             ORDER BY m.start_at ASC 
             LIMIT :limit"
        );
        $stmt->bindValue(":current_time", $current_time, PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Financial Management Methods
    
    // Contributions (cotisations)
    function add_contribution($adherents_id, $amount_cents, $method, $reference = null, $notes = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO contributions (adherents_id, amount_cents, method, reference, notes) 
             VALUES (:adherents_id, :amount_cents, :method, :reference, :notes)"
        );
        return $stmt->execute([
            ":adherents_id" => $adherents_id,
            ":amount_cents" => $amount_cents,
            ":method" => $method,
            ":reference" => $reference,
            ":notes" => $notes
        ]);
    }

    function get_contributions($limit = 20, $page = 1, $filter_adherent = "", $filter_method = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT c.*, a.first_name, a.last_name 
            FROM contributions c 
            JOIN adherents a ON c.adherents_id = a.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_adherent)) {
            $sql .= " AND (LOWER(a.first_name) LIKE LOWER(:filter_adherent) OR LOWER(a.last_name) LIKE LOWER(:filter_adherent))";
            $params[":filter_adherent"] = "%$filter_adherent%";
        }

        if (!empty($filter_method)) {
            $sql .= " AND LOWER(c.method) = LOWER(:filter_method)";
            $params[":filter_method"] = $filter_method;
        }

        $sql .= " ORDER BY c.paid_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_contributions_count($filter_adherent = "", $filter_method = "")
    {
        $sql = "
            SELECT COUNT(c.id) 
            FROM contributions c 
            JOIN adherents a ON c.adherents_id = a.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_adherent)) {
            $sql .= " AND (LOWER(a.first_name) LIKE LOWER(:filter_adherent) OR LOWER(a.last_name) LIKE LOWER(:filter_adherent))";
            $params[":filter_adherent"] = "%$filter_adherent%";
        }

        if (!empty($filter_method)) {
            $sql .= " AND LOWER(c.method) = LOWER(:filter_method)";
            $params[":filter_method"] = $filter_method;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function get_expiring_contributions($days_ahead = 30)
    {
        $target_date = date('Y-m-d', strtotime("+{$days_ahead} days"));
        $stmt = $this->db->prepare(
            "SELECT a.*, MAX(c.paid_at) as last_payment_date, COUNT(c.id) as total_payments
             FROM adherents a
             LEFT JOIN contributions c ON a.id = c.adherents_id
             WHERE a.is_active = 1
             GROUP BY a.id
             HAVING last_payment_date < :target_date OR last_payment_date IS NULL
             ORDER BY last_payment_date ASC"
        );
        $stmt->execute([":target_date" => $target_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Partners
    function add_partner($name, $contact = null, $email = null, $phone = null, $address = null, $website = null, $notes = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO partners (name, contact, email, phone, address, website, notes) 
             VALUES (:name, :contact, :email, :phone, :address, :website, :notes)"
        );
        return $stmt->execute([
            ":name" => $name,
            ":contact" => $contact,
            ":email" => $email,
            ":phone" => $phone,
            ":address" => $address,
            ":website" => $website,
            ":notes" => $notes
        ]);
    }

    function get_partners($limit = 20, $page = 1, $filter_name = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM partners WHERE 1=1";
        $params = [];

        if (!empty($filter_name)) {
            $sql .= " AND LOWER(name) LIKE LOWER(:filter_name)";
            $params[":filter_name"] = "%$filter_name%";
        }

        $sql .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_partners_count($filter_name = "")
    {
        $sql = "SELECT COUNT(id) FROM partners WHERE 1=1";
        $params = [];

        if (!empty($filter_name)) {
            $sql .= " AND LOWER(name) LIKE LOWER(:filter_name)";
            $params[":filter_name"] = "%$filter_name%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function get_partner_by_id($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM partners WHERE id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update_partner($id, $name, $contact = null, $email = null, $phone = null, $address = null, $website = null, $notes = null)
    {
        $stmt = $this->db->prepare(
            "UPDATE partners 
             SET name = :name, contact = :contact, email = :email, phone = :phone, 
                 address = :address, website = :website, notes = :notes 
             WHERE id = :id"
        );
        return $stmt->execute([
            ":id" => $id,
            ":name" => $name,
            ":contact" => $contact,
            ":email" => $email,
            ":phone" => $phone,
            ":address" => $address,
            ":website" => $website,
            ":notes" => $notes
        ]);
    }

    function delete_partner($id)
    {
        $stmt = $this->db->prepare("DELETE FROM partners WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    // Donors and Donations
    function add_donor($name, $contact = null, $email = null, $notes = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO donors (name, contact, email, notes) 
             VALUES (:name, :contact, :email, :notes)"
        );
        return $stmt->execute([
            ":name" => $name,
            ":contact" => $contact,
            ":email" => $email,
            ":notes" => $notes
        ]);
    }

    function get_donors($limit = 20, $page = 1, $filter_name = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT d.*, COUNT(dn.id) as donation_count, SUM(dn.amount_cents) as total_donated 
            FROM donors d 
            LEFT JOIN donations dn ON d.id = dn.donor_id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_name)) {
            $sql .= " AND LOWER(d.name) LIKE LOWER(:filter_name)";
            $params[":filter_name"] = "%$filter_name%";
        }

        $sql .= " 
            GROUP BY d.id 
            ORDER BY d.name ASC 
            LIMIT :limit OFFSET :offset
        ";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_donors_count($filter_name = "")
    {
        $sql = "SELECT COUNT(id) FROM donors WHERE 1=1";
        $params = [];

        if (!empty($filter_name)) {
            $sql .= " AND LOWER(name) LIKE LOWER(:filter_name)";
            $params[":filter_name"] = "%$filter_name%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function add_donation($donor_id, $amount_cents, $method = null, $reference = null, $notes = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO donations (donor_id, amount_cents, method, reference, notes) 
             VALUES (:donor_id, :amount_cents, :method, :reference, :notes)"
        );
        return $stmt->execute([
            ":donor_id" => $donor_id,
            ":amount_cents" => $amount_cents,
            ":method" => $method,
            ":reference" => $reference,
            ":notes" => $notes
        ]);
    }

    function get_donations($donor_id, $limit = 20, $page = 1)
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare(
            "SELECT * FROM donations WHERE donor_id = :donor_id ORDER BY donated_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(":donor_id", $donor_id, PDO::PARAM_INT);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Subsidies
    function add_subsidy($partner_id, $title, $amount_cents, $awarded_at = null, $conditions = null, $notes = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO subsidies (partner_id, title, amount_cents, awarded_at, conditions, notes) 
             VALUES (:partner_id, :title, :amount_cents, :awarded_at, :conditions, :notes)"
        );
        return $stmt->execute([
            ":partner_id" => $partner_id,
            ":title" => $title,
            ":amount_cents" => $amount_cents,
            ":awarded_at" => $awarded_at,
            ":conditions" => $conditions,
            ":notes" => $notes
        ]);
    }

    function get_subsidies($limit = 20, $page = 1, $filter_partner = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT s.*, p.name as partner_name 
            FROM subsidies s 
            LEFT JOIN partners p ON s.partner_id = p.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_partner)) {
            $sql .= " AND LOWER(p.name) LIKE LOWER(:filter_partner)";
            $params[":filter_partner"] = "%$filter_partner%";
        }

        $sql .= " ORDER BY s.awarded_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_subsidies_count($filter_partner = "")
    {
        $sql = "
            SELECT COUNT(s.id) 
            FROM subsidies s 
            LEFT JOIN partners p ON s.partner_id = p.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_partner)) {
            $sql .= " AND LOWER(p.name) LIKE LOWER(:filter_partner)";
            $params[":filter_partner"] = "%$filter_partner%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // Communication Module Methods
    
    // Articles Management
    function add_article($title, $content, $author_id, $status = 'draft')
    {
        $stmt = $this->db->prepare(
            "INSERT INTO articles (title, content, author_id, status, published_at, updated_at) 
             VALUES (:title, :content, :author_id, :status, :published_at, :updated_at)"
        );
        $current_time = time();
        return $stmt->execute([
            ":title" => $title,
            ":content" => $content,
            ":author_id" => $author_id,
            ":status" => $status,
            ":published_at" => $status === 'published' ? $current_time : null,
            ":updated_at" => $current_time
        ]);
    }

    function get_articles($limit = 20, $page = 1, $filter_title = "", $filter_status = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT a.*, u.username as author_username 
            FROM articles a 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_title)) {
            $sql .= " AND LOWER(a.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filter_title%";
        }

        if (!empty($filter_status)) {
            $sql .= " AND a.status = :filter_status";
            $params[":filter_status"] = $filter_status;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_articles_count($filter_title = "", $filter_status = "")
    {
        $sql = "SELECT COUNT(a.id) FROM articles a WHERE 1=1";
        $params = [];

        if (!empty($filter_title)) {
            $sql .= " AND LOWER(a.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filter_title%";
        }

        if (!empty($filter_status)) {
            $sql .= " AND a.status = :filter_status";
            $params[":filter_status"] = $filter_status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function get_article_by_id($id)
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.username as author_username 
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE a.id = :id"
        );
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update_article($id, $title, $content, $status = null)
    {
        $current_time = time();
        $sql = "UPDATE articles SET title = :title, content = :content, updated_at = :updated_time";
        $params = [
            ":id" => $id,
            ":title" => $title,
            ":content" => $content,
            ":updated_time" => $current_time
        ];

        if ($status !== null) {
            $sql .= ", status = :status";
            $params[":status"] = $status;
            if ($status === 'published') {
                $sql .= ", published_at = :published_at";
                $params[":published_at"] = $current_time;
            }
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    function delete_article($id)
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    function publish_article($id)
    {
        $current_time = time();
        $stmt = $this->db->prepare(
            "UPDATE articles SET status = 'published', published_at = :published_at WHERE id = :id"
        );
        return $stmt->execute([
            ":id" => $id,
            ":published_at" => $current_time
        ]);
    }

    // Documents Management
    function upload_document($filename, $original_name, $mime_type, $size_bytes, $uploader_id, $path, $description = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO documents (filename, original_name, mime_type, size_bytes, uploader_id, path, description) 
             VALUES (:filename, :original_name, :mime_type, :size_bytes, :uploader_id, :path, :description)"
        );
        return $stmt->execute([
            ":filename" => $filename,
            ":original_name" => $original_name,
            ":mime_type" => $mime_type,
            ":size_bytes" => $size_bytes,
            ":uploader_id" => $uploader_id,
            ":path" => $path,
            ":description" => $description
        ]);
    }

    function get_documents($limit = 50, $page = 1, $filter_filename = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT d.*, u.username as uploader_username 
            FROM documents d 
            LEFT JOIN users u ON d.uploader_id = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_filename)) {
            $sql .= " AND (LOWER(d.filename) LIKE LOWER(:filter_filename) OR LOWER(d.original_name) LIKE LOWER(:filter_filename))";
            $params[":filter_filename"] = "%$filter_filename%";
        }

        $sql .= " ORDER BY d.uploaded_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_documents_count($filter_filename = "")
    {
        $sql = "SELECT COUNT(id) FROM documents WHERE 1=1";
        $params = [];

        if (!empty($filter_filename)) {
            $sql .= " AND (LOWER(filename) LIKE LOWER(:filter_filename) OR LOWER(original_name) LIKE LOWER(:filter_filename))";
            $params[":filter_filename"] = "%$filter_filename%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    function get_document_by_id($id)
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, u.username as uploader_username 
             FROM documents d 
             LEFT JOIN users u ON d.uploader_id = u.id 
             WHERE d.id = :id"
        );
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function delete_document($id)
    {
        // First get the document to delete the file
        $doc = $this->get_document_by_id($id);
        if ($doc && file_exists($doc['path'])) {
            unlink($doc['path']);
        }
        
        $stmt = $this->db->prepare("DELETE FROM documents WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    // Media Attachment Methods
    function attach_media_to_article($article_id, $document_id)
    {
        $stmt = $this->db->prepare(
            "INSERT OR IGNORE INTO article_media (article_id, document_id) 
             VALUES (:article_id, :document_id)"
        );
        return $stmt->execute([
            ":article_id" => $article_id,
            ":document_id" => $document_id
        ]);
    }

    function detach_media_from_article($article_id, $document_id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM article_media WHERE article_id = :article_id AND document_id = :document_id"
        );
        return $stmt->execute([
            ":article_id" => $article_id,
            ":document_id" => $document_id
        ]);
    }

    function get_article_media($article_id)
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, u.username as uploader_username 
             FROM article_media am 
             JOIN documents d ON am.document_id = d.id 
             LEFT JOIN users u ON d.uploader_id = u.id 
             WHERE am.article_id = :article_id 
             ORDER BY d.uploaded_at DESC"
        );
        $stmt->execute([":article_id" => $article_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function attach_document_to_mission($mission_id, $document_id)
    {
        $stmt = $this->db->prepare(
            "INSERT OR IGNORE INTO mission_documents (mission_id, document_id) 
             VALUES (:mission_id, :document_id)"
        );
        return $stmt->execute([
            ":mission_id" => $mission_id,
            ":document_id" => $document_id
        ]);
    }

    function remove_document_from_mission($mission_id, $document_id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM mission_documents WHERE mission_id = :mission_id AND document_id = :document_id"
        );
        return $stmt->execute([
            ":mission_id" => $mission_id,
            ":document_id" => $document_id
        ]);
    }

    function get_mission_documents($mission_id)
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, u.username as uploader_username 
             FROM mission_documents md 
             JOIN documents d ON md.document_id = d.id 
             LEFT JOIN users u ON d.uploader_id = u.id 
             WHERE md.mission_id = :mission_id 
             ORDER BY d.uploaded_at DESC"
        );
        $stmt->execute([":mission_id" => $mission_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Statistics Dashboard Methods
    function get_adherent_statistics()
    {
        $stats = [];
        
        // Total adherents
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM adherents WHERE is_active = 1");
        $stmt->execute();
        $stats['total_active'] = $stmt->fetchColumn();
        
        // New adherents this year
        $year_start = strtotime(date('Y-01-01'));
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM adherents WHERE joined_at >= :year_start");
        $stmt->bindValue(":year_start", $year_start, PDO::PARAM_INT);
        $stmt->execute();
        $stats['new_this_year'] = $stmt->fetchColumn();
        
        // Age distribution
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
        
        // City distribution
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
        
        // Profession distribution
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
        
        // Total missions
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM missions");
        $stmt->execute();
        $stats['total_missions'] = $stmt->fetchColumn();
        
        // Missions this year
        $year_start = strtotime(date('Y-01-01'));
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM missions WHERE start_at >= :year_start");
        $stmt->bindValue(":year_start", $year_start, PDO::PARAM_INT);
        $stmt->execute();
        $stats['missions_this_year'] = $stmt->fetchColumn();
        
        // Upcoming missions
        $current_time = time();
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM missions WHERE start_at > :current_time");
        $stmt->bindValue(":current_time", $current_time, PDO::PARAM_INT);
        $stmt->execute();
        $stats['upcoming_missions'] = $stmt->fetchColumn();
        
        // Total participants
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT adherent_id) FROM mission_participants");
        $stmt->execute();
        $stats['total_participants'] = $stmt->fetchColumn();
        
        // Average participants per mission
        $stmt = $this->db->prepare("
            SELECT AVG(participant_count) as avg_participants 
            FROM (
                SELECT COUNT(*) as participant_count 
                FROM mission_participants 
                GROUP BY mission_id
            )
        ");
        $stmt->execute();
        $stats['avg_participants_per_mission'] = round($stmt->fetchColumn(), 1);
        
        return $stats;
    }

    function get_financial_statistics()
    {
        $stats = [];
        
        // Total contributions this year
        $year_start = strtotime(date('Y-01-01'));
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount_cents), 0) as total 
            FROM contributions 
            WHERE paid_at >= :year_start
        ");
        $stmt->bindValue(":year_start", $year_start, PDO::PARAM_INT);
        $stmt->execute();
        $stats['contributions_this_year'] = $stmt->fetchColumn();
        
        // Total donations
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM donations");
        $stmt->execute();
        $stats['total_donations'] = $stmt->fetchColumn();
        
        // Total subsidies
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM subsidies");
        $stmt->execute();
        $stats['total_subsidies'] = $stmt->fetchColumn();
        
        // Number of contributors
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT adherents_id) FROM contributions");
        $stmt->execute();
        $stats['contributor_count'] = $stmt->fetchColumn();
        
        // Number of donors
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT donor_id) FROM donations");
        $stmt->execute();
        $stats['donor_count'] = $stmt->fetchColumn();
        
        // Monthly contribution trend (last 12 months)
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
        
        // Most active adherents
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
        
        // Participation rate
        $stmt = $this->db->prepare("SELECT COUNT(id) FROM adherents WHERE is_active = 1");
        $stmt->execute();
        $total_adherents = $stmt->fetchColumn();
        
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT adherent_id) FROM mission_participants");
        $stmt->execute();
        $participants_count = $stmt->fetchColumn();
        
        $stats['participation_rate'] = $total_adherents > 0 ? round(($participants_count / $total_adherents) * 100, 1) : 0;
        
        return $stats;
    }

    // Enhanced Security Methods
    function get_users($limit = 50, $page = 1)
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("
            SELECT u.*, a.first_name, a.last_name, GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN adherents a ON u.adherent_id = a.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_user_by_id($id)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, a.first_name, a.last_name, GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN adherents a ON u.adherent_id = a.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = :id
            GROUP BY u.id
        ");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update_user_roles($user_id, $role_ids)
    {
        // Remove existing roles
        $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $user_id]);
        
        // Add new roles
        foreach ($role_ids as $role_id) {
            $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
            $stmt->execute([
                ":user_id" => $user_id,
                ":role_id" => $role_id
            ]);
        }
        
        return true;
    }

    function delete_user($id)
    {
        // Delete user roles first
        $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $id]);
        
        // Delete sessions
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $id]);
        
        // Delete user
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    function get_audit_logs($limit = 50, $page = 1, $filter_entity = "", $filter_action = "")
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT al.*, u.username 
            FROM audit_logs al 
            LEFT JOIN users u ON al.performed_by = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filter_entity)) {
            $sql .= " AND al.entity = :filter_entity";
            $params[":filter_entity"] = $filter_entity;
        }

        if (!empty($filter_action)) {
            $sql .= " AND al.action = :filter_action";
            $params[":filter_action"] = $filter_action;
        }

        $sql .= " ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            if (in_array($key, [':limit', ':offset'])) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function log_audit($entity, $entity_id, $action, $performed_by = null, $details = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (entity, entity_id, action, performed_by, details) 
            VALUES (:entity, :entity_id, :action, :performed_by, :details)
        ");
        return $stmt->execute([
            ":entity" => $entity,
            ":entity_id" => $entity_id,
            ":action" => $action,
            ":performed_by" => $performed_by,
            ":details" => $details
        ]);
    }

    function backup_database($backup_path = null)
    {
        if ($backup_path === null) {
            $backup_dir = __DIR__ . "/../backups/";
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0755, true);
            }
            $backup_path = $backup_dir . "backup_" . date('Y-m-d_H-i-s') . ".sql";
        }

        try {
            // Export database structure and data
            $tables = $this->db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
            
            $backup_content = "-- Database Backup - " . date('Y-m-d H:i:s') . "\n";
            $backup_content .= "-- Generated by FAGE Association Management System\n\n";
            
            foreach ($tables as $table) {
                // Get CREATE TABLE statement
                $create_stmt = $this->db->query("SELECT sql FROM sqlite_master WHERE name='{$table}'")->fetchColumn();
                $backup_content .= $create_stmt . ";\n\n";
                
                // Get table data
                $rows = $this->db->query("SELECT * FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $backup_content .= "-- Data for table {$table}\n";
                    foreach ($rows as $row) {
                        $columns = array_keys($row);
                        $values = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            if (is_numeric($value)) return $value;
                            return "'" . addslashes($value) . "'";
                        }, $row);
                        
                        $backup_content .= "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $backup_content .= "\n";
                }
            }
            
            // Write backup to file
            file_put_contents($backup_path, $backup_content);
            
            return [
                'success' => true,
                'path' => $backup_path,
                'size' => filesize($backup_path)
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Backup failed: ' . (isset($e) ? $e->getMessage() : 'Unknown error')
            ];
        }
    }

    function get_backup_files()
    {
        $backup_dir = __DIR__ . "/../backups/";
        if (!is_dir($backup_dir)) {
            return [];
        }
        
        $files = [];
        foreach (glob($backup_dir . "*.sql") as $file) {
            $files[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'created' => filemtime($file)
            ];
        }
        
        // Sort by creation date (newest first)
        usort($files, function($a, $b) {
            return $b['created'] - $a['created'];
        });
        
        return $files;
    }

    function has_permission($user_id, $permission)
    {
        $stmt = $this->db->prepare("
            SELECT r.permissions 
            FROM users u 
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id 
            WHERE u.id = :user_id AND r.permissions = 'all'
        ");
        $stmt->execute([":user_id" => $user_id]);
        
        if ($stmt->fetchColumn()) {
            return true; // Admin has all permissions
        }
        
        // Check specific permission (simplified for this implementation)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM users u 
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id 
            WHERE u.id = :user_id AND (r.permissions = :permission OR r.permissions = 'all')
        ");
        $stmt->execute([
            ":user_id" => $user_id,
            ":permission" => $permission
        ]);
        
        return $stmt->fetchColumn() > 0;
    }
}
