<?php 

class Placebeet_ImageServer
{
	protected $_config;
	/**
	 * @var Placebeet_ImageCache
	 */
	protected $_cache;
	
	/**
	 * @var Placebeet_ImageFactory
	 */
	protected $_imageFactory;
	
	
	public function __construct (array $config, Placebeet_ImageCache $cache = null)
	{
		$this->_config = $config;
		if (!isset($cache)) {
			$this->_cache = new Placebeet_ImageCache($config, $config['cache_enabled']);
		} else {
			$this->_cache = $cache;
		}
	}
	
	public function getImage (Placebeet_ImageRequest $request)
	{
		$key = Placebeet_ImageCache::imageRequestToCacheKey($request);
		if (false === ($image = $this->_cache->getImage($key))) {
			$image = $this->_getImageFactory()->create($request);
			if (false !== $image) {
				$this->_cache->saveImage($key, $image);
			}	
		}
		return $image;
	}
	
	protected function _getImageFactory ()
	{
		if (!isset($this->_imageFactory)) {
			$this->_imageFactory = new Placebeet_ImageFactory($this->_config);
		}
		return $this->_imageFactory;
	}
}