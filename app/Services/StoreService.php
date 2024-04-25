<?php

namespace App\Services;

use App\Http\Requests\FileStoreRequest;
use App\Jobs\StoreOpenMeteoDataJob;
use App\Repositories\FileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StoreService {

    public function __construct (
        private readonly UserRepository $userRepository,
        private readonly FileRepository $fileRepository,
        private readonly FileMetaDataService $fileMetaDataService,
        private readonly ThumbnailService $thumbnailService
    ) {}

    /**
     * Store all file data
     *
     * @param FileStoreRequest $request
     *
     * @return void
     * @throws \Throwable
     */
    public function store (FileStoreRequest $request): void {

        $requestData = $request->toArray();

        $user = $this->userRepository->getOrCreate($requestData['name'], $requestData['email']);

        DB::beginTransaction();
        try {
            foreach ($requestData['files'] as $file) {

                $fileName = $file->getClientOriginalName();

                // create thumbnail for the file
                $thumbnail = $this->thumbnailService->makeThumbnail($file);

                // generate path for image
                [$name, $fullName, $path] = $this->generateFilePath($fileName);

                // generate path for thumbnail
                [$thumbnailName, $thumbnailFullName, $thumbnailPath] = $this->generateFilePath($fileName, true);

                $fileData = [
                    'user_id' => $user->id,
                    'filename' => $fileName,
                    'filepath' => $fullName,
                    'thumbnail' => $thumbnailFullName,
                    'metadata' => $this->fileMetaDataService->getMetaData($file)
                ];

                $storedFile = $this->fileRepository->store($fileData);

                $file->storeAs($path, $name);

                $this->checkAndCreatePathInPublic($thumbnailPath);

                $thumbnail->save($thumbnailPath . DIRECTORY_SEPARATOR . $thumbnailName);

                // run job to get and then store temperature for file
                StoreOpenMeteoDataJob::dispatch($storedFile->id);
            }

        } catch (\Throwable $e) {

            Log::error("[StoreService][store]" . "\n". $e->getMessage());

            DB::rollBack();

            throw $e;
        }
        DB::commit();
    }

    /**
     * Generate hash name, uploads and thumbnails paths for file
     *
     * @param string $filename
     * @param bool $isThumbnail
     *
     * @return array
     */
    private function generateFilePath (string $filename, bool $isThumbnail = false): array {

        $pathPrefix = $isThumbnail ? 'thumbnails' : 'uploads';

        $hashName =  md5($filename . $pathPrefix);

        // generate random offset for building the path
        $hashLength = strlen($hashName);
        $randomOffset = rand(0, $hashLength - 2);

        $subPaths[] = $pathPrefix;
        $subPaths[] = now()->format('Ymd');
        $subPaths[] = substr($hashName, $randomOffset, 2);

        return [
            $hashName,
            implode(DIRECTORY_SEPARATOR, $subPaths) . DIRECTORY_SEPARATOR . $hashName,
            implode(DIRECTORY_SEPARATOR, $subPaths)
        ];
    }

    /**
     * Check directory for thumbnail and create if not exists
     *
     * @param string $path
     *
     * @return void
     */
    private function checkAndCreatePathInPublic (string $path): void {
        if (!File::exists($path)) {

            // create directory if not exists
            File::makeDirectory($path, 0755, true, true);
        }
    }
}
