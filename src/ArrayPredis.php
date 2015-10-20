<?php

namespace GMO\Cache;

use GMO\Common\Collections\ArrayCollection;
use GMO\Common\String;
use Predis;
use Predis\Command;
use Predis\Command\CommandInterface;
use Predis\NotSupportedException;

/**
 * @method array blpop(array $keys, $timeout)
 * @method array brpop(array $keys, $timeout)
 * @method array brpoplpush($source, $destination, $timeout)
 * @method string lindex($key, $index)
 * @method int linsert($key, $whence, $pivot, $value)
 * @method int llen($key)
 * @method string lpop($key)
 * @method int lpush($key, array $values)
 * @method int lpushx($key, $value)
 * @method array lrange($key, $start, $stop)
 * @method int lrem($key, $count, $value)
 * @method mixed lset($key, $index, $value)
 * @method mixed ltrim($key, $start, $stop)
 * @method string rpop($key)
 * @method string rpoplpush($source, $destination)
 * @method int rpush($key, array $values)
 * @method int rpushx($key, $value)
 * @method int sadd($key, array $members)
 * @method int scard($key)
 * @method array sdiff(array $keys)
 * @method int sdiffstore($destination, array $keys)
 * @method array sinter(array $keys)
 * @method int sinterstore($destination, array $keys)
 * @method int sismember($key, $member)
 * @method array smembers($key)
 * @method int smove($source, $destination, $member)
 * @method string spop($key)
 * @method string srandmember($key, $count = null)
 * @method int srem($key, $member)
 * @method array sscan($key, $cursor, array $options = null)
 * @method array sunion(array $keys)
 * @method int sunionstore($destination, array $keys)
 * @method int zadd($key, array $membersAndScoresDictionary)
 * @method int zcard($key)
 * @method string zcount($key, $min, $max)
 * @method string zincrby($key, $increment, $member)
 * @method int zinterstore($destination, array $keys, array $options = null)
 * @method array zrange($key, $start, $stop, array $options = null)
 * @method array zrangebyscore($key, $min, $max, array $options = null)
 * @method int zrank($key, $member)
 * @method int zrem($key, $member)
 * @method int zremrangebyrank($key, $start, $stop)
 * @method int zremrangebyscore($key, $min, $max)
 * @method array zrevrange($key, $start, $stop, array $options = null)
 * @method array zrevrangebyscore($key, $min, $max, array $options = null)
 * @method int zrevrank($key, $member)
 * @method int zunionstore($destination, array $keys, array $options = null)
 * @method string zscore($key, $member)
 * @method array zscan($key, $cursor, array $options = null)
 * @method array zrangebylex($key, $start, $stop, array $options = null)
 * @method int zremrangebylex($key, $min, $max)
 * @method int zlexcount($key, $min, $max)
 * @method int pfadd($key, array $elements)
 * @method mixed pfmerge($destinationKey, array $sourceKeys)
 * @method int pfcount(array $keys)
 * @method mixed pubsub($subcommand, $argument)
 * @method mixed eval($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed evalsha($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed script($subcommand, $argument = null)
 * @method mixed auth($password)
 * @method string echo ($message)
 * @method mixed select($database)
 * @method mixed bgrewriteaof()
 * @method mixed bgsave()
 * @method mixed client($subcommand, $argument = null)
 * @method mixed config($subcommand, $argument = null)
 * @method int dbsize()
 * @method mixed flushall()
 * @method mixed flushdb()
 * @method array info($section = null)
 * @method int lastsave()
 * @method mixed save()
 * @method mixed slaveof($host, $port)
 * @method mixed slowlog($subcommand, $argument = null)
 * @method array time()
 * @method array command()
 */
class ArrayPredis implements Predis\ClientInterface
{
    protected $data;
    protected $expiring;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->data = new ArrayCollection();
        $this->expiring = new ArrayCollection();
    }

    //region Keys

    protected function doExpire()
    {
        foreach ($this->expiring as $key => $time) {
            if (time() >= $time) {
                unset($this->expiring[$key]);
                unset($this->data[$key]);
            }
        }
    }

    public function del($keys, $key2 = null, $key3 = null)
    {
        $this->doExpire();

        $keys = $this->normalizeArgs(func_get_args());
        $count = 0;
        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                $count++;
            }
            unset($this->data[$key]);
        }

        return $count;
    }

    public function dump($key)
    {
        throw new NotSupportedException();
    }

    public function exists($key)
    {
        return $this->doExists($key);
    }

    protected function doExists($key, $runExpiration = true)
    {
        if ($runExpiration) {
            $this->doExpire();
        }

        return isset($this->data[$key]);
    }

    public function expire($key, $seconds)
    {
        if (!$this->doExists($key)) {
            return false;
        }

        $this->expiring[$key] = time() + $seconds;

        return true;
    }

    public function expireat($key, $timestamp)
    {
        if (!$this->doExists($key)) {
            return false;
        }

        $this->expiring[$key] = $timestamp;

        return true;
    }

    public function pexpire($key, $milliseconds)
    {
        if (!$this->doExists($key)) {
            return false;
        }

        $this->expiring[$key] = time() + ceil($milliseconds / 1000);

        return true;
    }

    public function pexpireat($key, $timestamp)
    {
        if (!$this->doExists($key)) {
            return false;
        }

        $this->expiring[$key] = ceil($timestamp / 1000);

        return true;
    }

    public function keys($pattern)
    {
        $this->doExpire();

        return Redis\Glob::filter($pattern, $this->data->getKeys());
    }

    public function move($key, $db)
    {
        throw new NotSupportedException();
    }

    public function object($subcommand, $key)
    {
        throw new NotSupportedException();
    }

    public function persist($key)
    {
        if (!$this->doExists($key)) {
            return false;
        }

        if (!$this->expiring->containsKey($key)) {
            return false;
        }

        $this->expiring->remove($key);

        return true;
    }

    public function ttl($key)
    {
        if (!$this->doExists($key)) {
            return -2;
        }
        if (!$this->expiring->containsKey($key)) {
            return -1;
        }

        return $this->expiring->get($key) - time();
    }

    public function pttl($key)
    {
        if (!$this->doExists($key)) {
            return -2;
        }
        if (!$this->expiring->containsKey($key)) {
            return -1;
        }

        return ($this->expiring->get($key) - time()) * 1000;
    }

    public function randomkey()
    {
        $this->doExpire();

        return array_rand($this->data->getKeys()->toArray());
    }

    public function rename($key, $target)
    {
        if ($key == $target) {
            throw new Predis\Response\ServerException('ERR source and destination objects are the same');
        }

        if (!$this->doExists($key)) {
            throw new Predis\Response\ServerException('ERR no such key');
        }

        $this->doRename($key, $target);

        return 'OK';
    }

    public function renamenx($key, $target)
    {
        if ($key == $target) {
            throw new Predis\Response\ServerException('ERR source and destination objects are the same');
        }

        if (!$this->doExists($key)) {
            throw new Predis\Response\ServerException('ERR no such key');
        }

        if ($this->doExists($target, false)) {
            return false;
        }

        $this->doRename($key, $target);

        return true;
    }

    protected function doRename($key, $target)
    {
        $item = $this->data->remove($key);
        $this->data->set($target, $item);

        if ($this->expiring->containsKey($key)) {
            $time = $this->expiring->remove($key);
            $this->expiring->set($target, $time);
        }
    }

    public function sort($key, array $options = null)
    {
        throw new NotSupportedException();
    }

    public function type($key)
    {
        throw new NotSupportedException();
    }

    public function scan($cursor, array $options = null)
    {
        throw new NotSupportedException();
    }

    //endregion

    //region Strings

    public function append($key, $value)
    {
        $this->doExpire();

        $this->data[$key] .= $value;

        return $this->strlen($key);
    }

    public function incr($key)
    {
        return $this->incrby($key, 1);
    }

    public function incrbyfloat($key, $increment)
    {
        return $this->incrby($key, $increment);
    }

    public function incrby($key, $value)
    {
        if (!$this->doExists($key)) {
            $this->data[$key] = 0;
        }

        return $this->data[$key] += $value;
    }

    public function decr($key)
    {
        return $this->decrby($key, 1);
    }

    public function decrby($key, $value)
    {
        if (!$this->doExists($key)) {
            $this->data[$key] = 0;
        }

        return $this->data[$key] -= $value;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        $this->doExpire();

        if (!isset($this->data[$key])) {
            return null;
        }

        return $this->data[$key];
    }

    public function getrange($key, $start, $end)
    {
        $value = $this->get($key);
        if ($end < 0) {
            $end = mb_strlen($value) + $end;
        }
        return mb_substr($this->get($key), $start, $end);
    }

    public function getset($key, $value)
    {
        $ret = $this->get($key);
        $this->set($key, $value);

        return $ret;
    }

    /**
     * SET key value [NX|XX] [EX|PX ttl]
     *
     * @param string     $key
     * @param string     $value
     * @param null $expireResolution
     * @param null $expireTTL
     * @param null $flag
     *
     * @return null|string
     */
    public function set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
    {
        $expireResolution = strtolower($expireResolution);
        $expireTTL = strtolower($expireTTL);

        $condition = null;
        $ttl = 0;
        if (in_array($expireResolution, array('nx', 'xx'))) {
            $condition = $expireResolution;
            if ($expireTTL === 'ex') {
                $ttl = $flag;
            } elseif ($expireTTL === 'px') {
                $ttl = ceil($flag / 1000);
            }
        } else {
            if ($expireResolution === 'ex') {
                $ttl = $expireTTL;
            } elseif ($expireResolution === 'px') {
                $ttl = ceil($expireTTL / 1000);
            }
        }

        if ($condition === 'nx' && $this->doExists($key)) {
            return null;
        }
        if ($condition === 'xx' && !$this->doExists($key)) {
            return null;
        }

        $this->data[$key] = $value;
        if ($ttl > 0) {
            $this->expiring[$key] = time() + $ttl;
        }

        return 'OK';
    }

    public function setex($key, $seconds, $value)
    {
        $this->data[$key] = $value;
        $this->expiring[$key] = time() + $seconds;

        return 'OK';
    }

    public function psetex($key, $milliseconds, $value)
    {
        $this->data[$key] = $value;
        $this->expiring[$key] = time() + ceil($milliseconds / 1000);

        return 'OK';
    }

    public function setnx($key, $value)
    {
        if (isset($this->data[$key])) {
            return false;
        }

        $this->data[$key] = $value;

        return true;
    }

    public function msetnx(array $dictionary)
    {
        foreach ($dictionary as $key => $value) {
            if ($this->doExists($key)) {
                return false;
            }
        }

        $this->mset($dictionary);

        return true;
    }

    public function setrange($key, $offset, $value)
    {
        $old = $this->get($key);
        $string = str_pad(mb_substr($old, 0, $offset), $offset, "\0") . $value;

        $this->data[$key] = $string;

        return mb_strlen($string);
    }

    public function mset(array $dictionary)
    {
        $this->data->replace($dictionary);

        return 'OK';
    }

    public function mget($keys)
    {
        $keys = $this->normalizeArgs(func_get_args());
        $this->doExpire();

        $values = array();

        foreach ($keys as $key) {
            $values[] = $this->get($key);
        }

        return $values;
    }

    public function strlen($key)
    {
        $this->doExpire();

        return mb_strlen($this->get($key));
    }

    public function bitcount($key, $start = null, $end = null)
    {
        throw new NotSupportedException();
    }

    public function bitop($operation, $destkey, $key)
    {
        throw new NotSupportedException();
    }

    public function getbit($key, $offset)
    {
        throw new NotSupportedException();
    }

    public function setbit($key, $offset, $value)
    {
        throw new NotSupportedException();
    }

    //endregion

    //region Hashes

    public function hset($key, $field, $value)
    {
        if (!$this->isTraversable($key)) {
            $this->data[$key] = new ArrayCollection();
        }
        $isNew = $this->data[$key]->containsKey($field);
        $this->data[$key][$field] = $value;

        return !$isNew;
    }

    public function hsetnx($key, $field, $value)
    {
        if ($this->hexists($key, $field)) {
            return false;
        }

        $this->hset($key, $field, $value);

        return true;
    }

    public function hget($key, $field)
    {
        $this->doExpire();

        if (isset($this->data[$key][$field])) {
            return $this->data[$key][$field];
        }

        return null;
    }

    public function hlen($key)
    {
        $this->doExpire();

        if (!$this->isTraversable($key)) {
            return 0;
        }

        return count($this->data[$key]);
    }

    public function hdel($key, $fields)
    {
        if (!is_array($fields)) {
            $fields = func_get_args();
            array_shift($fields);
        }

        $this->doExpire();

        if (!$this->isTraversable($key)) {
            return 0;
        }

        $count = 0;
        foreach ($fields as $field) {
            if (isset($this->data[$key][$field])) {
                $count++;
                unset($this->data[$key][$field]);
            }
        }

        return $count;
    }

    public function hkeys($key)
    {
        $this->doExpire();

        if (!$this->isTraversable($key)) {
            return array();
        }

        return $this->data[$key]->getKeys()->toArray();
    }

    public function hvals($key)
    {
        $this->doExpire();

        if (!$this->isTraversable($key)) {
            return array();
        }

        return $this->data[$key]->getValues()->toArray();
    }

    public function hgetall($key)
    {
        if (!$this->isTraversable($key)) {
            return array();
        }

        return $this->data->get($key)->toArray();
    }

    public function hexists($key, $field)
    {
        $this->doExpire();

        return isset($this->data[$key][$field]);
    }

    public function hincrby($key, $field, $increment)
    {
        if (!$this->isTraversable($key)) {
            $this->data[$key] = new ArrayCollection();
        }
        if (!isset($this->data[$key][$field])) {
            $this->data[$key][$field] = 0;
        }

        return $this->data[$key][$field] += $increment;
    }

    public function hincrbyfloat($key, $field, $increment)
    {
        return $this->hincrby($key, $field, $increment);
    }

    public function hmset($key, array $dictionary)
    {
        if (!$this->isTraversable($key)) {
            $this->data[$key] = new ArrayCollection();
        }
        foreach ($dictionary as $hashKey => $value) {
            $this->data[$key][$hashKey] = $value;
        }

        return 'OK';
    }

    public function hmget($key, array $fields)
    {
        $this->doExpire();

        if (!$this->isTraversable($key)) {
            return array_pad(array(), count($fields), null);
        }

        $values = array();
        foreach ($fields as $field) {
            $values[] = $this->hget($key, $field);
        }

        return $values;
    }

    public function hscan($key, $cursor, array $options = null)
    {
        throw new NotSupportedException();
    }

    //endregion

    public function multi()
    {
        throw new NotSupportedException();
    }

    public function exec()
    {
        throw new NotSupportedException();
    }

    public function discard()
    {
        throw new NotSupportedException();
    }

    public function watch($key)
    {
        throw new NotSupportedException();
    }

    public function unwatch()
    {
        throw new NotSupportedException();
    }

    public function subscribe($channels, $callback)
    {
        throw new NotSupportedException();
    }

    public function psubscribe($patterns, $callback)
    {
        throw new NotSupportedException();
    }

    public function publish($channel, $message)
    {
        throw new NotSupportedException();
    }


    public function executeCommand(CommandInterface $command)
    {
        throw new NotSupportedException();
    }

    public function getProfile()
    {
        throw new NotSupportedException();
    }

    public function getOptions()
    {
        throw new NotSupportedException();

    }

    public function connect()
    {
        throw new NotSupportedException();
    }

    public function disconnect()
    {
        throw new NotSupportedException();

    }

    public function getConnection()
    {
        throw new NotSupportedException();

    }

    public function createCommand($method, $arguments = array())
    {
        throw new NotSupportedException();
    }

    public function __call($method, $arguments)
    {
        throw new NotSupportedException();
    }

    protected function isTraversable($key)
    {
        return $this->doExists($key) && ArrayCollection::isTraversable($this->data[$key]);
    }

    protected function normalizeArgs(array $args)
    {
        if (count($args) === 1 && is_array($args[0])) {
            return $args[0];
        }

        return $args;
    }
}
