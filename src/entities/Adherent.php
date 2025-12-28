<?php

namespace ButA2SaeS3\entities;

use ButA2SaeS3\dto\AddAdherentDto;
use ButA2SaeS3\dto\AdherentDto;

class Adherent
{
    public function __construct(
        public readonly int $id,
        public readonly string $prenom,
        public readonly string $nom,
        public readonly string $adresse,
        public readonly string $code_postal,
        public readonly string $ville,
        public readonly string $tel,
        public readonly string $email,
        public readonly string $age,
        public readonly string $profession
    ) {}

    public static function fromDbData(object|array $data): self
    {
        if (is_array($data)) {
            $data = (object)$data;
        }

        return new self(
            (int)$data->id,
            (string)$data->prenom,
            (string)$data->nom,
            (string)$data->adresse,
            (string)$data->code_postal,
            (string)$data->ville,
            (string)$data->tel,
            (string)$data->email,
            (string)$data->age,
            (string)$data->profession
        );
    }

    public static function fromDto(AddAdherentDto $dto, int $id = 0): self
    {
        return new self(
            $id,
            $dto->prenom,
            $dto->nom,
            $dto->adresse,
            $dto->code_postal,
            $dto->ville,
            $dto->tel,
            $dto->email,
            $dto->age,
            $dto->profession
        );
    }

    public static function fromAdherentDto(AdherentDto $dto): self
    {
        return new self(
            $dto->id,
            $dto->prenom,
            $dto->nom,
            $dto->adresse,
            $dto->code_postal,
            $dto->ville,
            $dto->tel,
            $dto->email,
            $dto->age,
            $dto->profession
        );
    }

    public function toAddDto(): AddAdherentDto
    {
        return new AddAdherentDto(
            $this->prenom,
            $this->nom,
            $this->adresse,
            $this->code_postal,
            $this->ville,
            $this->tel,
            $this->email,
            $this->age,
            $this->profession
        );
    }

    public function toUpdateDto(): \ButA2SaeS3\dto\UpdateAdherentDto
    {
        return new \ButA2SaeS3\dto\UpdateAdherentDto(
            $this->id,
            $this->prenom,
            $this->nom,
            $this->adresse,
            $this->code_postal,
            $this->ville,
            $this->tel,
            $this->email,
            $this->age,
            $this->profession
        );
    }

    public function getFullName(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
