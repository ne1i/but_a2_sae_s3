<?php

namespace ButA2SaeS3\dto;

class AddPartnerDto
{
    public function __construct(
        public string $name,
        public ?string $contact = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $website = null,
        public ?string $notes = null
    ) {}
}
