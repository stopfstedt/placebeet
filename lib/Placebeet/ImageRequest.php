<?php 
/**
 * 
 * Enter description here ...
 * @author Stefan Topfstedt <stefan@destruct-o-bot.com>
 *
 */

/**
 * 
 * Enter description here ...
 * @author Stefan Topfstedt <stefan@destruct-o-bot.com>
 *
 */
class Placebeet_ImageRequest
{
	protected $_fileName;
	protected $_width;
	protected $_height;
	protected $_greyscale;
	protected $_watermark;
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $fileName
	 * @param int $width
	 * @param int $height
	 * @param boolean $greyscale
	 * @param boolean $watermark
	 */
	public function __construct ($fileName, $width, $height, $greyscale = false, $watermark = false)
	{
		$this->_fileName = $fileName;
		$this->_width = $width;
		$this->_height = $height;
		$this->_greyscale = $greyscale;
		$this->_watermark = $watermark;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getFileName ()
	{
		return $this->_fileName;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getWidth ()
	{
		return $this->_width;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getHeight ()
	{
		return $this->_height;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isGreyscale ()
	{
		return $this->_greyscale;
	}
	
	public function useWatermark ()
	{
		return $this->_watermark;
	}
}