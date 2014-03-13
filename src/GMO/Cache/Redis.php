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
		$result = $this->redis->get($key);
		if($result === NULL) {
			return NULL;
		}
		
		return json_decode($result, true);
	}
	
	public function set($key, $value, $expiration = 0) {
		// TODO: Replace this with Redis serialization - https://github.com/nrk/predis/issues/29#issuecomment-1202624
		$value = json_encode($value);
		
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
		return $this->redis->incrby($key, $value);
	}
	
	public function decrement($key, $value=1, $expiration = 0) {
		return $this->redis->decrby($key, $value);
	}
		
	protected $host = null;
	protected $port = null;
	protected $memcache;
}