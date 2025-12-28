<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\AddMissionDto;
use ButA2SaeS3\dto\UpdateMissionDto;
use ButA2SaeS3\validation\ValidationResult;

class MissionValidationService
{
    public static function validateAddMission(array $data): ValidationResult
    {
        return self::validateMission($data, null);
    }

    public static function validateUpdateMission(array $data, int $id): ValidationResult
    {
        return self::validateMission($data, $id);
    }

    private static function validateMission(array $data, ?int $id): ValidationResult
    {
        $result = ValidationResult::empty();

        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $location = trim($data['location'] ?? '');
        $startDate = $data['start_date'] ?? '';
        $startTime = $data['start_time'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $endTime = $data['end_time'] ?? '';
        $capacity = $data['capacity'] ?? null;
        $budget = $data['budget_cents'] ?? null;

        if ($title === '') {
            $result->addMessage('title', 'Le titre est requis');
        }
        if ($description === '') {
            $result->addMessage('description', 'La description est requise');
        }
        if ($location === '') {
            $result->addMessage('location', 'Le lieu est requis');
        }

        $start_at = strtotime($startDate . ' ' . $startTime);
        $end_at = strtotime($endDate . ' ' . $endTime);

        if ($start_at === false || $end_at === false) {
            $result->addMessage('datetime', "Format de date/heure invalide");
        } elseif ($start_at >= $end_at) {
            $result->addMessage('datetime', "La date de fin doit être après la date de début");
        }

        if ($capacity !== null && $capacity !== '') {
            if (!is_numeric($capacity) || $capacity <= 0) {
                $result->addMessage('capacity', "La capacité doit être un nombre positif");
            }
        }

        if ($budget !== null && $budget !== '') {
            if (!is_numeric($budget) || $budget < 0) {
                $result->addMessage('budget_cents', "Le budget doit être un nombre positif ou nul");
            }
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $dtoBudget = $budget !== null && $budget !== '' ? (int)($budget * 100) : 0;
        $dtoCapacity = ($capacity !== null && $capacity !== '') ? (int)$capacity : null;

        if ($id === null) {
            $dto = new AddMissionDto(
                $title,
                $description,
                $location,
                $start_at,
                $end_at,
                $dtoCapacity,
                $dtoBudget
            );
        } else {
            $dto = new UpdateMissionDto(
                $id,
                $title,
                $description,
                $location,
                $start_at,
                $end_at,
                $dtoCapacity,
                $dtoBudget
            );
        }

        $result->setValue($dto);
        return $result;
    }
}
