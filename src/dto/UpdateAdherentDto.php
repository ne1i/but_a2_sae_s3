<?php

namespace ButA2SaeS3\dto;

class UpdateAdherentDto
{
    public function __construct(
        public int $id,
        public string $prenom,
        public string $nom,
        public string $adresse,
        public string $code_postal,
        public string $ville,
        public string $tel,
        public string $email,
        public string $age,
        public string $profession
    ) {}
}
