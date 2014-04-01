<?php
namespace GMO\Cache;

use GMO\Cache\Exception\ConnectionFailureException;
use GMO\Cache\Exception\InvalidSlaveException;
use Predis\Client;

/**
 * Class Redis
 * @package GMO\Common
 * @since 1.9.1
 */
class Redis implements ICache {
	public function __construct($host='localhost', $port=6379, $slaves=array()) {
		$this->host = $host;
		$this->port = $port;
		
		$parameters = array(
			'scheme' => 'tcp',
			'host'   => $host,
			'port'   => (int) $port,
			'alias'  => 'master' );
		$options = null;
		
		if(!empty($slaves)) {
			$parameters = $this->makeSlaveParameters($parameters, $slaves);
			$options = array('replication' => true);
		}
		
		try {
			$this->redis = new Client($parameters, $options);
			$this->redis->connect();
		} catch(\Exception $e) {
			throw new ConnectionFailureException($e->getMessage());
		}
	}
	
	public function get($key) {
		$result = $this->redis->get($key);
		return json_decode($result, true);
	}

	public function set($key, $value, $expiration = 0) {
		// TODO: Replace this with Redis serialization - https://github.com/nrk/predis/issues/29#issuecomment-1202624
		$value = json_encode($value);

		if($expiration === 0) {
			$this->redis->set($key, $value);
		} else {
			$this->redis->setex($key, $expiration, $value);
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

	protected function makeSlaveParameters($parameters, $slaves) {
		if(empty($parameters)) {
			throw new ConnectionFailureException('Configuration for master is missing');
		}
		
		$parameters = array($parameters);
		
		$i = 0;
		foreach($slaves as $slave) {
			if(!isset($slave['host']) || empty($slave['host'])) {
				throw new InvalidSlaveException('Slave is missing field "host"');
			}
			if(!isset($slave['port']) || empty($slave['port']) || !is_numeric($slave['port'])) {
				throw new InvalidSlaveException('Slave is missing field "port"');
			}
			$parameters[] = array(
				'scheme' => 'tcp',
				'host'   => $slave['host'],
				'port'   => (int) $slave['port'],
				'alias'  => 'slave' . ++$i );
		}
	
		return $parameters;
	}
	
	protected $host = null;
	protected $port = null;
	public $redis;
}
