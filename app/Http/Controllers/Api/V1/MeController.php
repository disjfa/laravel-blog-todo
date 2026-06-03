<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MeController extends Controller
{
    #[OA\Get(
        path: '/me',
        summary: 'Get the authenticated user with their customers',
        security: [['sanctum' => []]],
        tags: ['Me'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated user with customers',
                content: new OA\JsonContent(ref: '#/components/schemas/Me')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('customers'));
    }
}
