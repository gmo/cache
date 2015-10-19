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
        $this->markTestSkipped();
    }

    public function testDump()
    {
        $this->markTestSkipped();
    }

    public function testExists()
    {
        $this->markTestSkipped();
    }

    public function testExpire()
    {
        $this->markTestSkipped();
    }

    public function testPExpireAt()
    {
        $this->markTestSkipped();
    }

    public function testKeys()
    {
        $this->markTestSkipped();
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
        $this->markTestSkipped();
    }

    public function testPttl()
    {
        $this->markTestSkipped();
    }

    public function testTtl()
    {
        $this->markTestSkipped();
    }

    public function testRandomKey()
    {
        $this->markTestSkipped();
    }

    public function testRename()
    {
        $this->markTestSkipped();
    }

    public function testRenameNx()
    {
        $this->markTestSkipped();
    }

    public function testRestore()
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
        $this->markTestSkipped();
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
        $this->markTestSkipped();
    }

    public function testDecrementBy()
    {
        $this->markTestSkipped();
    }

    public function testGetBit()
    {
        $this->markTestSkipped();
    }

    public function testGetRange()
    {
        $this->markTestSkipped();
    }

    public function testGetSet()
    {
        $this->markTestSkipped();
    }

    public function testIncrement()
    {
        $this->markTestSkipped();
    }

    public function testIncrementByFloat()
    {
        $this->markTestSkipped();
    }

    public function testIncrementBy()
    {
        $this->markTestSkipped();
    }

    public function testMultipleSet()
    {
        $this->markTestSkipped();
    }

    public function testMultipleGet()
    {
        $this->markTestSkipped();
    }

    public function testMultipleSetNx()
    {
        $this->markTestSkipped();
    }

    public function testGet()
    {
        $this->markTestSkipped();
    }

    public function testSet()
    {
        $this->markTestSkipped();
    }

    public function testSetBit()
    {
        $this->markTestSkipped();
    }

    public function testSetEx()
    {
        $this->markTestSkipped();
    }

    public function testSetNx()
    {
        $this->markTestSkipped();
    }

    public function testSetRange()
    {
        $this->markTestSkipped();
    }

    public function testStringLength()
    {
        $this->markTestSkipped();
    }

    //endregion

    //region Hashes

    public function testHashSet()
    {
        $this->markTestSkipped();
    }

    public function testHashSetEx()
    {
        $this->markTestSkipped();
    }

    public function testHashGet()
    {
        $this->markTestSkipped();
    }

    public function testHashLength()
    {
        $this->markTestSkipped();
    }

    public function testHashDelete()
    {
        $this->markTestSkipped();
    }

    public function testHashKeys()
    {
        $this->markTestSkipped();
    }

    public function testHashValues()
    {
        $this->markTestSkipped();
    }

    public function testHashGetAll()
    {
        $this->markTestSkipped();
    }

    public function testHashExists()
    {
        $this->markTestSkipped();
    }

    public function testHashIncrementBy()
    {
        $this->markTestSkipped();
    }

    public function testHashIncrementByFloat()
    {
        $this->markTestSkipped();
    }

    public function testHashMultipleSet()
    {
        $this->markTestSkipped();
    }

    public function testHashMultipleGet()
    {
        $this->markTestSkipped();
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
