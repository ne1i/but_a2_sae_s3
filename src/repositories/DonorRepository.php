<?php

namespace ButA2SaeS3\repositories;

use ButA2SaeS3\dto\AddDonorDto;
use PDO;

class DonorRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function add(AddDonorDto $dto): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO donors (name, contact, email, notes) VALUES (:name, :contact, :email, :notes)"
        );
        return $stmt->execute([
            ":name" => $dto->name,
            ":contact" => $dto->contact,
            ":email" => $dto->email,
            ":notes" => $dto->notes
        ]);
    }

    public function list(int $limit, int $page, string $filterName = ""): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT d.*,
                   COALESCE(SUM(do.amount_cents), 0) as total_donated,
                   COUNT(do.id) as donation_count
            FROM donors d
            LEFT JOIN donations do ON do.donor_id = d.id
            WHERE 1=1
        ";
        $params = [];

        if ($filterName !== '') {
            $sql .= " AND LOWER(d.name) LIKE LOWER(:filter_name)";
            $params[":filter_name"] = "%$filterName%";
        }

        $sql .= " GROUP BY d.id ORDER BY d.added_at DESC LIMIT :limit OFFSET :offset";
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
}

