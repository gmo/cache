<?php
namespace GMO\Cache;

interface ICache {
	function get($key);
	function set($key, $value, $expiration = 0);
	function delete($key);
	function deleteAll();
	function increment($key, $value=1, $expiration = 0);
	function decrement($key, $value=1, $expiration = 0);
}