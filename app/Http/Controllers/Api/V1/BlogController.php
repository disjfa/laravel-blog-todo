<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\Customer;
use App\Queries\BlogQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class BlogController extends Controller
{
    #[OA\Get(
        path: '/customers/{customer}/blogs',
        summary: 'List blogs for a customer',
        security: [['sanctum' => []]],
        tags: ['Blogs'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/filter'),
            new OA\Parameter(ref: '#/components/parameters/sort'),
            new OA\Parameter(ref: '#/components/parameters/include'),
            new OA\Parameter(ref: '#/components/parameters/page'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated blog list', content: new OA\JsonContent(ref: '#/components/schemas/BlogCollection')),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function index(Customer $customer): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Blog::class, $customer]);

        $blogs = (new BlogQuery)
            ->forCustomer($customer->id)
            ->paginate();

        return BlogResource::collection($blogs);
    }

    #[OA\Post(
        path: '/customers/{customer}/blogs',
        summary: 'Create a blog',
        security: [['sanctum' => []]],
        tags: ['Blogs'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/customer')],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'slug', 'excerpt', 'content_markdown', 'status'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'slug', type: 'string', maxLength: 255),
                    new OA\Property(property: 'excerpt', type: 'string', maxLength: 500),
                    new OA\Property(property: 'content_markdown', type: 'string'),
                    new OA\Property(property: 'status', ref: '#/components/schemas/BlogStatus'),
                    new OA\Property(property: 'publish_at', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/Blog')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreBlogRequest $request, Customer $customer): JsonResponse
    {
        $this->authorize('create', [Blog::class, $customer]);

        $blog = Blog::create([
            ...$request->validated(),
            'customer_id' => $customer->id,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(BlogResource::make($blog), 201);
    }

    #[OA\Get(
        path: '/customers/{customer}/blogs/{blog}',
        summary: 'Get a blog',
        security: [['sanctum' => []]],
        tags: ['Blogs'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/blog'),
            new OA\Parameter(ref: '#/components/parameters/include'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Blog', content: new OA\JsonContent(ref: '#/components/schemas/Blog')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Customer $customer, Blog $blog): BlogResource
    {
        $this->authorize('view', $blog);

        return BlogResource::make($blog->load('assets', 'author'));
    }

    #[OA\Patch(
        path: '/customers/{customer}/blogs/{blog}',
        summary: 'Update a blog',
        security: [['sanctum' => []]],
        tags: ['Blogs'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/blog'),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'slug', type: 'string', maxLength: 255),
                    new OA\Property(property: 'excerpt', type: 'string', maxLength: 500),
                    new OA\Property(property: 'content_markdown', type: 'string'),
                    new OA\Property(property: 'status', ref: '#/components/schemas/BlogStatus'),
                    new OA\Property(property: 'publish_at', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated blog', content: new OA\JsonContent(ref: '#/components/schemas/Blog')),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateBlogRequest $request, Customer $customer, Blog $blog): BlogResource
    {
        $this->authorize('update', $blog);

        $blog->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return BlogResource::make($blog);
    }

    #[OA\Delete(
        path: '/customers/{customer}/blogs/{blog}',
        summary: 'Delete a blog',
        security: [['sanctum' => []]],
        tags: ['Blogs'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/customer'),
            new OA\Parameter(ref: '#/components/parameters/blog'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function destroy(Customer $customer, Blog $blog): JsonResponse
    {
        $this->authorize('delete', $blog);

        $blog->delete();

        return response()->json(null, 204);
    }
}
