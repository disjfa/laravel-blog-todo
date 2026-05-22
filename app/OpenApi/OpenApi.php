<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Laravel Blog API',
    description: 'Customer-scoped Blog and Todo API with social automation.',
)]
#[OA\Server(url: '/api/v1', description: 'API v1')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Sanctum bearer token',
)]
#[OA\Tag(name: 'Blogs', description: 'Blog CRUD scoped to a customer')]
#[OA\Tag(name: 'Todos', description: 'Todo board scoped to a customer')]
#[OA\Tag(name: 'Me', description: 'Authenticated user context')]

// ── Shared parameter components ────────────────────────────────────────────

#[OA\Parameter(
    parameter: 'customer',
    name: 'customer',
    in: 'path',
    required: true,
    description: 'Customer UUID',
    schema: new OA\Schema(type: 'string', format: 'uuid'),
)]
#[OA\Parameter(
    parameter: 'blog',
    name: 'blog',
    in: 'path',
    required: true,
    description: 'Blog UUID',
    schema: new OA\Schema(type: 'string', format: 'uuid'),
)]
#[OA\Parameter(
    parameter: 'todo',
    name: 'todo',
    in: 'path',
    required: true,
    description: 'Todo UUID',
    schema: new OA\Schema(type: 'string', format: 'uuid'),
)]

// ── Query builder shared parameters ───────────────────────────────────────

#[OA\Parameter(
    parameter: 'filter',
    name: 'filter',
    in: 'query',
    required: false,
    description: 'Spatie Query Builder filter map, e.g. filter[status]=draft',
    schema: new OA\Schema(type: 'object'),
    style: 'deepObject',
    explode: true,
)]
#[OA\Parameter(
    parameter: 'sort',
    name: 'sort',
    in: 'query',
    required: false,
    description: 'Comma-separated sort fields, prefix with - for descending, e.g. -created_at',
    schema: new OA\Schema(type: 'string'),
)]
#[OA\Parameter(
    parameter: 'include',
    name: 'include',
    in: 'query',
    required: false,
    description: 'Comma-separated relations to include in the response',
    schema: new OA\Schema(type: 'string'),
)]
#[OA\Parameter(
    parameter: 'page',
    name: 'page',
    in: 'query',
    required: false,
    description: 'Pagination page number',
    schema: new OA\Schema(type: 'integer', default: 1),
)]
class OpenApi {}
