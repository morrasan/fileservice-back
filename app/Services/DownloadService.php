<?php

namespace App\Services;

use App\Repositories\FileRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class DownloadService {

    public function __construct(private readonly FileRepository $fileRepository) {}

    /**
     * Get original name and upload path (for download) of file by file id
     *
     * @param int $fileId
     *
     * @return array
     * @throws FileNotFoundException
     */
    public function filePath(int $fileId): array {

        $file = $this->fileRepository->getById($fileId);

        if (Storage::exists($file->filepath)) {

            return [
                Storage::path($file->filepath),
                $file->filename
            ];
        } else {

            throw new FileNotFoundException('File not found');
        }
    }
}
