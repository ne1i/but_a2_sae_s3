<?php

namespace ButA2SaeS3\repositories;

use PDO;

class DocumentRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function upload(string $filename, string $originalName, string $mimeType, int $sizeBytes, int $uploaderId, string $path, ?string $description = null): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO documents (filename, original_name, mime_type, size_bytes, uploader_id, path, description) 
             VALUES (:filename, :original_name, :mime_type, :size_bytes, :uploader_id, :path, :description)"
        );
        return $stmt->execute([
            ":filename" => $filename,
            ":original_name" => $originalName,
            ":mime_type" => $mimeType,
            ":size_bytes" => $sizeBytes,
            ":uploader_id" => $uploaderId,
            ":path" => $path,
            ":description" => $description
        ]);
    }

    public function list(int $limit, int $page, string $filterFilename = ""): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT d.*, u.username as uploader_username 
            FROM documents d 
            LEFT JOIN users u ON d.uploader_id = u.id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filterFilename)) {
            $sql .= " AND (LOWER(d.filename) LIKE LOWER(:filter_filename) OR LOWER(d.original_name) LIKE LOWER(:filter_filename))";
            $params[":filter_filename"] = "%$filterFilename%";
        }

        $sql .= " ORDER BY d.uploaded_at DESC LIMIT :limit OFFSET :offset";
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

    public function count(string $filterFilename = ""): int
    {
        $sql = "SELECT COUNT(id) FROM documents WHERE 1=1";
        $params = [];

        if (!empty($filterFilename)) {
            $sql .= " AND (LOWER(filename) LIKE LOWER(:filter_filename) OR LOWER(original_name) LIKE LOWER(:filter_filename))";
            $params[":filter_filename"] = "%$filterFilename%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, u.username as uploader_username 
             FROM documents d 
             LEFT JOIN users u ON d.uploader_id = u.id 
             WHERE d.id = :id"
        );
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $doc = $this->findById($id);
        if ($doc && isset($doc['path']) && file_exists($doc['path'])) {
            @unlink($doc['path']);
        }

        $stmt = $this->db->prepare("DELETE FROM documents WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function lastInsertId(): int
    {
        return (int)$this->db->lastInsertId();
    }
}
