<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TodoStatus',
    type: 'string',
    enum: ['todo', 'planned', 'in_progress', 'blocked', 'done'],
)]
class TodoStatusSchema {}
