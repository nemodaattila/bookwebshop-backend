<?php

namespace helper;

// Link image type to correct image loader
// - makes it easier to add additional types later on
// - makes the function easier to read
use exception\HttpResponseTriggerException;

/**
 * Class ImgHelper képekkel kapcsolatos függvények
 * @package core\backend\helper
 */
class ImgHelper
{
    /**
     * imagekezelők tipustól függően - betöltőfüggvény , mentőfüggvény, minőség
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
     * @param string $fileName
     * @param string $sourcePath
     * @param string $targetPath
     * @param int $width
     * @param int $height
     * @todo
     */
    static public function createCover(string $fileName, string $sourcePath, string $targetPath, int $width, int $height)
    {
//        var_dump($fileName);
    }

    /**
     * képből thumbnail-t csinál
     * @param string $filename a file neve (elérési út nélkül)
     * @param string $src elérési út
     * @param string $dest thumbnail mentési helye
     * @param int $targetWidth a létrehozandó thumbnail szélessége
     * @param int|null $targetHeight a létrehozandó thumbnail magassága | null - arányosan
     * @throws RequestResultException ha a forrásfájl nem létezik
     * @throws RequestResultException ha a mentés nem sikerült
     * @author //source : https://pqina.nl/blog/creating-thumbnails-with-php/
     */
    static public function createThumbnail(string $filename, string $src, string $dest, int $targetWidth, int $targetHeight = null): ?bool
    {
        if (!file_exists($src . '\\' . $filename)) {
            throw new RequestResultException(500, ['errorCode=>IHCTFNE', 'fileName' => $src . '\\' . $filename]);
        }
        $type = exif_imagetype($src . '\\' . $filename);
        if (!$type || !self::IMAGE_HANDLERS[$type]) {
            return null;
        }
        $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $src . '\\' . $filename);
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
            throw new RequestResultException(500, ['errorCode' => 'IHTCE']);
        }
        return $save;
    }

    /**
     * képből base64 stringet képez
     * @param string $file a leképezendő kép
     * @return string az átalakított kép base64 string formában
     * @throws RequestResultException ha a kép nem létezik
     */
    static public function convertImageToBase64String(string $file): string
    {
        if (!file_exists($file)) {
            throw new HttpResponseTriggerException(false,['errorCode=>IHCTFNE', 'fileName' => $file],200);
        }
        $imgData = base64_encode(file_get_contents($file));
        return 'data: ' . mime_content_type($file) . ';base64,' . $imgData;
    }
}
