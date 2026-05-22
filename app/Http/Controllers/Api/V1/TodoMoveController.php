<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MoveTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Customer;
use App\Models\Todo;
use OpenApi\Attributes as OA;

class TodoMoveController extends Controller
{
    #[OA\Patch(
        path: '/customers/{customer}/todos/{todo}/move',
        summary: 'Move a todo to a different status/position (Kanban reorder)',
        security: [['sanctum' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/todo'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['position'],
                properties: [
                    new OA\Property(property: 'status', ref: '#/components/schemas/TodoStatus'),
                    new OA\Property(property: 'position', type: 'string', description: 'Lexicographic position key within the column'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Moved todo', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function __invoke(MoveTodoRequest $request, Customer $customer, Todo $todo): TodoResource
    {
        abort_if(
            $todo->customer_id !== $customer->id,
            403,
            'Todo does not belong to this customer.'
        );

        $todo->update($request->validated());

        return TodoResource::make($todo);
    }
}
