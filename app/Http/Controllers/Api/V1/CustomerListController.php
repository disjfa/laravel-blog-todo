<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CustomerListController extends Controller
{
    #[OA\Get(
        path: '/customers',
        summary: 'List customers the authenticated user belongs to',
        security: [['sanctum' => []]],
        tags: ['Customers'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of customers',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Customer')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json($request->user()->customers);
    }
}
