<?php

namespace ButA2SaeS3\validation;

use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\dto\AddMissionDto;
use ButA2SaeS3\validation\ValidationResult;

class Validators
{
    public static function validate_add_adherent(array $formdata): ValidationResult
    {

        $fieldnames = [
            'prenom',
            'nom',
            'adresse',
            'code_postal',
            'ville',
            'tel',
            'email',
            'age',
            'profession'
        ];

        $result = ValidationResult::empty();

        foreach ($fieldnames as $fieldname) {
            if (empty($formdata[$fieldname])) {
                $result->addMessage("$fieldname", "Le {$fieldname} est obligatoire");
            }
        }


        if (!$result->hasMessages()) {
            $result->setValue(new AddAdherentDto(
                prenom: $formdata["prenom"],
                nom: $formdata["nom"],
                adresse: $formdata["adresse"],
                code_postal: $formdata["code_postal"],
                ville: $formdata["ville"],
                tel: $formdata["tel"],
                email: $formdata["email"],
                age: $formdata["age"],
                profession: $formdata["profession"],
            ));
        }

        return $result;
    }

    public static function validate_add_mission(array $formdata): ValidationResult
    {
        $fieldnames = [
            'title',
            'description',
            'location',
            'start_date',
            'start_time',
            'end_date',
            'end_time'
        ];

        $result = ValidationResult::empty();

        foreach ($fieldnames as $fieldname) {
            if (empty($formdata[$fieldname])) {
                if ($fieldname === 'capacity' || $fieldname === 'budget_cents') {
                    continue;
                }
                $result->addMessage($fieldname, "Le champ {$fieldname} est obligatoire");
            }
        }


        $start_at = strtotime($formdata['start_date'] . ' ' . $formdata['start_time']);
        $end_at = strtotime($formdata['end_date'] . ' ' . $formdata['end_time']);

        if ($start_at === false || $end_at === false) {
            $result->addMessage('datetime', "Format de date/heure invalide");
        } elseif ($start_at >= $end_at) {
            $result->addMessage('datetime', "La date de fin doit être après la date de début");
        }


        if (!empty($formdata['capacity']) && (!is_numeric($formdata['capacity']) || $formdata['capacity'] <= 0)) {
            $result->addMessage('capacity', "La capacité doit être un nombre positif");
        }

        if (!empty($formdata['budget_cents']) && (!is_numeric($formdata['budget_cents']) || $formdata['budget_cents'] < 0)) {
            $result->addMessage('budget_cents', "Le budget doit être un nombre positif ou nul");
        }

        if (!$result->hasMessages()) {
            $result->setValue(new AddMissionDto(
                title: $formdata["title"],
                description: $formdata["description"],
                location: $formdata["location"],
                start_at: $start_at,
                end_at: $end_at,
                capacity: !empty($formdata['capacity']) ? (int)$formdata['capacity'] : null,
                budget_cents: !empty($formdata['budget_cents']) ? (int)$formdata['budget_cents'] : 0
            ));
        }

        return $result;
    }
}
