<?php

namespace ButA2SaeS3\repositories;

use ButA2SaeS3\dto\AddSubsidyDto;
use PDO;

class SubsidyRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function add(AddSubsidyDto $dto): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO subsidies (partner_id, title, amount_cents, awarded_at, conditions, notes)
             VALUES (:partner_id, :title, :amount_cents, :awarded_at, :conditions, :notes)"
        );
        return $stmt->execute([
            ":partner_id" => $dto->partnerId,
            ":title" => $dto->title,
            ":amount_cents" => $dto->amountCents,
            ":awarded_at" => $dto->awardedAt,
            ":conditions" => $dto->conditions,
            ":notes" => $dto->notes
        ]);
    }

    public function list(int $limit, int $page, string $filterPartner = ""): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT s.*, p.name as partner_name
            FROM subsidies s
            LEFT JOIN partners p ON s.partner_id = p.id
            WHERE 1=1
        ";
        $params = [];

        if ($filterPartner !== '') {
            $sql .= " AND LOWER(p.name) LIKE LOWER(:filter_partner)";
            $params[":filter_partner"] = "%$filterPartner%";
        }

        $sql .= " ORDER BY s.id DESC LIMIT :limit OFFSET :offset";
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

