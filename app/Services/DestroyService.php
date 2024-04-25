<?php

namespace App\Services;

use App\Repositories\FileRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DestroyService {

    public function __construct (private readonly FileRepository $fileRepository) {}

    /**
     * Destroy the file from database/uploads/thumbnails by id
     *
     * @param int $id
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function destroy (int $id): void {

        $file = $this->fileRepository->getById($id);

        $this->deleteFileFromUploads($file->filepath);

        $this->deleteFileFromPublic($file->thumbnail);

        $file->delete();
    }

    /**
     * Delete file from uploads by path
     *
     * @param string $filePath
     *
     * @return void
     * @throws FileNotFoundException
     */
    private function deleteFileFromUploads (string $filePath): void {

        if (Storage::exists($filePath)) {

            Storage::delete($filePath);

        } else {

            throw new FileNotFoundException('File not found');
        }
    }

    /**
     * Delete file from thumbnails by path
     *
     * @param string $thumbnailPath
     *
     * @return void
     * @throws FileNotFoundException
     */
    private function deleteFileFromPublic (string $thumbnailPath): void {

        if (File::exists($thumbnailPath)) {

            File::delete($thumbnailPath);

        } else {

            throw new FileNotFoundException('File not found');
        }
    }
}
