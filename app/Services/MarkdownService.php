<?php

namespace App\Services;

use GrahamCampbell\Markdown\Facades\Markdown;

class MarkdownService
{
    public function toHtml(?string $markdown): ?string
    {
        if ($markdown === null || $markdown === '') {
            return null;
        }

        return Markdown::convert($markdown)->getContent();
    }
}
