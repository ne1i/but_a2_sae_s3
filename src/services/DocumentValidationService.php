<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\UploadDocumentDto;
use ButA2SaeS3\validation\ValidationResult;

class DocumentValidationService
{
    public static function validateUpload(array $data): ValidationResult
    {
        $result = ValidationResult::empty();
        $description = trim($data['description'] ?? '');
        $result->setValue(new UploadDocumentDto($description));
        return $result;
    }
}
