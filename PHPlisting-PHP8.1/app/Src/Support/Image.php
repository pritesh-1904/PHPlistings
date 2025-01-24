<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Support;

class Image
{

    private $image;
    private $type;
    private $sourceWidth;
    private $sourceHeight;

    public function __construct($path)
    {
        if ($path instanceof \App\Src\Http\File\File) {
            $path = $path->getRealPath();
        }

        $this->type = $this->getImageType($path);

        if (false === $this->type) {
            throw new \Exception('Image file not found or is corrupted.');
        }

        switch($this->type) {
            case '1':
                $this->image = imagecreatefromgif($path);
                break;
            case '2':
                $this->image = imagecreatefromjpeg($path);
                break;
            case '3':
                $this->image = imagecreatefrompng($path);
                break;
            case '18':
                $this->image = imagecreatefromwebp($path);
                break;
            default:
                throw new \Exception('Unsupported file format.');
                break;
        }

        $this->sourceWidth = imagesx($this->image);
        $this->sourceHeight = imagesy($this->image);
    }

    public function getSourceWidth()
    {
        return $this->sourceWidth;
    }
    
    public function getSourceHeight()
    {
        return $this->sourceHeight;
    }

    public function resize($width, $height, $proportional = false)
    {
        if ($proportional) {
            $ratio = $width / imagesx($this->image);
            $height = round(imagesy($this->image) * $ratio);
        }

        $image = $this->create($width, $height);

        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));

        $this->image = $image;

        return $this;
    }

    public function crop($width, $height)
    {
        $ratio = imagesx($this->image) / imagesy($this->image);
        $desiredRatio = $width / $height;

        $tempWidth = $width;
        $tempHeight = round($width / $ratio);

        if ($ratio > $desiredRatio) {
            $tempHeight = $height;
            $tempWidth = round($height * $ratio);
        }

        $temp = $this->create($tempWidth, $tempHeight);

        imagecopyresampled($temp, $this->image, 0, 0, 0, 0, $tempWidth, $tempHeight, imagesx($this->image), imagesy($this->image));
    
        $x0 = round(($tempWidth - $width) / 2);
        $y0 = round(($tempHeight - $height) / 2);

        $this->image = $this->create($width, $height);

        imagecopyresampled($this->image, $temp, 0, 0, $x0, $y0, $width, $height, $width, $height);

        return $this;
    }

    public function cut($width, $height, $sourceX = 0, $sourceY = 0, $sourceWidth = null, $sourceHeight = null)
    {
        $temp = $this->create($width, $height);
        
        imagecopyresampled($temp, $this->image, 0, 0, $sourceX, $sourceY, $width, $height, $sourceWidth ?? imagesx($this->image), $sourceHeight ?? imagesy($this->image));

        $this->image = $temp;

        return $this;
    }

    public function fit($width, $height)
    {
        $background = $this->create($width, $height);

        $ratio = imagesy($this->image) / imagesx($this->image);

        $tempWidth = $width;
        $tempHeight = round($tempWidth * $ratio);

        if ($tempHeight > $height) {
            $tempWidth = round($height / $ratio);
            $tempHeight = $height;
        }

        $image = $this->resize($tempWidth, $tempHeight);

        $sourceWidth = imagesx($image->image);
        $sourceHeight = imagesy($image->image);

        imagecopyresampled(
            $background,
            $image->image,
            round(($width - $sourceWidth) / 2),
            round(($height - $sourceHeight) / 2),
            0,
            0,
            $sourceWidth,
            $sourceHeight,
            $sourceWidth,
            $sourceHeight
        );

        $this->image = $background;

        return $this;    
    }

    public function rotate($angle)
    {
        $this->image = imagerotate($this->image, $angle, 0);

        return $this;
    }

    public function grayscale()
    {
        imagefilter($this->image, \IMG_FILTER_GRAYSCALE);

        return $this;
    }

    public function addWatermark($path, $transparency = 0, $position = 'bottom right', $padding = 5)
    {
        if (file_exists($path)) {
            $type = $this->getImageType($path);

            if (false === $type) {
                return $this;
            }

            switch($type) {
                case '1':
                    $watermark = imagecreatefromgif($path);
                    break;
                case '2':
                    $watermark = imagecreatefromjpeg($path);
                    break;
                case '3':
                    $watermark = imagecreatefrompng($path);
                    break;
                case '18':
                    $watermark = imagecreatefromwebp($path);
                    break;
                default:
                    throw new \Exception('Unsupported watermark file format.');
                    break;
            }
            
            list($v, $h) = explode(' ', $position);

            switch($v) {
                case 'top':
                    $y = $padding;
                    break;
                case 'bottom':
                    $y = imagesy($this->image) - imagesy($watermark) - $padding;
                    break;
                case 'center':
                    $y = (imagesy($this->image) / 2) - (imagesy($watermark) / 2);
                    break;
                default:
                    $y = $padding;
            }

            switch($h) {
                case 'left':
                    $x = $padding;
                    break;
                case 'right':
                    $x = imagesx($this->image) - imagesx($watermark) - $padding;
                    break;
                case 'center':
                    $x = (imagesx($this->image) / 2) - (imagesx($watermark) / 2);
                    break;
                default:
                    $x = $padding;
            }

            imagecopyresampled($this->image, $watermark, $x, $y, 0, 0, imagesx($watermark), imagesy($watermark), imagesx($watermark), imagesy($watermark));
        }

        return $this;
    }

    public function save($path, $quality = 95)
    {
        if ($quality < 10) {
            $quality = 10;
        }
        
        if ($quality > 100) {
            $quality = 100;
        }
        
        switch($this->type) {
            case '1':
                imagegif($this->image, $path);
                break;
            case '2':
                imagejpeg($this->image, $path, $quality);
                break;
            case '3':
                imagepng($this->image, $path, 10 - round($quality/10));
                break;
            case '18':
                imagewebp($this->image, $path, $quality);
                break;
        }
    }   

    private function getImageType($path)
    {
        return exif_imagetype($path);
    }

    private function create($width, $height)
    {
        $image = imagecreatetruecolor($width, $height);

        switch($this->type) {
            case '1':
                imagealphablending($image, false);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagecolortransparent($image, $color);
                imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $color);
                imagesavealpha($image, true);
                break;
            case '2':
                $color = imagecolorallocate($image, 255, 255, 255);
                imagefill($image, 0, 0, $color);
                break;
            case '3':
                imagealphablending($image, false);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $color);
                imagesavealpha($image, true);
                break;
            case '18':
                imagealphablending($image, false);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $color);
                imagesavealpha($image, true);
                break;
        }

        return $image;
    }

}
