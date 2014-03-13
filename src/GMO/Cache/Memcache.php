<?php
namespace GMO\Cache;

use GMO\Cache\Exception\ConnectionFailureException;
use GMO\Cache\Exception\DependencyMissingException;

/**
 * Class Memcache
 * @package GMO\Common
 * @since 1.9.1
 */
class Memcache implements ICache {
	public function __construct($host='localhost', $port=11211) {
		$this->host = $host;
		$this->port = $port;
		$this->memcache = new \Memcache();
		if(!class_exists('Memcache')) {
			throw new DependencyMissingException('Class \Memcache does not exist');
		}
		
		$connectionSuccess = $this->memcache->connect($this->host, $this->port);
		if(empty($connectionSuccess)) {
			throw new ConnectionFailureException('Unable to connect to Memcache server');
		}
	}

	public function get($key) {
		$result = $this->memcache->get($key);
		if($result === false) {
			return NULL;
		}
		
		return $result;
	}
	
	public function set($key, $value, $expiration = 0) {
		if(!is_string($value)) {
			$value = json_encode($value);
		}
		
		$this->memcache->set($key, $value, false, $expiration);
	}
	
	public function delete($key) {
		$this->memcache->delete($key);
	}
	
	public function deleteAll() {
		$this->memcache->flush();
	}
	
	public function increment($key, $value=1, $expiration = 0) {
		$result = $this->memcache->increment($key, $value);
		if($result === false) {
			$this->memcache->add($key, 0, false, $expiration);
			$result = $this->memcache->increment($key, $value);
		}
		return $result;
	}
	
	public function decrement($key, $value=1, $expiration = 0) {
		$result = $this->memcache->decrement($key, $value);
		if($result === false) {
			$this->memcache->add($key, 0, false, $expiration);
			$result = $this->memcache->decrement($key, $value);
		}
		return $result;
	}
		
	protected $host = null;
	protected $port = null;
	protected $memcache;
}