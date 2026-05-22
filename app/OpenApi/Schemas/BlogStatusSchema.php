<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BlogStatus',
    type: 'string',
    enum: ['draft', 'published', 'archived'],
)]
class BlogStatusSchema {}
