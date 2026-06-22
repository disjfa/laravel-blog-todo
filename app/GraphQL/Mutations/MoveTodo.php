<?php

namespace App\GraphQL\Mutations;

use App\Models\Todo;
use Illuminate\Auth\Access\AuthorizationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class MoveTodo
{
    /**
     * @param  array{customerId: string, todoId: string, status?: string|null, position: string}  $args
     */
    public function __invoke(mixed $root, array $args, GraphQLContext $context): Todo
    {
        $todo = Todo::query()->findOrFail($args['todoId']);

        if ($todo->customer_id !== $args['customerId']) {
            throw new AuthorizationException('Todo does not belong to this customer.');
        }

        $todo->update([
            'status' => $args['status'] ?? $todo->status,
            'position' => $args['position'],
        ]);

        return $todo->fresh(['blog', 'platform', 'creator']);
    }
}

