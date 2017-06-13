<?php
namespace GMO\Cache;

use Gmo\Common\Deprecated;
use GMO\DependencyInjection\ServiceProviderInterface;
use Pimple;

Deprecated::cls('\GMO\Cache\CacheServiceProvider');

/**
 * @deprecated
 */
class CacheServiceProvider implements ServiceProviderInterface {

	/** @inheritdoc */
	public function register(Pimple $container) {

		$container[CacheKeys::REDIS_HOST] = 'localhost';
		$container[CacheKeys::REDIS_PORT] = 6379;

		$container[CacheKeys::REDIS_NEW] = function() use ($container) {
			return new Redis($container[CacheKeys::REDIS_HOST], $container[CacheKeys::REDIS_PORT]);
		};
		$container[CacheKeys::REDIS] = $container->share(function() use ($container) {
			return $container[CacheKeys::REDIS_NEW];
		});
	}

	/** @inheritdoc */
	public function boot(Pimple $container) { }
}
