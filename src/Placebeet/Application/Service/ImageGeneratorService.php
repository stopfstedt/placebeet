<?php

namespace Placebeet\Application\Service;

/**
 * Image generator service.
 *
 * Class ImageFactoryService
 * @package Placebeet\Application\Service
 */
class ImageGeneratorService
{
    protected $_resourcesDir;

    /**
     * @param string $resourcesDir directory path to the resources dir.
     */
    public function __construct ($resourcesDir)
    {
        $this->_resourcesDir = $resourcesDir;
    }

    /**
     * Generates an image from a given source images and properties.
     * @param string $srcImageName the name of the source image file.
     * @param int $targetWidth the target image width
     * @param int $targetHeight the target image height
     * @param bool $useGreyscale set to TRUE to generate greyscale image
     * @param bool $useWatermark set to TRUE to apply watermark text to image
     * @return resource|bool The generated image, or FALSE on errors.
     */
    public function create ($srcImageName, $targetWidth, $targetHeight,
      $useGreyscale = FALSE, $useWatermark = FALSE)
    {
        $srcImage = $this->_loadSourceImage($srcImageName);
        if (FALSE === $srcImage) {
            return FALSE;
        }
        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);

        $image = $this->_resizeImage($srcImage, $srcWidth, $srcHeight,
          $targetWidth, $targetHeight);

        if ($useGreyscale) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }

        if ($useWatermark) {
            $padding = (int) $targetWidth * 0.1;
            $fontSize = $targetHeight;
            $font =  $this->_resourcesDir . '/fonts/DroidSansMono.ttf';
            $text = "{$targetWidth} x {$targetHeight}";
            $textBox = $this->_calculateTextBox($text, $font, $fontSize);
            while ($fontSize > 0 &&
              ($textBox['width'] + $padding) > $targetWidth) {
                $fontSize--;
                $textBox = $this->_calculateTextBox($text, $font, $fontSize);
            }
            $black = imagecolorallocate($image, 0, 0, 0);
            $white = imagecolorallocate($image, 255, 255, 255);
            $yellow = imagecolorallocate($image, 255, 255, 0);
            $textColor = $useGreyscale ? $yellow : $white;

            $offsetLeft = abs($textBox['left'] + ($targetWidth / 2) -
              ($textBox['width'] / 2));
            $offsetTop = abs($textBox['top'] + ($targetHeight / 2) -
              ($textBox['height'] / 2));
            $this->_applyTextToImage($image, $fontSize, $offsetLeft,
              $offsetTop, $textColor, $black, $font, $text, 1);
        }
        return $image;
    }

    /**
     * Loads an image by its given name from file.
     * @param string $fileName the image file name.
     * @return resource|bool the image resource, or FALSE on error.
     */
    protected function _loadSourceImage ($fileName)
    {
        $path = $this->_resourcesDir . '/images/' . basename(
            $fileName
          );

        return @imagecreatefromjpeg($path);
    }

    /**
     * Re-sizes a given image.
     *
     * @param resource $image the give source image.
     * @param int $srcWidth the image source width .
     * @param int $srcHeight the image source height.
     * @param int $targetWidth the target source width.
     * @param int $targetHeight the target source height.
     *
     * @return resource|bool the re-sized image, or FALSE on errors.
     */
    public function _resizeImage (
      $image,
      $srcWidth,
      $srcHeight,
      $targetWidth,
      $targetHeight
    ) {
        $targetWidthHeightRatio = $targetWidth / $targetHeight;
        $srcWidthHeightRatio = $srcWidth / $srcHeight;
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if (false === $resizedImage) {
            return false;
        }
        $srcOffsetWidth = 0;
        $srcOffsetHeight = 0;
        if ($targetWidthHeightRatio != $srcWidthHeightRatio) {
            // crop image to meet ratio
            $adjustedWidth = (int) abs($srcHeight * $targetWidthHeightRatio);
            $adjustedHeight = (int) abs($srcWidth / $targetWidthHeightRatio);
            $deltaWidth = $srcWidth - $adjustedWidth;
            $deltaHeight = $srcHeight - $adjustedHeight;
            if (0 < $deltaWidth) {
                $srcOffsetWidth = (int) ($deltaWidth / 2);
                $srcWidth = $adjustedWidth;

            } elseif (0 < $deltaHeight) {
                $srcOffsetHeight = (int) ($deltaHeight / 2);
                $srcHeight = $adjustedHeight;
            }
        }
        imagecopyresampled($resizedImage, $image, 0, 0, $srcOffsetWidth,
          $srcOffsetHeight, $targetWidth, $targetHeight, $srcWidth, $srcHeight);

        return $resizedImage;
    }

    /**
     * Writes the given text with a border into the image using TrueType fonts.
     * @author John Ciacia
     * @param resource $image An image resource
     * @param int $size The font size
     * @param int $x Upper left corner of the text
     * @param int $y Lower left corner of the text
     * @param string $textColor This is the color of the main text
     * @param string $strokeColor This is the color of the text border
     * @param string $fontFile The path to the TrueType font you wish to use
     * @param string $text The text string in UTF-8 encoding
     * @param int $px Number of pixels the text border will be
     * @see http://us.php.net/manual/en/function.imagettftext.php
     * @link http://www.johnciacia.com/2010/01/04/using-php-and-gd-to-add-border-to-text/
     */
    protected function _applyTextToImage(&$image, $size, $x, $y, $textColor,
      $strokeColor, $fontFile, $text, $px) {
        $minX = $x - $px;
        $maxX = $x + $px;
        $minY = $y - $px;
        $maxY = $y + $px;
        for ($i = $minX; $i <= $maxX; $i++) {
            for ($j = $minY; $j <= $maxY; $j++) {
                imagettftext($image, $size, 0, $i, $j, $strokeColor,
                  $fontFile, $text);
            }
        }
        imagettftext($image, $size, 0, $x, $y, $textColor, $fontFile, $text);
    }

    /**
     * Calculates the dimensions of a textbox for a given text and used font.
     * @param string $text the text
     * @param resource $fontFile the font file
     * @param int $fontSize the font size
     * @return array an array containing the calculated textbox attributes.
     * @link http://php.net/manual/en/function.imagettfbbox.php#85266
     */
    protected function _calculateTextBox($text, $fontFile, $fontSize)
    {
        $rect = imagettfbbox($fontSize, 0, $fontFile, $text);

        $minX = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
        $maxX = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
        $minY = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
        $maxY = max(array($rect[1], $rect[3], $rect[5], $rect[7]));

        return array(
          "left" => abs($minX),
          "top" => abs($minY),
          "width" => $maxX - $minX,
          "height" => $maxY - $minY,
          "box" => $rect
        );
    }
}

