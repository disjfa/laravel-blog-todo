<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Blog',
    required: ['id', 'customer_id', 'title', 'slug', 'content_markdown', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'customer_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'excerpt', type: 'string', nullable: true),
        new OA\Property(property: 'content_markdown', type: 'string'),
        new OA\Property(property: 'content_html', type: 'string', description: 'Rendered HTML, computed at response time'),
        new OA\Property(property: 'status', ref: '#/components/schemas/BlogStatus'),
        new OA\Property(property: 'publish_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'author', ref: '#/components/schemas/Author', nullable: true),
    ],
)]
class BlogSchema {}
