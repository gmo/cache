<?php
namespace GMO\Cache;

use Gmo\Common\Deprecated;

Deprecated::cls('\GMO\Cache\CacheKeys');

/**
 * @deprecated
 */
final class CacheKeys {

	const REDIS_HOST = 'redis.host';
	const REDIS_PORT = 'redis.port';
	const REDIS_NEW = 'redis.new';
	const REDIS = 'redis';

	private function __construct() { }
}
