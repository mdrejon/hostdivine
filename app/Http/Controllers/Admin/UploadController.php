<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Image uploads for CMS image fields (stored on the public disk).
 */
class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:4096', 'mimes:jpg,jpeg,png,webp,gif,svg'],
        ]);

        $path = $request->file('file')->store('cms', 'public');

        return response()->json(['path' => 'storage/'.$path, 'url' => asset('storage/'.$path)]);
    }
}
