<?php
namespace GMO\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ClearableCache;

/**
 * Wraps a Doctrine Cache instance in an ICache interface.
 * 
 * @deprecated Use doctrine/cache instead.
 */
class DoctrineProxy implements ICache
{
    /** @var Cache */
    protected $cache;

    /**
     * Constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Gets an entry from the cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->fetch($key);
    }

    /**
     * Puts data into cache.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expiration Sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     */
    public function set($key, $value, $expiration = 0)
    {
        $this->cache->save($key, $value, $expiration);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $key
     */
    public function delete($key)
    {
        $this->cache->delete($key);
    }

    /**
     * Clears all cache entries.
     *
     * @throws \RuntimeException If the cache instance is not an instanceof Doctrine\Common\Cache\ClearableCache
     */
    public function deleteAll()
    {
        if ($this->cache instanceof ClearableCache) {
            $this->cache->deleteAll();
        }

        throw new \RuntimeException('Cache given is not an instanceof Doctrine\Common\Cache\ClearableCache');
    }

    /**
     * Increment a key.
     *
     * Note: This is not atomic. It is faked in PHP.
     *
     * @param string $key
     * @param int    $value
     * @param int    $expiration
     */
    public function increment($key, $value = 1, $expiration = 0)
    {
        $new = intval($this->get($key));
        $new += $value;
        $this->set($key, $new, $expiration);
    }

    /**
     * Decrement a key.
     *
     * Note: This is not atomic. It is faked in PHP.
     *
     * @param string $key
     * @param int    $value
     * @param int    $expiration
     */
    public function decrement($key, $value = 1, $expiration = 0)
    {
        $new = intval($this->get($key));
        $new -= $value;
        $this->set($key, $new, $expiration);
    }
}
