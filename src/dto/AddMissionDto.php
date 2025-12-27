<?php

namespace ButA2SaeS3\dto;

class AddMissionDto
{
    public function __construct(
        public string $title,
        public string $description,
        public string $location,
        public int $start_at,
        public int $end_at,
        public ?int $capacity,
        public int $budget_cents
    ) {}
}
