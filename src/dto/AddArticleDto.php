<?php

namespace ButA2SaeS3\dto;

class AddArticleDto
{
    public function __construct(
        public string $title,
        public string $content,
        public string $status = 'draft'
    ) {}
}
