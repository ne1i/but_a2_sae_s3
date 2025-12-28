<?php

namespace ButA2SaeS3\dto;

class AddUserDto
{
    public function __construct(
        public string $username,
        public string $password,
        public string $role,
        public array $poles = []
    ) {}
}
