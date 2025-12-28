<?php

namespace ButA2SaeS3\dto;

class AddDonorDto
{
    public function __construct(
        public string $name,
        public ?string $contact = null,
        public ?string $email = null,
        public ?string $notes = null
    ) {}
}
