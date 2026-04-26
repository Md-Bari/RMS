<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function storeMenuImage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $data['image']->store('menu-items', 'public');
        $url = rtrim($request->getSchemeAndHttpHost(), '/').'/'.ltrim(Storage::url($path), '/');

        return response()->json([
            'path' => $path,
            'url' => $url,
        ], 201);
    }
}
