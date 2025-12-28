<?php

namespace ButA2SaeS3\repositories;

use ButA2SaeS3\entities\Adherent;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\dto\AddAdherentDto;

class AdherentRepository
{
    private \PDO $pdo;

    public function __construct(private FageDB $db)
    {
        $this->pdo = $db->getConnection();
    }

    public function findAll(int $limit, int $page, array $filters = []): array
    {
        $ville = $filters['ville'] ?? '';
        $age = $filters['age'] ?? '';
        $profession = $filters['profession'] ?? '';

        $page = max(1, $page);
        $limit = max(1, $limit);

        $sql = "SELECT * FROM adherents WHERE 1=1";
        $params = [];

        if (!empty($ville)) {
            $sql .= " AND LOWER(city) = LOWER(:filter_ville)";
            $params[":filter_ville"] = $ville;
        }

        if (!empty($age)) {
            $sql .= " AND age >= :filter_age";
            $params[":filter_age"] = $age;
        }

        if (!empty($profession)) {
            $sql .= " AND profession LIKE :filter_profession";
            $params[":filter_profession"] = "%$profession%";
        }

        $sql .= " LIMIT :count OFFSET :offset";
        $params[":count"] = $limit;
        $params[":offset"] = $limit * ($page - 1);

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();
        $result = $stmt->fetchAll();

        return array_map(fn($row) => new Adherent(
            (int)$row["id"],
            $row["first_name"],
            $row["last_name"],
            $row["address"],
            $row["postal_code"],
            $row["city"],
            $row["phone"],
            $row["email"],
            $row["age"],
            $row["profession"]
        ), $result);
    }

    public function findById(int $id): ?Adherent
    {
        $stmt = $this->pdo->prepare("SELECT * FROM adherents WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Adherent(
            (int)$row["id"],
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

    public function exists(string $prenom, string $nom, string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM adherents WHERE first_name = :prenom AND last_name = :nom AND email = :email");

        $stmt->execute([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
        ]);
        $result = $stmt->fetchColumn();
        return $result !== false;
    }

    public function save(AddAdherentDto $dto): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO adherents(first_name, last_name, address, postal_code, city, phone, email, age, profession)
        VALUES(:prenom, :nom, :adresse, :code_postal, :ville, :tel, :email, :age, :profession)");

        return $stmt->execute([
            'prenom' => $dto->prenom,
            'nom' => $dto->nom,
            'adresse' => $dto->adresse,
            'code_postal' => $dto->code_postal,
            'ville' => $dto->ville,
            'tel' => $dto->tel,
            'email' => $dto->email,
            'age' => $dto->age,
            'profession' => $dto->profession
        ]);
    }

    public function update(\ButA2SaeS3\dto\UpdateAdherentDto $dto): bool
    {
        $stmt = $this->pdo->prepare("UPDATE adherents SET 
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
            ':prenom' => $dto->prenom,
            ':nom' => $dto->nom,
            ':adresse' => $dto->adresse,
            ':code_postal' => $dto->code_postal,
            ':ville' => $dto->ville,
            ':tel' => $dto->tel,
            ':email' => $dto->email,
            ':age' => $dto->age,
            ':profession' => $dto->profession,
            ':id' => $dto->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM adherents WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function count(array $filters = []): int
    {
        $ville = $filters['ville'] ?? '';
        $age = $filters['age'] ?? '';
        $profession = $filters['profession'] ?? '';

        $sql = "SELECT COUNT(id) FROM adherents WHERE 1=1";
        $params = [];

        if (!empty($ville)) {
            $sql .= " AND LOWER(city) = LOWER(:filter_ville)";
            $params[":filter_ville"] = $ville;
        }

        if (!empty($age)) {
            $sql .= " AND age >= :filter_age";
            $params[":filter_age"] = $age;
        }

        if (!empty($profession)) {
            $sql .= " AND profession LIKE :filter_profession";
            $params[":filter_profession"] = "%$profession%";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getDistinctCities(): array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT city FROM adherents ORDER BY city");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getDistinctProfessions(): array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT profession FROM adherents WHERE profession IS NOT NULL AND profession != '' ORDER BY profession");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function findByEmail(string $email): ?Adherent
    {
        $stmt = $this->pdo->prepare("SELECT * FROM adherents WHERE email = :email");
        $stmt->execute([":email" => $email]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return new Adherent(
            (int)$row["id"],
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
}
