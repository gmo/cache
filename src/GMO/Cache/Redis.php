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

	public function deleteMultiple($args, $_ = null) {
		$keys = static::normalizeArgs(func_get_args());
		$this->redis->del($keys);
	}

	public function deleteAll() {
		$this->redis->flushdb();
	}
	
	public function increment($key, $value=1, $expiration = 0) {
		return $this->redis->incrby($key, $value);
	}
	
	public function decrement($key, $value=1, $expiration = 0) {
		return $this->redis->decrby($key, $value);
	}
	
	public function selectDb($database) {
		return $this->redis->select($database);
	}

	//region Hash commands
	public function setHash($key, $field, $value) {
		$this->redis->hset($key, $field, $value);
	}

	public function setMultipleHash($key, array $kvArray) {
		$this->redis->hmset($key, $kvArray);
	}

	public function getHash($key, $field) {
		return $this->redis->hget($key, $field);
	}

	public function getAllHash($key) {
		return $this->redis->hgetall($key);
	}

	public function incrementHash($key, $field, $value = 1) {
		return $this->redis->hincrby($key, $field, $value);
	}
	//endregion

	//region List commands
	public function prependList($key, $value) {
		$this->redis->lpush($key, $value);
	}

	public function appendList($key, $value) {
		$this->redis->rpush($key, $value);
	}

	public function trimList($key, $start, $stop) {
		$this->redis->ltrim($key, $start, $stop);
	}

	public function getList($key, $start = 0, $stop = -1) {
		return $this->redis->lrange($key, $start, $stop);
	}

	public function setList($key, array $data) {
		$this->redis->pipeline(function($pipe) use ($key, $data) {
			$pipe->del($key);
			$pipe->rpush($key, $data);
		});
	}
	//endregion

	public function publish($channel, $message) {
		$this->redis->publish($channel, $message);
	}

	public function pipeline($callback) {
		return $this->redis->pipeline($callback);
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

	private static function normalizeArgs(array $args) {
		return (count($args) === 1 && is_array($args[0])) ? $args[0] : $args;
	}

	protected $host = null;
	protected $port = null;
	public $redis;
}
