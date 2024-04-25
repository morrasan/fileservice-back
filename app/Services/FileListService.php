<?php

namespace App\Services;

use App\Repositories\FileRepository;
use App\Transformers\FileListTransformer;

class FileListService {

    // number of files per page
    private int $fileLimit = 10;

    public function __construct (
        private readonly FileRepository $fileRepository,
        private readonly FileListTransformer $fileListTransformer
    ) {}

    /**
     * Return transformed files data with nextCursor by the limit
     *
     * @param int $cursor
     *
     * @return array
     */
    public function getFileListByCursor (int $cursor): array {

        [$files, $nextCursor] = $this->fileRepository->getLimitedFilesPerCursor($cursor, $this->fileLimit);

        return [
            'files' => $this->fileListTransformer->transform($files),
            'nextCursor' => $nextCursor,
        ];
    }
}
