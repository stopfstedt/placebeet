<?php 

class Placebeet_ImageFactory
{
	/**
	 * @var array
	 */
	protected $_config;
	
	/**
	 * 
	 * Enter description here ...
	 * @param array $config
	 */
	public function __construct (array $config)
	{
		$this->_config = $config;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Placebeet_ImageRequest $request
	 * @return mixed|boolean
	 */
	public function create (Placebeet_ImageRequest $request)
	{
		$srcImage = $this->_loadSourceImage($request->getFileName());
		$srcWidth = imagesx($srcImage);
		$srcHeight = imagesy($srcImage);
		$targetWidth = $request->getWidth();
		$targetHeight = $request->getHeight();
		$image = $this->_resizeImage($srcImage, $srcWidth, $srcHeight, $targetWidth, $targetHeight);
		if ($request->isGreyscale()) {
			imagefilter($image, IMG_FILTER_GRAYSCALE);
		}
		
		if ($request->useWatermark()) {
			$padding = (int) $targetWidth * 0.1;
			$fontSize = $targetHeight;
						$font = RESOURCES_DIR . '/fonts/DroidSansMono.ttf';
			$text = "{$targetWidth} x {$targetHeight}";
			$textBox = $this->_calculateTextBox($text, $font, $fontSize, 0);
			while ($fontSize > 0 && ($textBox['width'] + $padding)  > $targetWidth) {
				$fontSize--;
				$textBox = $this->_calculateTextBox($text, $font, $fontSize, 0);
			}
			$black = imagecolorallocate($image, 0, 0, 0);
			$white = imagecolorallocate($image, 255, 255, 255);
			$yellow = imagecolorallocate($image, 255, 255, 0);
			$textColor = $request->isGreyscale() ? $yellow : $white;

			$offsetLeft = $textBox['left'] + ($targetWidth / 2) - ($textBox['width'] / 2);
   			$offsetTop = $textBox['top'] + ($targetHeight / 2) - ($textBox['height'] / 2);
			$this->_imagettfstroketext($image, $fontSize, 0, $offsetLeft, $offsetTop, $textColor, $black, $font, $text, 1);
		}
		return $image;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $fileName
	 * @return mixed|boolean
	 */
	
	protected function _loadSourceImage ($fileName)
	{
		$path = APP_ROOT . '/' . $this->_config['images_path'] . '/' . basename($fileName);
		return @imagecreatefromjpeg($path);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param mixed $image
	 * @param int $srcWidth
	 * @param int $srcHeight
	 * @param int $targetWidth
	 * @param int $targetHeight
	 * @return mixed|boolean
	 */
	public function _resizeImage ($image, $srcWidth, $srcHeight, $targetWidth, $targetHeight)
	{
		$targetWidthHeightRatio = $targetWidth / $targetHeight;
		$srcWidthHeightRatio = $srcWidth / $srcHeight;
		$resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
		$srcOffsetWidth = 0;
		$srcOffsetHeight = 0;
		if ($targetWidthHeightRatio != $srcWidthHeightRatio) {
			// crop image to meet ratio
			$adjustedWidth = (int) abs($srcHeight * $targetWidthHeightRatio); 
			$adjustedHeight = (int) abs ($srcWidth / $targetWidthHeightRatio);
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
		imagecopyresampled($resizedImage, $image, 0, 0, $srcOffsetWidth, $srcOffsetHeight, $targetWidth, $targetHeight, $srcWidth, $srcHeight);
		return $resizedImage;	
	}
	
	/**
 	 * Writes the given text with a border into the image using TrueType fonts.
 	 * @author John Ciacia
 	 * @param image An image resource
 	 * @param size The font size
 	 * @param angle The angle in degrees to rotate the text
 	 * @param x Upper left corner of the text
 	 * @param y Lower left corner of the text
 	 * @param textcolor This is the color of the main text
	 * @param strokecolor This is the color of the text border
 	 * @param fontfile The path to the TrueType font you wish to use
 	 * @param text The text string in UTF-8 encoding
 	 * @param px Number of pixels the text border will be
 	 * @see http://us.php.net/manual/en/function.imagettftext.php
 	 * @link http://www.johnciacia.com/2010/01/04/using-php-and-gd-to-add-border-to-text/
 	 */
	protected function _imagettfstroketext(&$image, $size, $angle, $x, $y, $textcolor, $strokecolor, $fontfile, $text, $px) {
    	for ($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++) {
        	for ($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++) {
            	$bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
        	}
    	}
   		return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
	}
	
	/**
	 * 
 	 * @link http://php.net/manual/en/function.imagettfbbox.php#85266 
 	 */
	protected function _calculateTextBox($text, $fontFile, $fontSize, $fontAngle) {
  		$rect = imagettfbbox($fontSize, $fontAngle, $fontFile, $text);
 
  		$minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
  		$maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
  		$minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
  		$maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));

  		return array(
    		"left"   => abs($minX),
    		"top"    => abs($minY),
    		"width"  => $maxX - $minX,
    		"height" => $maxY - $minY,
    		"box"    => $rect
  		);
	}
}