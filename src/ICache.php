<?php
namespace GMO\Cache;

use Gmo\Common\Deprecated;

Deprecated::cls('\GMO\Cache\ICache');

/**
 * @deprecated Use doctrine/cache instead.
 */
interface ICache {
	function get($key);
	function set($key, $value, $expiration = 0);
	function delete($key);
	function deleteAll();
	function increment($key, $value=1, $expiration = 0);
	function decrement($key, $value=1, $expiration = 0);
}
