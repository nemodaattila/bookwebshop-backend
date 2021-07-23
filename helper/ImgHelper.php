<?php

namespace helper;

use exception\HttpResponseTriggerException;

/**
 * Class ImgHelper helper functions connected to images
 * @package helper
 */
class ImgHelper
{
    /**
     * imageHandler functions by file type - loading/saving + image quality
     */
    const IMAGE_HANDLERS = [
        IMAGETYPE_JPEG => [
            'load' => 'imagecreatefromjpeg',
            'save' => 'imagejpeg',
            'quality' => 100
        ],
        IMAGETYPE_PNG => [
            'load' => 'imagecreatefrompng',
            'save' => 'imagepng',
            'quality' => 0
        ],
        IMAGETYPE_GIF => [
            'load' => 'imagecreatefromgif',
            'save' => 'imagegif'
        ]
    ];

    /**
     * creates a thumbnail from an image
     * @param string $filename filename - without path
     * @param string $path path of the file to be converted
     * @param string $dest save path of the thumbnail
     * @param int $targetWidth width of the thumbnail
     * @param int|null $targetHeight height of the thumbnail - if it's 0 it will be proportionate
     * @throws HttpResponseTriggerException if file not exists
     * @throws HttpResponseTriggerException if save failed
     * @author source : https://pqina.nl/blog/creating-thumbnails-with-php/
     */
    static public function createThumbnail(string $filename, string $path, string $dest, int $targetWidth, int $targetHeight = null): ?bool
    {
        if (!file_exists($path . '\\' . $filename)) {
            throw new HttpResponseTriggerException(false, ['errorCode=>IHCTFNE', 'fileName' => $path . '\\' . $filename], 500);
        }
        $type = exif_imagetype($path . '\\' . $filename);
        if (!$type || !self::IMAGE_HANDLERS[$type]) {
            return null;
        }
        $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $path . '\\' . $filename);
        if (!$image) {
            return null;
        }
        $width = imagesx($image);
        $height = imagesy($image);
        if ($targetHeight == null) {
            $ratio = $width / $height;
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            } else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }
        imagecopyresampled(
            $thumbnail,
            $image,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $width, $height
        );
        $save = call_user_func(
            self::IMAGE_HANDLERS[$type]['save'],
            $thumbnail,
            $dest . '\\' . $filename,
            self::IMAGE_HANDLERS[$type]['quality']
        );
        if ($save !== true) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'IHTCE'], 500);
        }
        return $save;
    }

    /**
     * converts an image to base64 string
     * @param string $file image to be converted
     * @return string image in base64 string form
     * @throws HttpResponseTriggerException if file not exists
     */
    static public function convertImageToBase64String(string $file): string
    {
        if (!file_exists($file)) {
            throw new HttpResponseTriggerException(false, ['errorCode=>IHCTFNE', 'fileName' => $file], 500);
        }
        $imgData = base64_encode(file_get_contents($file));
        return 'data: ' . mime_content_type($file) . ';base64,' . $imgData;
    }
}
