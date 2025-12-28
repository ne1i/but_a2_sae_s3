<?php

namespace ButA2SaeS3\repositories;

use PDO;

class ArticleMediaRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function attach(int $articleId, int $documentId): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO article_media (article_id, document_id) VALUES (:article_id, :document_id)"
        );
        return $stmt->execute([
            ":article_id" => $articleId,
            ":document_id" => $documentId
        ]);
    }

    public function detach(int $articleId, int $documentId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM article_media WHERE article_id = :article_id AND document_id = :document_id"
        );
        return $stmt->execute([
            ":article_id" => $articleId,
            ":document_id" => $documentId
        ]);
    }

    public function listForArticle(int $articleId): array
    {
        $stmt = $this->db->prepare(
            "SELECT d.* 
             FROM documents d 
             INNER JOIN article_media am ON am.document_id = d.id 
             WHERE am.article_id = :article_id
             ORDER BY d.uploaded_at DESC"
        );
        $stmt->execute([":article_id" => $articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
