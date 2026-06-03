<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Customer;
use App\Models\Todo;
use App\Queries\TodoQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class TodoController extends Controller
{
    #[OA\Get(
        path: '/customers/{customer}/todos',
        summary: 'List todos for a customer',
        security: [['sanctum' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/filter'),
            new OA\Parameter(ref: '#/components/parameters/sort'),
            new OA\Parameter(ref: '#/components/parameters/include'),
            new OA\Parameter(ref: '#/components/parameters/page'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated todo list', content: new OA\JsonContent(ref: '#/components/schemas/TodoCollection')),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function index(Customer $customer): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Todo::class, $customer]);

        $todos = (new TodoQuery)
            ->forCustomer($customer->id)
            ->paginate();

        return TodoResource::collection($todos);
    }

    #[OA\Post(
        path: '/customers/{customer}/todos',
        summary: 'Create a todo',
        security: [['sanctum' => []]],
        tags: ['Todos'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/customer')],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['platform_id', 'title', 'status', 'due_at'],
                properties: [
                    new OA\Property(property: 'blog_id', type: 'string', format: 'uuid', nullable: true),
                    new OA\Property(property: 'platform_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'content_markdown', type: 'string', nullable: true),
                    new OA\Property(property: 'status', ref: '#/components/schemas/TodoStatus'),
                    new OA\Property(property: 'position', type: 'string', nullable: true),
                    new OA\Property(property: 'due_at', type: 'string', format: 'date-time'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreTodoRequest $request, Customer $customer): JsonResponse
    {
        $this->authorize('create', [Todo::class, $customer]);

        $todo = Todo::create([
            ...$request->validated(),
            'customer_id' => $customer->id,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return TodoResource::make($todo)->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/customers/{customer}/todos/{todo}',
        summary: 'Get a todo',
        security: [['sanctum' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/todo'),
            new OA\Parameter(ref: '#/components/parameters/include'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Todo', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Customer $customer, Todo $todo): TodoResource
    {
        $this->authorize('view', $todo);

        return TodoResource::make($todo->load('blog', 'platform', 'creator'));
    }

    #[OA\Patch(
        path: '/customers/{customer}/todos/{todo}',
        summary: 'Update a todo',
        security: [['sanctum' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/todo'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'blog_id', type: 'string', format: 'uuid', nullable: true),
                    new OA\Property(property: 'platform_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'content_markdown', type: 'string', nullable: true),
                    new OA\Property(property: 'status', ref: '#/components/schemas/TodoStatus'),
                    new OA\Property(property: 'position', type: 'string', nullable: true),
                    new OA\Property(property: 'due_at', type: 'string', format: 'date-time'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated todo', content: new OA\JsonContent(ref: '#/components/schemas/Todo')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateTodoRequest $request, Customer $customer, Todo $todo): TodoResource
    {
        $this->authorize('update', $todo);

        $todo->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return TodoResource::make($todo);
    }

    #[OA\Delete(
        path: '/customers/{customer}/todos/{todo}',
        summary: 'Delete a todo',
        security: [['sanctum' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/todo'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function destroy(Customer $customer, Todo $todo): JsonResponse
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return response()->json(null, 204);
    }
}
