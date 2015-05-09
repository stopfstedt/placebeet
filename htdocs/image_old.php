<?php 
ini_set('display_errors', 'on');
$imgPath = dirname(__FILE__) . '/i/beetle.jpg';

$images = array (
);



interface Placebeet_Constants {
	const TEXT_TO_IMAGE_RATIO = 0.5;
	const MIN_FONT_SIZE = 4;
	const PT_TO_PX_RATIO = 0.75;
	const FONT_WIDTH_TO_HEIGHT_RATIO = 0.6;
	const SOURCE_IMAGE_PATH = 'i';
	const CACHE_PATH = '/tmp';
	const DEFAULT_WIDTH = 300;
}

class Placebeet_ImageRequest2
{
	protected $_width = Placebeet_Constants::DEFAULT_WIDTH;
	protected $_height = Placebeet_Constants::DEFAULT_WIDTH;
	protected $_useGreyscale = false;
	
	public function __construct ($width, $height, $useGreyscale = false)
	{
		if (0 > (int) $width) {
			$this->_width = (int) $width;
		}
		if (0 > (int) $width) {
			$this->_height = (int) $height;
		} else {
			$this->_width;
		}
		$this->_useGreyscale = (boolean) $useGreyscale;
	}
	/**
	 * @return the $_width
	 */
	public function getWidth() {
		return $this->_width;
	}

	/**
	 * @return the $_height
	 */
	public function getHeight() {
		return $this->_height;
	}

	/**
	 * @return the $_useGreyscale
	 */
	public function getUseGreyscale() {
		return $this->_useGreyscale;
	}

	/**
	 * @param field_type $_width
	 */
	public function setWidth($_width) {
		$this->_width = $_width;
	}

	/**
	 * @param field_type $_height
	 */
	public function setHeight($_height) {
		$this->_height = $_height;
	}

	/**
	 * @param field_type $_useGreyscale
	 */
	public function setUseGreyscale($_useGreyscale) {
		$this->_useGreyscale = $_useGreyscale;
	}
}

$clean = array();



$watermarkImagesDirPath = dirname(dirname(__FILE__)) . '/w';
//echo $watermarkImagesDirPath; exit;
watermarkImage($imgPath, 0, 0);

function watermarkImage ($src, $width, $height) {
   global $defaultWidth, $defaultHeight, $watermarkImagesDirPath;
   $ratio = .1;
   list($src_width, $src_height) = getimagesize($src);
   $resize_width = (int) ($src_width * $ratio);
   $resize_height = (int) ($src_height * $ratio);
   $watermarkText = "{$resize_width} x {$resize_height}";
   $image_r = imagecreatetruecolor($resize_width, $resize_height);
   //$image_p = imagecreatetruecolor($src_width , $src_height);
   $image = imagecreatefromjpeg($src);
   //imagecopyresampled($image_p, $image, 0, 0, 0, 0, $src_width, $src_height, $src_width, $src_height);
   $black = imagecolorallocate($image, 0, 0, 0);
   $watermark_img = createWatermarkImage($resize_width, $resize_height);
   $watermark_filename = "{$resize_width} x {$resize_height}.png";
   $watermark_img_path = $watermarkImagesDirPath . "/{$watermark_filename}";
   imagepng($watermark_img, $watermark_img_path, 0);
   imagedestroy($watermark_img);
   $watermark_img = imagecreatefrompng($watermark_img_path);
   // resize image
   imagecopyresampled($image_r, $image, 0, 0, 0, 0, $resize_width, $resize_height, $src_width, $src_height);
   // apply watermark
   imagecopymerge($image_r, $watermark_img, 0, 0, 0, 0, $resize_width, $resize_height, 60);
   header('Content-Type: image/jpeg');
   imagejpeg($image_r, null, 100);
   imagedestroy($image);
   imagedestroy($image_r);
};

/**
 * @link http://php.net/manual/en/function.imagettfbbox.php#85266 
 */
function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) {
  $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text);
 
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


function createWatermarkImage ($width, $height) {
	/*image generation code*/
	//create Image 
	$bg = imagecreatetruecolor($width, $height);

	$watermarkText = "{$width} x {$height}";
	//This will make it transparent
	//imagesavealpha($bg, true);

	$trans_colour = imagecolorallocatealpha($bg, 255, 255, 255, 127);

	imagefill($bg, 0, 0, $trans_colour);

	// White text
	$white = imagecolorallocate($bg, 255, 255, 255);
	// Grey Text
	$grey = imagecolorallocate($bg, 128, 128, 128);
	// Black Text
	$black = imagecolorallocate($bg, 0,0,0);

	$font = dirname(__FILE__) . '/i/lucon.ttf';
    $font_size = calculateWatermarkFontSize($width, $height);
    //echo $font_size; exit;

   $textbox = calculateTextBox($watermarkText, $font, $font_size, 0);
   $offset_left = $textbox['left'] + ($width / 2) - ($textbox['width'] / 2);
   $offset_top = $textbox['top'] + ($height / 2) - ($textbox['height'] / 2); 
   imagettftext($bg, $font_size, 0, $offset_left, $offset_top, $black, $font, $watermarkText);
   imagecolortransparent($bg, $white);
   return  $bg;
}

function calculateWatermarkFontSize ($width, $height) {
	$size = $height * Placebeet_Constants::TEXT_TO_IMAGE_RATIO;
	$char_num = strlen("{$width} x {$height}");
	$textLength = $size * $char_num  * Placebeet_Constants::FONT_WIDTH_TO_HEIGHT_RATIO;
	while ($textLength >= $height) {
		$size--;
		$textLength = $size * $char_num  * Placebeet_Constants::FONT_WIDTH_TO_HEIGHT_RATIO;
	}
	//echo $size; exit;
	$size = $size * Placebeet_Constants::PT_TO_PX_RATIO;
	//echo $size; exit;
	return (int) $size;
}

function saveToCache ($cache, $img, $img_id)
{
	// start buffering
	ob_start();
	// output jpeg (or any other chosen) format & quality
	imagejpeg($img, NULL, 100);
	// capture output to string
	$contents = ob_get_contents();
	// end capture
	ob_end_clean();
	$cache->save($img_id, $contents);
}

function createWatermarkFilename ($width, $height)
{
	return "{$width}x{$height}.png";
}