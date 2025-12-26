<?php

namespace ButA2SaeS3\validation;

use ButA2SaeS3\dto\AddAdherentDto;
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
}
