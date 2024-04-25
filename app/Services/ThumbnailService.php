<?php

namespace App\Services;

use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class ThumbnailService {

    private int $width = 500;
    private int $height = 500;

    /**
     * Make thumbnail from file
     *
     * @param $file
     *
     * @return ImageInterface
     */
    public function makeThumbnail($file): ImageInterface {

        $tempPath = $file->getPathname();

        $image = Image::read($tempPath);

        $image->resize($this->width, $this->height);

        return $image;
    }
}
