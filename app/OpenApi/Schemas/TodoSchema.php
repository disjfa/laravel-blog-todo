<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Todo',
    required: ['id', 'customer_id', 'title', 'status', 'position', 'due_at'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'customer_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'blog_id', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'platform_id', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'content_markdown', type: 'string', nullable: true),
        new OA\Property(property: 'content_html', type: 'string', nullable: true, description: 'Rendered HTML, computed at response time'),
        new OA\Property(property: 'status', ref: '#/components/schemas/TodoStatus'),
        new OA\Property(property: 'position', type: 'string', description: 'Per-column lexicographic sort key'),
        new OA\Property(property: 'due_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
class TodoSchema {}
