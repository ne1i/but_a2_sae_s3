<?php

namespace ButA2SaeS3\repositories;

use ButA2SaeS3\dto\AddMissionDto;
use ButA2SaeS3\dto\UpdateMissionDto;
use PDO;

class MissionRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function add(AddMissionDto $mission, ?int $createdBy): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO missions (title, description, location, start_at, end_at, capacity, budget_cents, created_by) 
             VALUES (:title, :description, :location, :start_at, :end_at, :capacity, :budget_cents, :created_by)"
        );

        return $stmt->execute([
            ":title" => $mission->title,
            ":description" => $mission->description,
            ":location" => $mission->location,
            ":start_at" => $mission->start_at,
            ":end_at" => $mission->end_at,
            ":capacity" => $mission->capacity,
            ":budget_cents" => $mission->budget_cents,
            ":created_by" => $createdBy
        ]);
    }

    public function update(UpdateMissionDto $mission): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE missions 
             SET title = :title, description = :description, location = :location, 
                 start_at = :start_at, end_at = :end_at, capacity = :capacity, budget_cents = :budget_cents 
             WHERE id = :id"
        );

        return $stmt->execute([
            ":id" => $mission->id,
            ":title" => $mission->title,
            ":description" => $mission->description,
            ":location" => $mission->location,
            ":start_at" => $mission->start_at,
            ":end_at" => $mission->end_at,
            ":capacity" => $mission->capacity,
            ":budget_cents" => $mission->budget_cents
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM missions WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.username as created_by_username 
             FROM missions m 
             LEFT JOIN users u ON m.created_by = u.id 
             WHERE m.id = :id"
        );
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findAll(int $limit, int $page, string $filterTitle = '', string $filterLocation = ''): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT m.*, u.username as created_by_username 
            FROM missions m 
            LEFT JOIN users u ON m.created_by = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filterTitle)) {
            $sql .= " AND LOWER(m.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filterTitle%";
        }

        if (!empty($filterLocation)) {
            $sql .= " AND LOWER(m.location) LIKE LOWER(:filter_location)";
            $params[":filter_location"] = "%$filterLocation%";
        }

        $sql .= " ORDER BY m.start_at DESC LIMIT :limit OFFSET :offset";
        $params[":limit"] = $limit;
        $params[":offset"] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(string $filterTitle = '', string $filterLocation = ''): int
    {
        $sql = "SELECT COUNT(m.id) FROM missions m WHERE 1=1";
        $params = [];

        if (!empty($filterTitle)) {
            $sql .= " AND LOWER(m.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filterTitle%";
        }

        if (!empty($filterLocation)) {
            $sql .= " AND LOWER(m.location) LIKE LOWER(:filter_location)";
            $params[":filter_location"] = "%$filterLocation%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function addParticipant(int $missionId, int $adherentId, string $role): bool
    {
        $stmt = $this->db->prepare(
            "INSERT OR IGNORE INTO mission_participants (mission_id, adherent_id, role) 
             VALUES (:mission_id, :adherent_id, :role)"
        );
        return $stmt->execute([
            ":mission_id" => $missionId,
            ":adherent_id" => $adherentId,
            ":role" => $role
        ]);
    }

    public function removeParticipant(int $missionId, int $adherentId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM mission_participants WHERE mission_id = :mission_id AND adherent_id = :adherent_id"
        );
        return $stmt->execute([
            ":mission_id" => $missionId,
            ":adherent_id" => $adherentId
        ]);
    }

    public function participants(int $missionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT mp.*, a.first_name, a.last_name, a.email, a.phone 
             FROM mission_participants mp 
             JOIN adherents a ON mp.adherent_id = a.id 
             WHERE mp.mission_id = :mission_id 
             ORDER BY mp.registered_at"
        );
        $stmt->execute([":mission_id" => $missionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
