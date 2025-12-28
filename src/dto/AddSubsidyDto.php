<?php

namespace ButA2SaeS3\dto;

class AddSubsidyDto
{
    public function __construct(
        public ?int $partnerId,
        public string $title,
        public int $amountCents,
        public ?int $awardedAt,
        public ?string $conditions = null,
        public ?string $notes = null
    ) {}
}
