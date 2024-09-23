<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\Files\UploadImage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct(public UploadImage $fileService)
    {
    }
    public function store(FileRequest $request)
    {
        $file = $this->fileService->storeFile($request->file('file'));
        return new FileResource($file);
    }
    public function destroy(Request $request, File $file)
    {
        if (in_array($request->user()->role, ['admin', 'moder', 'manager'])) {
            $file->delete();
            return response()->noContent();
        }
        if ($request->user()->id == $file->imagable?->supplier?->id) {
            $file->delete();
            return response()->noContent();
        }
        return response()->json(['message' => "Not authorized"], 403);
    }
}
