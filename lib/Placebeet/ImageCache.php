<?php 

require_once 'Cache/Lite.php';

class Placebeet_ImageCache
{
	/**
	 * @var Cache_Lite
	 */
	protected $_cache;
	
	/**
	 * @var array
	 */
	protected $_config;
	
	protected $_enabled = true;
	
	public function __construct (array $config, $enabled = true)
	{
		$this->_config = $config;
		$this->_enabled = $enabled;
		$this->_initCache($this->_config);
		
	}
	
	public function getImage ($key)
	{
		if (!$this->_enabled) {
			return false;
		}
		$image = false;
		if (false !== ($data = $this->_cache->get($key))) {
			$image = @imagecreatefromstring($data);
			if (false === $image) {
				$this->_cache->deleteImage($key);
			}
			unset($data);
		}
		return $image;
	}
	
	public function saveImage ($key, $image)
	{
		if (!$this->_enabled) {
			return;
		}
		ob_start();
		imagejpeg($image, NULL, 100);
		$data = ob_get_contents();
		ob_end_clean();
		$this->_cache->save($data, $key);
		unset($data);
	}
	
	/**
	 * Removes an image from the cache
	 * @param string $key the key for the image to remove
	 */
	public function deleteImage ($key)
	{
		$this->_cache->remove($key);
	}

	protected function _initCache (array $config)
	{
		if (!$this->_enabled) {
			return;
		}
		$options = array(
					'cacheDir' => $config['cache_tmp_dir'],
					'lifeTime' => $config['cache_lifetime']);
		$this->_cache = new Cache_Lite($options);
	}
	
	public static function imageRequestToCacheKey (Placebeet_ImageRequest $request)
	{
		return 'placebeet_image_' . md5(serialize($request));
	}
	
	public static function imageRequestToWatermarkCacheKey (Placebeet_ImageRequest $request)
	{
		$a = array();
		$a['width'] = $request->getWidth();
		$a['height'] = $request->getHeight();
		$a['greyscale'] = $request->isGreyscale();
		return 'placebeet_watermark_' . md5(serialize($a));
	}
}