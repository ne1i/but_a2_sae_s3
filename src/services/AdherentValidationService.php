<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\dto\UpdateAdherentDto;
use ButA2SaeS3\validation\ValidationResult;

class AdherentValidationService
{
    public static function validateAddAdherent(array $data): ValidationResult
    {
        return self::validateAdherent($data, null);
    }

    public static function validateUpdateAdherent(array $data, int $id): ValidationResult
    {
        return self::validateAdherent($data, $id);
    }

    private static function validateAdherent(array $data, ?int $id): ValidationResult
    {
        $result = ValidationResult::empty();

        $prenom = trim($data['prenom'] ?? '');
        $nom = trim($data['nom'] ?? '');
        $email = trim($data['email'] ?? '');
        $adresse = trim($data['adresse'] ?? '');
        $code_postal = trim($data['code_postal'] ?? '');
        $ville = trim($data['ville'] ?? '');
        $tel = trim($data['tel'] ?? '');
        $age = $data['age'] ?? '';
        $profession = trim($data['profession'] ?? '');

        if ($prenom === '') {
            $result->addMessage('prenom', 'Le prénom est requis');
        }

        if ($nom === '') {
            $result->addMessage('nom', 'Le nom est requis');
        }

        if ($email === '') {
            $result->addMessage('email', 'L\'email est requis');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result->addMessage('email', 'Veuillez entrer une adresse email valide');
        }

        if ($adresse === '') {
            $result->addMessage('adresse', 'L\'adresse est requise');
        }

        if ($code_postal === '') {
            $result->addMessage('code_postal', 'Le code postal est requis');
        } elseif (!preg_match('/^\d{5}$/', $code_postal)) {
            $result->addMessage('code_postal', 'Le code postal doit contenir 5 chiffres');
        }

        if ($ville === '') {
            $result->addMessage('ville', 'La ville est requise');
        }

        if ($tel === '') {
            $result->addMessage('tel', 'Le téléphone est requis');
        } elseif (!preg_match('/^(\+\d{1,3})?[\d\s\.\-\(\)]+$/', $tel)) {
            $result->addMessage('tel', 'Le numéro de téléphone n\'est pas valide');
        }

        if ($age === '') {
            $result->addMessage('age', 'L\'âge est requis');
        } elseif (!is_numeric($age) || $age < 0 || $age > 150) {
            $result->addMessage('age', 'L\'âge doit être un nombre entre 0 et 150');
        }

        if ($profession === '') {
            $result->addMessage('profession', 'La profession est requise');
        }

        if ($result->hasMessages()) {
            return $result;
        }

        if ($id === null) {
            $dto = new AddAdherentDto(
                $prenom,
                $nom,
                $adresse,
                $code_postal,
                $ville,
                $tel,
                $email,
                (string)$age,
                $profession
            );
        } else {
            $dto = new UpdateAdherentDto(
                $id,
                $prenom,
                $nom,
                $adresse,
                $code_postal,
                $ville,
                $tel,
                $email,
                (string)$age,
                $profession
            );
        }

        $result->setValue($dto);
        return $result;
    }
}
