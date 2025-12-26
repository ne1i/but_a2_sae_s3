<?php

namespace ButA2SaeS3\dto;

class MissionDto
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $description,
        public string $location,
        public int $start_at,
        public int $end_at,
        public ?int $capacity,
        public int $budget_cents,
        public ?int $created_by,
        public ?string $created_by_username,
        public string $created_at
    ) {}
}

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