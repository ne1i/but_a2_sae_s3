<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\AddContributionDto;
use ButA2SaeS3\validation\ValidationResult;

class ContributionValidationService
{
    public static function validateAddContribution(array $data): ValidationResult
    {
        $result = ValidationResult::empty();

        $adherents_id = $data['adherents_id'] ?? null;
        $amount = $data['amount'] ?? null;
        $method = trim($data['method'] ?? '');
        $reference = trim($data['reference'] ?? '');
        $notes = trim($data['notes'] ?? '');

        if (empty($adherents_id)) {
            $result->addMessage('adherents_id', 'Sélectionner un adhérent');
        }

        if ($amount === null || $amount === '' || !is_numeric($amount) || $amount <= 0) {
            $result->addMessage('amount', 'Le montant doit être un nombre positif');
        }

        if ($method === '') {
            $result->addMessage('method', 'Le moyen de paiement est requis');
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $dto = new AddContributionDto(
            (int)$adherents_id,
            (int)round(((float)$amount) * 100),
            $method,
            $reference !== '' ? $reference : null,
            $notes !== '' ? $notes : null
        );

        $result->setValue($dto);
        return $result;
    }
}
