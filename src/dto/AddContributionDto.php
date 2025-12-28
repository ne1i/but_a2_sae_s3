<?php

namespace ButA2SaeS3\dto;

class AddContributionDto
{
    public function __construct(
        public int $adherents_id,
        public int $amount_cents,
        public string $method,
        public ?string $reference = null,
        public ?string $notes = null
    ) {}
}
