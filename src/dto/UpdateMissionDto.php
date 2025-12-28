<?php

namespace ButA2SaeS3\dto;

class UpdateMissionDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $location,
        public int $start_at,
        public int $end_at,
        public ?int $capacity,
        public int $budget_cents
    ) {}
}
