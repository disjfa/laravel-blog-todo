<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiDocsController extends Controller
{
    public function ui(): Response
    {
        return response()->view('api.docs');
    }

    public function json(): JsonResponse
    {
        $path = public_path('api-docs.json');

        abort_if(! file_exists($path), 404, 'API docs not generated. Run: composer docs');

        return response()->json(json_decode(file_get_contents($path), true));
    }
}
