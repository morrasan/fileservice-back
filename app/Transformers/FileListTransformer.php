<?php

namespace App\Transformers;

use App\Models\File;

class FileListTransformer {

    /**
     * Transformation files data
     *
     * @param $files
     *
     * @return array
     */
    public function transform ($files): array {

        $transformData = [];

        foreach ($files as $file) {

            $transformData[] = [
                'id' => $file->id,
                'filename' => $file->filename,
                'filesize' => $file->metadata['FileSize'],
                'extension' => $file->metadata['MimeType'],
                'resolution' => $this->getResolution($file),
                'ownerName' => $file->user->name,
                'thumbnailUrl' => $file->thumbnail,
            ];
        }

        return $transformData;
    }

    /**
     * Get resolution from file
     *
     * @param File $file
     *
     * @return string
     */
    private function getResolution (File $file): string {

        return $file->metadata['COMPUTED']['Width'] . 'x' . $file->metadata['COMPUTED']['Height'];
    }
}
