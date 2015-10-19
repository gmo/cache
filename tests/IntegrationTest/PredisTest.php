<?php

namespace GMO\Cache\Tests\IntegrationTest;

use Predis;

class PredisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Predis\ClientInterface */
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->client->flushdb();
    }

    public function createClient()
    {
        $client = new Predis\Client();
        $client->select(10);
        return $client;
    }

    //region Keys

    public function testDelete()
    {
        $this->client->set('foo', 'bar');
        $this->client->set('hello', 'world');

        $this->client->del('foo', 'hello');
        $this->assertFalse($this->client->exists('foo'));
        $this->assertFalse($this->client->exists('hello'));

        $this->client->set('foo', 'bar');
        $this->client->set('hello', 'world');

        $this->client->del(array('foo', 'hello'));
        $this->assertFalse($this->client->exists('foo'));
        $this->assertFalse($this->client->exists('hello'));
    }

    public function testDump()
    {
        $this->markTestSkipped();
    }

    public function testExists()
    {
        $this->assertFalse($this->client->exists('foo'));
        $this->client->set('foo', 'bar');
        $this->assertTrue($this->client->exists('foo'));
    }

    public function testExpire()
    {
        $this->client->set('foo', 'bar');
        $this->assertTrue($this->client->expire('foo', 1));
        usleep(1.5e+6);
        $this->assertFalse($this->client->exists('foo'));
    }

    public function testPreciseExpire()
    {
        $this->client->set('foo', 'bar');
        $this->assertTrue($this->client->pexpire('foo', 500));
        usleep(1.5e+6);
        $this->assertFalse($this->client->exists('foo'));
    }

    public function testExpireAt()
    {
        $this->client->set('foo', 'bar');
        $this->client->expireat('foo', time() + 1);
        usleep(1.5e+6);
        $this->assertFalse($this->client->exists('foo'));
    }

    public function testPreciseExpireAt()
    {
        $this->client->set('foo', 'bar');
        $this->client->pexpireat('foo', (time() + 1) * 1000);
        usleep(1.5e+6);
        $this->assertFalse($this->client->exists('foo'));
    }

    public function testKeys()
    {
        $this->assertEquals(array(), $this->client->keys('derp'));

        $this->client->mset(array(
            'color:red'   => 'red',
            'color:blue'  => 'blue',
            'color:green' => 'green',
            'foo'         => 'bar',
        ));

        $expected = array(
            'color:red',
            'color:blue',
            'color:green',
        );
        $this->assertArraySimilar($expected, $this->client->keys('color*'));

        $expected[] = 'foo';
        $this->assertArraySimilar($expected, $this->client->keys('*'));
    }

    protected function assertArraySimilar(array $expected, array $actual)
    {
        $this->assertSame(array_diff($expected, $actual), array_diff($actual, $expected));
    }

    public function testMove()
    {
        $this->markTestSkipped();
    }

    public function testObject()
    {
        $this->markTestSkipped();
    }

    public function testPersist()
    {
        $this->client->set('foo', 'bar');
        $this->client->expire('foo', 10);
        $this->client->persist('foo');
        $this->assertSame(-1, $this->client->ttl('foo'));
    }

    public function testTtl()
    {
        $this->client->set('foo', 'bar');
        $this->client->expire('foo', 20);

        $this->assertThat(
            $this->client->ttl('foo'),
            $this->logicalAnd($this->greaterThan(0), $this->lessThanOrEqual(20))
        );
    }

    public function testPreciseTtl()
    {
        $this->client->set('foo', 'bar');
        $this->client->expire('foo', 20);

        $this->assertThat(
            $this->client->pttl('foo'),
            $this->logicalAnd($this->greaterThan(0), $this->lessThanOrEqual(20000))
        );
    }

    public function testRandomKey()
    {
        $items = array(
            'hello' => 'world',
            'foo'   => 'bar',
        );
        $this->client->mset($items);

        $this->assertContains($this->client->randomkey(), array_keys($items));
    }

    public function testRename()
    {
        $this->client->set('foo', 'bar');
        try {
            $this->assertEquals('OK', $this->client->rename('foo', 'foo'));
            $this->fail('rename should throw exception when source and destination are the same');
        } catch (Predis\Response\ServerException $e) {
            if ($e->getMessage() !== 'ERR source and destination objects are the same') {
                throw $e;
            }
        }

        $this->assertEquals('OK', $this->client->rename('foo', 'baz'));
        $this->assertTrue($this->client->exists('baz'));
        $this->assertFalse($this->client->exists('foo'));
    }

    public function testRenameNx()
    {
        $this->client->set('hello', 'world');
        $this->client->set('foo', 'bar');
        try {
            $this->assertEquals('OK', $this->client->renamenx('foo', 'foo'));
            $this->fail('rename should throw exception when source and destination are the same');
        } catch (Predis\Response\ServerException $e) {
            if ($e->getMessage() !== 'ERR source and destination objects are the same') {
                throw $e;
            }
        }

        $this->assertFalse($this->client->renamenx('foo', 'hello'));

        $this->assertEquals('OK', $this->client->rename('foo', 'baz'));
        $this->assertTrue($this->client->exists('baz'));
        $this->assertFalse($this->client->exists('foo'));
    }

    public function testRestore()
    {
        $this->markTestSkipped();
    }

    public function testScan()
    {
        $this->markTestSkipped();
    }

    public function testSort()
    {
        $this->markTestSkipped();
    }

    public function testType()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Strings

    public function testAppend()
    {
        $this->assertSame(5, $this->client->append('foo', 'Hello'));
        $this->assertSame(11, $this->client->append('foo', ' World'));
        $this->assertSame('Hello World', $this->client->get('foo'));
    }

    public function testBitCount()
    {
        $this->markTestSkipped();
    }

    public function testBitOp()
    {
        $this->markTestSkipped();
    }

    public function testDecrement()
    {
        $this->assertEquals(-1, $this->client->decr('foo'));
        $this->assertEquals(-2, $this->client->decr('foo'));
    }

    public function testDecrementBy()
    {
        $this->assertEquals(-2, $this->client->decrby('foo', 2));
        $this->assertEquals(-4, $this->client->decrby('foo', 2));
    }

    public function testGetBit()
    {
        $this->markTestSkipped();
    }

    public function testGetRange()
    {
        $this->assertSame('', $this->client->getrange('foo', 0, -1));
        $this->client->set('foo', 'Hello World');
        $this->assertSame('ello Worl', $this->client->getrange('foo', 1, -2));
        $this->assertSame('World', $this->client->getrange('foo', -5, -1));
    }

    public function testGetSet()
    {
        $this->assertNull($this->client->getset('foo', 'bar'));
        $this->assertSame('bar', $this->client->getset('foo', 'derp'));
        $this->assertSame('derp', $this->client->get('foo'));
    }

    public function testIncrement()
    {
        $this->assertEquals(1, $this->client->incr('foo'));
        $this->assertEquals(2, $this->client->incr('foo'));
    }

    public function testIncrementByFloat()
    {
        $this->assertEquals(1.5, $this->client->incrbyfloat('foo', 1.5));
        $this->assertEquals(3.0, $this->client->incrbyfloat('foo', 1.5));
    }

    public function testIncrementBy()
    {
        $this->assertEquals(2, $this->client->incrby('foo', 2));
        $this->assertEquals(4, $this->client->incrby('foo', 2));
    }

    public function testMultipleSet()
    {
        $this->client->mset(array(
            'foo'   => 'bar',
            'hello' => 'world',
        ));
        $this->assertSame('bar', $this->client->get('foo'));
        $this->assertSame('world', $this->client->get('hello'));
    }

    public function testMultipleGet()
    {
        $this->client->mset(array(
            'foo'   => 'bar',
            'hello' => 'world',
        ));
        $this->assertEquals(array('bar', 'world'), $this->client->mget('foo', 'hello'));
    }

    public function testMultipleSetNx()
    {
        $this->assertTrue($this->client->msetnx(array(
            'foo'   => 'bar',
            'hello' => 'world',
        )));
        $this->assertFalse($this->client->msetnx(array(
            'foo' => 'baz',
            'red' => 'blue',
        )));
        $this->assertSame('bar', $this->client->get('foo'));
        $this->assertSame('world', $this->client->get('hello'));
        $this->assertFalse($this->client->exists('red'));
    }

    public function testGet()
    {
        $this->assertNull($this->client->get('foo'));
        $this->client->set('foo', 'bar');
        $this->assertSame('bar', $this->client->get('foo'));
    }

    public function testSet()
    {
        $this->assertEquals('OK', $this->client->set('foo', 'bar'));
        $this->assertEquals('OK', $this->client->set('foo', 'baz'));
        $this->assertSame('baz', $this->client->get('foo'));

        // EX expire time
        $this->assertEquals('OK', $this->client->set('foo', 'bar', 'EX', 20));
        $this->assertThat(
            $this->client->ttl('foo'),
            $this->logicalAnd($this->greaterThan(0), $this->lessThanOrEqual(20))
        );

        // PX expire time
        $this->client->del('foo');
        $this->assertEquals('OK', $this->client->set('foo', 'bar', 'PX', 20000));
        $this->assertThat(
            $this->client->ttl('foo'),
            $this->logicalAnd($this->greaterThan(0), $this->lessThanOrEqual(20))
        );

        $this->client->del('foo');
        $this->assertNull($this->client->set('foo', 'bar', 'XX'));
        $this->assertEquals('OK', $this->client->set('foo', 'baz', 'NX'));
        $this->assertNull($this->client->set('foo', 'blue', 'NX'));
        $this->assertEquals('OK', $this->client->set('foo', 'red', 'XX'));
    }

    public function testSetBit()
    {
        $this->markTestSkipped();
    }

    public function testSetEx()
    {
        $this->assertEquals('OK', $this->client->setex('foo', 20, 'bar'));
        $this->assertEquals('OK', $this->client->setex('foo', 20, 'baz'));
        $this->assertSame('baz', $this->client->get('foo'));
        $this->assertThat(
            $this->client->ttl('foo'),
            $this->logicalAnd($this->greaterThan(0), $this->lessThanOrEqual(20))
        );
    }

    public function testPreciseSetEx()
    {
        $this->assertEquals('OK', $this->client->psetex('foo', 20000, 'bar'));
        $this->assertEquals('OK', $this->client->psetex('foo', 20000, 'baz'));
        $this->assertSame('baz', $this->client->get('foo'));
        $this->assertThat(
            $this->client->ttl('foo'),
            $this->logicalAnd($this->greaterThan(0), $this->lessThanOrEqual(20))
        );
    }

    public function testSetNx()
    {
        $this->assertTrue($this->client->setnx('foo', 'bar'));
        $this->assertFalse($this->client->setnx('foo', 'bar'));
    }

    public function testSetRange()
    {
        $this->assertSame(9, $this->client->setrange('foo', 4, 'World'));
        $this->assertSame("\0\0\0\0World", $this->client->get('foo'));

        $this->client->set('foo', 'Hello World');
        $this->assertSame(14, $this->client->setrange('foo', 6, 'Universe'));
        $this->assertSame('Hello Universe', $this->client->get('foo'));
    }

    public function testStringLength()
    {
        $this->assertSame(0, $this->client->strlen('foo'));
        $this->client->set('foo', 'bar');
        $this->assertSame(3, $this->client->strlen('foo'));
    }

    //endregion

    //region Hashes

    public function testHashSet()
    {
        $this->assertTrue($this->client->hset('foo', 'bar', 'hello'));
        $this->assertEquals('hello', $this->client->hget('foo', 'bar'));

        $this->assertFalse($this->client->hset('foo', 'bar', 'world'));
        $this->assertEquals('world', $this->client->hget('foo', 'bar'));
    }

    public function testHashSetNx()
    {
        $this->assertTrue($this->client->hsetnx('foo', 'bar', 'hello'));
        $this->assertEquals('hello', $this->client->hget('foo', 'bar'));

        $this->assertFalse($this->client->hsetnx('foo', 'bar', 'world'));
        $this->assertEquals('hello', $this->client->hget('foo', 'bar'));
    }

    public function testHashGet()
    {
        $this->assertNull($this->client->hget('foo', 'bar'));

        $this->client->hset('foo', 'bar', 'hello');
        $this->assertEquals('hello', $this->client->hget('foo', 'bar'));
    }

    public function testHashLength()
    {
        $this->assertSame(0, $this->client->hlen('foo'));

        $this->client->hset('foo', 'hello', 'world');
        $this->client->hset('foo', 'bar', 'baz');
        $this->assertSame(2, $this->client->hlen('foo'));
    }

    public function testHashDelete()
    {
        $this->assertSame(0, $this->client->hdel('foo', 'bar'));

        $this->client->hset('foo', 'hello', 'world');
        $this->client->hset('foo', 'bar', 'baz');
        $this->client->hset('foo', 'red', 'blue');

        $this->assertSame(2, $this->client->hdel('foo', 'hello', 'bar', 'derp'));
        $this->assertEquals(array('red' => 'blue'), $this->client->hgetall('foo'));
    }

    public function testHashKeys()
    {
        $this->assertSame(array(), $this->client->hkeys('foo'));

        $this->client->hset('foo', 'hello', 'world');
        $this->client->hset('foo', 'bar', 'baz');
        $this->client->hset('foo', 'red', 'blue');

        $this->assertEquals(array('hello', 'bar', 'red'), $this->client->hkeys('foo'));
    }

    public function testHashValues()
    {
        $this->assertSame(array(), $this->client->hkeys('foo'));

        $this->client->hset('foo', 'hello', 'world');
        $this->client->hset('foo', 'bar', 'baz');
        $this->client->hset('foo', 'red', 'blue');

        $this->assertEquals(array('world', 'baz', 'blue'), $this->client->hvals('foo'));
    }

    public function testHashGetAll()
    {
        $this->assertSame(array(), $this->client->hgetall('foo'));

        $this->client->hset('foo', 'hello', 'world');
        $this->client->hset('foo', 'bar', 'baz');
        $this->client->hset('foo', 'red', 'blue');

        $expected = array(
            'hello' => 'world',
            'bar'   => 'baz',
            'red'   => 'blue',
        );
        $this->assertEquals($expected, $this->client->hgetall('foo'));
    }

    public function testHashExists()
    {
        $this->assertFalse($this->client->hexists('foo', 'hello'));
        $this->client->hset('foo', 'hello', 'world');
        $this->assertTrue($this->client->hexists('foo', 'hello'));
    }

    public function testHashIncrementBy()
    {
        $this->assertSame(2, $this->client->hincrby('foo', 'bar', 2));
        $this->assertSame(4, $this->client->hincrby('foo', 'bar', 2));
    }

    public function testHashIncrementByFloat()
    {
        $this->assertEquals(1.5, $this->client->hincrbyfloat('foo', 'bar', 1.5));
        $this->assertEquals(3.0, $this->client->hincrbyfloat('foo', 'bar', 1.5));
    }

    public function testHashMultipleSet()
    {
        $expected = array(
            'hello' => 'world',
            'red'   => 'blue',
        );
        $this->client->hmset('foo', $expected);
        $this->assertEquals($expected, $this->client->hgetall('foo'));
    }

    public function testHashMultipleGet()
    {
        $expected = array(
            'hello' => 'world',
            'red'   => 'blue',
            'herp'  => 'derp',
        );
        $this->client->hmset('foo', $expected);

        $this->assertEquals(array('world', 'blue'), $this->client->hmget('foo', array('hello', 'red')));
    }

    //endregion

    //region Lists

    public function testListLeftPush()
    {
        $this->assertEquals(2, $this->client->lpush('foo', 'world', 'hello'));
        $this->assertEquals(array('hello', 'world'), $this->client->lrange('foo', 0, -1));
        $this->assertEquals(3, $this->client->lpush('foo', 'derp'));
        $this->assertEquals(array('derp', 'hello', 'world'), $this->client->lrange('foo', 0, -1));
    }

    public function testListRightPush()
    {
        $this->assertEquals(2, $this->client->rpush('foo', 'hello', 'world'));
        $this->assertEquals(array('hello', 'world'), $this->client->lrange('foo', 0, -1));
        $this->assertEquals(3, $this->client->rpush('foo', 'derp'));
        $this->assertEquals(array('hello', 'world', 'derp'), $this->client->lrange('foo', 0, -1));
    }

    public function testListLeftPushExists()
    {
        $this->assertEquals(0, $this->client->lpushx('foo', 'bar'));
        $this->assertFalse($this->client->exists('foo'));
        $this->client->lpush('foo', 'world');
        $this->assertEquals(2, $this->client->lpushx('foo', 'hello'));
        $this->assertEquals(array('hello', 'world'), $this->client->lrange('foo', 0, -1));
    }

    public function testListRightPushExists()
    {
        $this->assertEquals(0, $this->client->rpushx('foo', 'bar'));
        $this->assertFalse($this->client->exists('foo'));
        $this->client->rpush('foo', 'hello');
        $this->assertEquals(2, $this->client->rpushx('foo', 'world'));
        $this->assertEquals(array('hello', 'world'), $this->client->lrange('foo', 0, -1));
    }

    public function testListLeftPop()
    {
        $this->assertNull($this->client->lpop('foo'));
        $this->client->rpush('foo', 'A', 'B', 'C');
        $this->assertSame('A', $this->client->lpop('foo'));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', 0, -1));
    }

    public function testListRightPop()
    {
        $this->assertNull($this->client->rpop('foo'));
        $this->client->rpush('foo', 'A', 'B', 'C');
        $this->assertSame('C', $this->client->rpop('foo'));
        $this->assertEquals(array('A', 'B'), $this->client->lrange('foo', 0, -1));
    }

    public function testListBlockLeftPop()
    {
        $this->markTestSkipped();
    }

    public function testListBlockRightPop()
    {
        $this->markTestSkipped();
    }

    public function testListLength()
    {
        $this->assertSame(0, $this->client->llen('foo'));
        $this->client->rpush('foo', 'A', 'B', 'C');
        $this->assertSame(3, $this->client->llen('foo'));
    }

    public function testListIndex()
    {
        $this->assertNull($this->client->lindex('foo', 0));
        $this->client->rpush('foo', 'A', 'B', 'C');

        $this->assertSame('A', $this->client->lindex('foo', 0));
        $this->assertSame('B', $this->client->lindex('foo', 1));
        $this->assertSame('C', $this->client->lindex('foo', 2));
        $this->assertSame('C', $this->client->lindex('foo', -1));
        $this->assertSame('B', $this->client->lindex('foo', -2));
    }

    public function testListSet()
    {
        try {
            $this->assertFalse($this->client->lset('foo', 0, 'bar'));
            $this->fail('lset on non-existent list should throw exception');
        } catch (Predis\Response\ServerException $e) {
            if ($e->getMessage() !== 'ERR no such key') {
                $this->fail('Wrong exception was thrown');
            }
        }

        $this->client->rpush('foo', 'A', 'B', 'C');
        $this->assertEquals('OK', $this->client->lset('foo', 0, 'bar'));
        $this->assertSame('bar', $this->client->lindex('foo', 0));

        try {
            $this->assertEquals('OK', $this->client->lset('foo', 100, 'bar'));
            $this->fail('lset with index out of range should throw exception');
        } catch (Predis\Response\ServerException $e) {
            if ($e->getMessage() !== 'ERR index out of range') {
                $this->fail('Wrong exception was thrown');
            }
        }
    }

    public function testListRange()
    {
        $this->assertEquals(array(), $this->client->lrange('foo', 0, -1));
        $this->client->rpush('foo', 'A', 'B', 'C');

        $this->assertEquals(array('A'), $this->client->lrange('foo', 0, 0));
        $this->assertEquals(array('A', 'B'), $this->client->lrange('foo', 0, 1));
        $this->assertEquals(array('A', 'B', 'C'), $this->client->lrange('foo', 0, 2));
        $this->assertEquals(array('A', 'B', 'C'), $this->client->lrange('foo', 0, -1));
        $this->assertEquals(array('A', 'B'), $this->client->lrange('foo', 0, -2));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', 1, -1));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', -2, 2));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', -2, -1));
    }

    public function testListTrim()
    {
        $this->assertEquals('OK', $this->client->ltrim('foo', 0, -1));
        $this->client->rpush('foo', 'A', 'B', 'C');

        $this->assertEquals('OK', $this->client->ltrim('foo', 1, -1));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', 0, -1));

        $this->assertEquals('OK', $this->client->ltrim('foo', 0, -2));
        $this->assertEquals(array('B'), $this->client->lrange('foo', 0, -1));
    }

    public function testListRemove()
    {
        $this->assertSame(0, $this->client->lrem('foo', 0, 'A'));
        $this->client->rpush('foo', 'A', 'B', 'C', 'A', 'A');
        $this->assertSame(3, $this->client->lrem('foo', 0, 'A'));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', 0, -1));

        $this->client->rpush('foo', 'B');
        $this->assertSame(1, $this->client->lrem('foo', -1, 'B'));
        $this->assertEquals(array('B', 'C'), $this->client->lrange('foo', 0, -1));

        $this->client->rpush('foo', 'B');
        $this->assertSame(1, $this->client->lrem('foo', 1, 'B'));
        $this->assertEquals(array('C', 'B'), $this->client->lrange('foo', 0, -1));
    }

    public function testListInsert()
    {
        $this->assertSame(0, $this->client->linsert('foo', 'after', 'hello', 'world'));
        $this->client->rpush('foo', 'hello', 'bar');

        $this->assertSame(3, $this->client->linsert('foo', 'after', 'hello', 'world'));
        $this->assertEquals(array('hello', 'world', 'bar'), $this->client->lrange('foo', 0, -1));

        $this->assertSame(4, $this->client->linsert('foo', 'before', 'bar', 'baz'));
        $this->assertEquals(array('hello', 'world', 'baz', 'bar'), $this->client->lrange('foo', 0, -1));
    }

    public function testListRightPopLeftPush()
    {
        $this->assertNull($this->client->rpoplpush('foo', 'bar'));
        $this->client->rpush('foo', 'A', 'B');
        $this->client->rpush('bar', 'C', 'D');
        $this->assertEquals('B', $this->client->rpoplpush('foo', 'bar'));
        $this->assertEquals(array('A'), $this->client->lrange('foo', 0, -1));
        $this->assertEquals(array('B', 'C', 'D'), $this->client->lrange('bar', 0, -1));
    }

    public function testListBlockRightPopLeftPush()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Sets

    public function testSetAdd()
    {
        $this->markTestSkipped();
    }

    public function testSetRemove()
    {
        $this->markTestSkipped();
    }

    public function testSetMove()
    {
        $this->markTestSkipped();
    }

    public function testSetIsMember()
    {
        $this->markTestSkipped();
    }

    public function testSetCard()
    {
        $this->markTestSkipped();
    }

    public function testSetPop()
    {
        $this->markTestSkipped();
    }

    public function testSetRandMember()
    {
        $this->markTestSkipped();
    }

    public function testSetInter()
    {
        $this->markTestSkipped();
    }

    public function testSetInterStore()
    {
        $this->markTestSkipped();
    }

    public function testSetUnion()
    {
        $this->markTestSkipped();
    }

    public function testSetUnionStore()
    {
        $this->markTestSkipped();
    }

    public function testSetDiff()
    {
        $this->markTestSkipped();
    }

    public function testSetDiffStore()
    {
        $this->markTestSkipped();
    }

    public function testSetMembers()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Sorted Sets

    public function testSortedSetAdd()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetRange()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetRemove()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetReverseRange()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetRangeByScore()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetReverseRangeByScore()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetCount()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetRemoveRangeByScore()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetRemoveRangeByRank()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetCard()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetScore()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetRank()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetReverseRank()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetIncrementBy()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetUnionStore()
    {
        $this->markTestSkipped();
    }

    public function testSortedSetInterStore()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Server

    public function testBackgroundRewriteAppendOnlyFile()
    {
        $this->markTestSkipped();
    }

    public function testBackgroundSave()
    {
        $this->markTestSkipped();
    }

    public function testConfig()
    {
        $this->markTestSkipped();
    }

    public function testDbSize()
    {
        $this->markTestSkipped();
    }

    public function testFlushAll()
    {
        $this->markTestSkipped();
    }

    public function testFlushDb()
    {
        $this->markTestSkipped();
    }

    public function testInfo()
    {
        $this->markTestSkipped();
    }

    public function testLastSave()
    {
        $this->markTestSkipped();
    }

    public function testSave()
    {
        $this->markTestSkipped();
    }

    public function testSlaveOf()
    {
        $this->markTestSkipped();
    }

    public function testTime()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Connection

    public function testAuth()
    {
        $this->markTestSkipped();
    }

    public function testPing()
    {
        $this->markTestSkipped();
    }

    public function testSelect()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Pub/Sub

    public function testPsubscribe()
    {
        $this->markTestSkipped();
    }

    public function testSubscribe()
    {
        $this->markTestSkipped();
    }

    public function testPublish()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Transactions

    public function testDiscard()
    {
        $this->markTestSkipped();
    }

    public function testExec()
    {
        $this->markTestSkipped();
    }

    public function testMulti()
    {
        $this->markTestSkipped();
    }

    public function testUnwatch()
    {
        $this->markTestSkipped();
    }

    public function testWatch()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Scripting

    public function testEvalSha()
    {
        $this->markTestSkipped();
    }

    public function testScript()
    {
        $this->markTestSkipped();
    }

    //endregion
}
