<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileMetaDataService {

    /**
     * Get metadata from file
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    public function getMetaData(UploadedFile $file): array {

        $metaData = exif_read_data($file->getPathname());

        if (!is_array($metaData)) {
            return [];
        }

        return $metaData;
    }
}
