<?php

namespace ButA2SaeS3\repositories;

use ButA2SaeS3\dto\AddArticleDto;
use PDO;

class ArticleRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function add(AddArticleDto $dto, int $authorId): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO articles (title, content, author_id, status, published_at, updated_at) 
             VALUES (:title, :content, :author_id, :status, :published_at, :updated_at)"
        );
        $current_time = time() + 3600;

        return $stmt->execute([
            ":title" => $dto->title,
            ":content" => $dto->content,
            ":author_id" => $authorId,
            ":status" => $dto->status,
            ":published_at" => $dto->status === 'published' ? $current_time : null,
            ":updated_at" => $current_time
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.username as author_username 
             FROM articles a 
             LEFT JOIN users u ON a.author_id = u.id 
             WHERE a.id = :id"
        );
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function list(int $limit, int $page, string $filterTitle = "", string $filterStatus = ""): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT a.*, u.username as author_username 
            FROM articles a 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filterTitle)) {
            $sql .= " AND LOWER(a.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filterTitle%";
        }

        if (!empty($filterStatus)) {
            $sql .= " AND a.status = :filter_status";
            $params[":filter_status"] = $filterStatus;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
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

    public function count(string $filterTitle = "", string $filterStatus = ""): int
    {
        $sql = "SELECT COUNT(a.id) FROM articles a WHERE 1=1";
        $params = [];

        if (!empty($filterTitle)) {
            $sql .= " AND LOWER(a.title) LIKE LOWER(:filter_title)";
            $params[":filter_title"] = "%$filterTitle%";
        }

        if (!empty($filterStatus)) {
            $sql .= " AND a.status = :filter_status";
            $params[":filter_status"] = $filterStatus;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function publish(int $id): bool
    {
        $current_time = time() + 3600;
        $stmt = $this->db->prepare(
            "UPDATE articles SET status = 'published', published_at = :published_at WHERE id = :id"
        );
        return $stmt->execute([
            ":id" => $id,
            ":published_at" => $current_time
        ]);
    }

    public function update(\ButA2SaeS3\dto\UpdateArticleDto $dto): bool
    {
        $current_time = time() + 3600;
        $sql = "UPDATE articles SET title = :title, content = :content, updated_at = :updated_time, status = :status";
        $params = [
            ":id" => $dto->id,
            ":title" => $dto->title,
            ":content" => $dto->content,
            ":updated_time" => $current_time,
            ":status" => $dto->status,
        ];

        if ($dto->status === 'published') {
            $sql .= ", published_at = :published_at";
            $params[":published_at"] = $current_time;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
