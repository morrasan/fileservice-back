<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileListRequest;
use App\Http\Requests\FileStoreRequest;
use App\Services\DestroyService;
use App\Services\DownloadService;
use App\Services\FileListService;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FileListRequest $request): JsonResponse {

        $cursor = $request->query('cursor', 0);

        $data = app(FileListService::class)->getFileListByCursor($cursor);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileStoreRequest $request): JsonResponse {

        app(StoreService::class)->store($request);

        return response()->json('Ok');
    }

    /**
     * Display the specified resource.
     */
    public function download(int $id): BinaryFileResponse|JsonResponse {

        try {

            [$path, $name] = app(DownloadService::class)->filePath($id);

            return response()->download($path, $name);

        } catch (\Throwable $e) {

            return response()->json(['error' => 'File not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse {

        try {
            app(DestroyService::class)->destroy($id);

            return response()->json(['message' => 'File deleted successfully']);

        } catch (\Throwable $e) {

            return response()->json(['error' => 'File not found'], 404);
        }
    }
}
