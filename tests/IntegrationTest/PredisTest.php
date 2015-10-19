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
        $this->markTestSkipped();
    }

    public function testListRightPush()
    {
        $this->markTestSkipped();
    }

    public function testListLeftPushExists()
    {
        $this->markTestSkipped();
    }

    public function testListRightPushExists()
    {
        $this->markTestSkipped();
    }

    public function testListLeftPop()
    {
        $this->markTestSkipped();
    }

    public function testListRightPop()
    {
        $this->markTestSkipped();
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
        $this->markTestSkipped();
    }

    public function testListIndex()
    {
        $this->markTestSkipped();
    }

    public function testListSet()
    {
        $this->markTestSkipped();
    }

    public function testListRange()
    {
        $this->markTestSkipped();
    }

    public function testListTrim()
    {
        $this->markTestSkipped();
    }

    public function testListRemove()
    {
        $this->markTestSkipped();
    }

    public function testListInsert()
    {
        $this->markTestSkipped();
    }

    public function testListRightPopLeftPush()
    {
        $this->markTestSkipped();
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
