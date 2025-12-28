<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\AddUserDto;
use ButA2SaeS3\validation\ValidationResult;

class UserValidationService
{
    public static function validateCreateUser(array $data): ValidationResult
    {
        $result = ValidationResult::empty();

        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password-confirm'] ?? '';
        $role = trim($data['role'] ?? '');
        $poles = $data['poles'] ?? [];

        if ($username === '') {
            $result->addMessage('username', "Le nom d'utilisateur est requis");
        }

        if ($password === '') {
            $result->addMessage('password', "Le mot de passe est requis");
        }

        if ($passwordConfirm === '') {
            $result->addMessage('password-confirm', "La confirmation est requise");
        } elseif ($password !== $passwordConfirm) {
            $result->addMessage('password-confirm', "Les mots de passe ne correspondent pas");
        }

        if ($role === '') {
            $result->addMessage('role', "Le rôle est requis");
        } elseif ($role === 'responsable-pole' && empty($poles)) {
            $result->addMessage('poles', "Sélectionner au moins un pôle");
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $dto = new AddUserDto(
            $username,
            $password,
            $role,
            is_array($poles) ? $poles : []
        );
        $result->setValue($dto);
        return $result;
    }
}
