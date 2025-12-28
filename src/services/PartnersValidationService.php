<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\dto\AddDonorDto;
use ButA2SaeS3\dto\AddPartnerDto;
use ButA2SaeS3\dto\AddSubsidyDto;
use ButA2SaeS3\validation\ValidationResult;

class PartnersValidationService
{
    public static function validateAddPartner(array $data): ValidationResult
    {
        $result = ValidationResult::empty();

        $name = trim($data['partner_name'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');
        $website = trim($data['website'] ?? '');
        $notes = trim($data['notes'] ?? '');

        if ($name === '') {
            $result->addMessage('partner_name', 'Le nom est requis');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result->addMessage('email', 'Email invalide');
        }

        if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
            $result->addMessage('website', 'URL invalide');
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $result->setValue(new AddPartnerDto(
            $name,
            $contact !== '' ? $contact : null,
            $email !== '' ? $email : null,
            $phone !== '' ? $phone : null,
            $address !== '' ? $address : null,
            $website !== '' ? $website : null,
            $notes !== '' ? $notes : null
        ));

        return $result;
    }

    public static function validateAddDonor(array $data): ValidationResult
    {
        $result = ValidationResult::empty();

        $name = trim($data['donor_name'] ?? '');
        $contact = trim($data['contact'] ?? '');
        $email = trim($data['email'] ?? '');
        $notes = trim($data['notes'] ?? '');

        if ($name === '') {
            $result->addMessage('donor_name', 'Le nom est requis');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result->addMessage('email', 'Email invalide');
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $result->setValue(new AddDonorDto(
            $name,
            $contact !== '' ? $contact : null,
            $email !== '' ? $email : null,
            $notes !== '' ? $notes : null
        ));

        return $result;
    }

    public static function validateAddSubsidy(array $data): ValidationResult
    {
        $result = ValidationResult::empty();

        $partnerId = $data['partner_id'] ?? null;
        $title = trim($data['title'] ?? '');
        $amount = $data['amount'] ?? null;
        $awardedAtStr = trim($data['awarded_at'] ?? '');
        $conditions = trim($data['conditions'] ?? '');
        $notes = trim($data['notes'] ?? '');

        if ($title === '') {
            $result->addMessage('title', 'Le titre est requis');
        }

        if ($amount === null || $amount === '' || !is_numeric($amount) || $amount < 0) {
            $result->addMessage('amount', 'Le montant doit être un nombre');
        }

        $awardedAt = null;
        if ($awardedAtStr !== '') {
            $parsed = strtotime($awardedAtStr);
            if ($parsed === false) {
                $result->addMessage('awarded_at', "Date invalide");
            } else {
                $awardedAt = $parsed;
            }
        }

        if ($result->hasMessages()) {
            return $result;
        }

        $pid = null;
        if ($partnerId !== null && $partnerId !== '') {
            $pid = (int)$partnerId;
        }

        $result->setValue(new AddSubsidyDto(
            $pid,
            $title,
            (int)round(((float)$amount) * 100),
            $awardedAt,
            $conditions !== '' ? $conditions : null,
            $notes !== '' ? $notes : null
        ));

        return $result;
    }
}

