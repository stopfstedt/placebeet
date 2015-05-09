<?php 

class Placebeet_ImageRequestFactory
{
	protected $_config;
	
	protected $_images;
	
	public function __construct (array $config, array $images)
	{
		$this->_config = $config;
		$this->_images = $images;	
	}
	
	public function create (array $input)
	{
		$imageNum = array_key_exists('image', $input) ? (int) $input['image'] : 0;
		if ($imageNum <= 0 || $imageNum > count($this->_images)) {
			$fileName = $this->_getRandomFilename();
		} else {
			$fileName = $this->_images[$imageNum];
		}
		$width = array_key_exists('width', $input) ? (int) $input['width'] : 0;
		if ($width <= 0) {
			$width = $this->_config['image_width_default'];
		}
		$height = array_key_exists('height', $input)? (int) $input['height'] : 0;
		if ($height <= 0) {
			$height = $width;
		}
		$yesValues = array('true', 'TRUE', 't', 'T', 'yes', 'YES', 'y', 'Y', 'on', 'ON');
		$greyscale = array_key_exists('greyscale', $input) ? $input['greyscale'] : false;
		if (false !== $greyscale) {
			$greyscale = in_array($greyscale, $yesValues);
		}
		$watermark = array_key_exists('watermark', $input) ? $input['watermark'] : false;
		if (false !== $watermark) {
			$watermark = in_array($watermark, $yesValues);
		}
		return new Placebeet_ImageRequest($fileName, $width, $height, $greyscale, $watermark);
	}
	
	protected function _getRandomFilename ()
	{
		$numFiles = count($this->_images);
		if ($numFiles) {
			return $this->_images[rand(1, $numFiles)];
		}
		return false;
	}
}
