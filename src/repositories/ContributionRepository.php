<?php

namespace ButA2SaeS3\repositories;

use PDO;

class ContributionRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function add(int $adherentId, int $amountCents, string $method, ?string $reference = null, ?string $notes = null): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO contributions (adherents_id, amount_cents, method, reference, notes) 
             VALUES (:adherents_id, :amount_cents, :method, :reference, :notes)"
        );
        return $stmt->execute([
            ":adherents_id" => $adherentId,
            ":amount_cents" => $amountCents,
            ":method" => $method,
            ":reference" => $reference,
            ":notes" => $notes
        ]);
    }

    public function list(int $limit, int $page, string $filterAdherent = "", string $filterMethod = ""): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT c.*, a.first_name, a.last_name 
            FROM contributions c 
            JOIN adherents a ON c.adherents_id = a.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filterAdherent)) {
            $sql .= " AND (LOWER(a.first_name) LIKE LOWER(:filter_adherent) OR LOWER(a.last_name) LIKE LOWER(:filter_adherent))";
            $params[":filter_adherent"] = "%$filterAdherent%";
        }

        if (!empty($filterMethod)) {
            $sql .= " AND LOWER(c.method) = LOWER(:filter_method)";
            $params[":filter_method"] = $filterMethod;
        }

        $sql .= " ORDER BY c.paid_at DESC LIMIT :limit OFFSET :offset";
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

    public function count(string $filterAdherent = "", string $filterMethod = ""): int
    {
        $sql = "
            SELECT COUNT(c.id) 
            FROM contributions c 
            JOIN adherents a ON c.adherents_id = a.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filterAdherent)) {
            $sql .= " AND (LOWER(a.first_name) LIKE LOWER(:filter_adherent) OR LOWER(a.last_name) LIKE LOWER(:filter_adherent))";
            $params[":filter_adherent"] = "%$filterAdherent%";
        }

        if (!empty($filterMethod)) {
            $sql .= " AND LOWER(c.method) = LOWER(:filter_method)";
            $params[":filter_method"] = $filterMethod;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function expiring(int $daysAhead = 30): array
    {
        $target_date = date('Y-m-d', strtotime("+{$daysAhead} days"));
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
}
