<?php
namespace GMO\Cache;

use GMO\Cache\Exception\ConnectionFailureException;

/**
 * Class Redis
 * @package GMO\Common
 * @since 1.9.1
 */
class Redis implements ICache {
	public function __construct($host='localhost', $port=6379) {
		$this->host = $host;
		$this->port = $port;
		try {
			$this->redis = new \Predis\Client(array(
				'scheme' => 'tcp',
				'host'   => $host,
				'port'   => (int) $port ));
			$this->redis->connect();
		} catch(\Exception $e) {
			throw new ConnectionFailureException($e->getMessage());
		}
	}

	public function get($key) {
		return $this->redis->get($key);
	}
	
	public function set($key, $value, $expiration = 0) {
		$this->redis->set($key, $value);
		if(!empty($expiration)) {
			$this->redis->expire($key, $expiration);
		}
	}
	
	public function delete($key) {
		$this->redis->del($key);
	}
	
	public function deleteAll() {
		$this->redis->flushall();
	}
	
	public function increment($key, $value=1, $expiration = 0) {
		$this->redis->incrby($key, $value);
	}
	
	public function decrement($key, $value=1, $expiration = 0) {
		$this->redis->decrby($key, $value);
	}
	
	protected function initCounter($key, $expiration) {
		if($this->get($key) === NULL) {
			$this->set($key, 0, $expiration);
		}
	}
		
	protected $host = null;
	protected $port = null;
	protected $memcache;
}