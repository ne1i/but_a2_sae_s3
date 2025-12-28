<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\AddArticleDto;
use ButA2SaeS3\validation\ValidationResult;

class ArticleValidationService
{
    public static function validateAddArticle(array $data): ValidationResult
    {
        $result = ValidationResult::empty();

        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');
        $status = $data['status'] ?? 'draft';

        if ($title === '') {
            $result->addMessage('title', 'Le titre est requis');
        }

        if ($content === '') {
            $result->addMessage('content', 'Le contenu est requis');
        }

        if ($status !== 'draft' && $status !== 'published') {
            $result->addMessage('status', 'Statut invalide');
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $result->setValue(new AddArticleDto($title, $content, $status));
        return $result;
    }

    public static function validateUpdateArticle(array $data, int $id): ValidationResult
    {
        $result = self::validateAddArticle($data);
        if ($result->hasMessages()) {
            return $result;
        }

        /** @var AddArticleDto $addDto */
        $addDto = $result->value();
        $result->setValue(new \ButA2SaeS3\dto\UpdateArticleDto($id, $addDto->title, $addDto->content, $addDto->status));
        return $result;
    }
}
