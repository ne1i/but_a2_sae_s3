<?php

namespace ButA2SaeS3\repositories;

use ButA2SaeS3\dto\AddPartnerDto;
use PDO;

class PartnerRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function add(AddPartnerDto $dto): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO partners (name, contact, email, phone, address, website, notes) 
             VALUES (:name, :contact, :email, :phone, :address, :website, :notes)"
        );
        return $stmt->execute([
            ":name" => $dto->name,
            ":contact" => $dto->contact,
            ":email" => $dto->email,
            ":phone" => $dto->phone,
            ":address" => $dto->address,
            ":website" => $dto->website,
            ":notes" => $dto->notes
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM partners WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM partners WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function list(int $limit, int $page, string $filterName = ""): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM partners WHERE 1=1";
        $params = [];

        if ($filterName !== '') {
            $sql .= " AND LOWER(name) LIKE LOWER(:filter_name)";
            $params[":filter_name"] = "%$filterName%";
        }

        $sql .= " ORDER BY added_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = in_array($key, [':limit', ':offset']) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAll(): array
    {
        $stmt = $this->db->query("SELECT id, name FROM partners ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

