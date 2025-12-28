<?php

namespace ButA2SaeS3\dto;

class UpdateArticleDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public string $status
    ) {}
}
