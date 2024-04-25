<?php

namespace App\Repositories;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;

class FileRepository {

    /**
     * Store new file in database
     *
     * @param array $data
     *
     * @return File
     */
    public function store(array $data): File {

        $file = new File();
        $file->filename = $data['filename'];
        $file->filepath = $data['filepath'];
        $file->thumbnail = $data['thumbnail'];
        $file->user_id = $data['user_id'];
        $file->metadata = $data['metadata'];
        $file->save();

        return $file;
    }

    /**
     * Get file by id
     *
     * @param int $id
     *
     * @return Model|null
     */
    public function getById(int $id): Model|null {
        return File::query()->findOrFail($id);
    }

    /**
     * Get collection of files and last file id from this collection as nextCursor
     *
     * @param int $cursor
     * @param int $limit
     *
     * @return array
     */
    public function getLimitedFilesPerCursor (int $cursor, int $limit): array {

        // get files starting from the current {cursor}, with {limit} elements each
        $files = File::query()->where('id', '>', $cursor)->with('user')->take($limit)->get();

        // get the next cursor for use in subsequent requests
        $nextCursor = $files->isNotEmpty() ? $files->last()->id : null;

        return [
            $files,
            $nextCursor
        ];
    }
}
