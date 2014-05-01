<?php
namespace IntegrationTest;

use GMO\Cache\Redis;
use GMO\Cache\Exception\ConnectionFailureException;

require_once __DIR__ . "/../../vendor/autoload.php";

class RedisTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->cache = new Redis();
		$this->cache->deleteAll();
	}

	public function test_invalid_construct() {
		try {
			$cache = new Redis('localhost', 100);
		} catch (ConnectionFailureException $e) {
			$this->assertSame('Connection refused [tcp://localhost:100]', $e->getMessage());
			return;
		}
		
		$this->fail('Exception ConnectionFailureException was not raised');
	}
	
	public function test_master_slave() {
		$cache = new Redis('localhost', 6379, array(
			array('host' => 'localhost', 'port' => 6379)));
		
		$cache->set('foo', 'bar');
		$result = $cache->get('foo');
		
		$this->assertSame('bar', $result);
	}
	
	public function test_set_and_get() {
		$this->cache->set('foo', 'bar');
		$result = $this->cache->get('foo');
		
		$this->assertSame('bar', $result);
	}
	
	public function test_set_expiration() {
		$expires = 1;
		$this->cache->set('foo', 'bar', $expires);
		sleep($expires + 1);
		$result = $this->cache->get('foo');
		
		$this->assertNull($result);
	}
	
	public function test_set_and_get_serialized() {
		$values = array(
			'one' => 'bar',
			'two' => 'baz' );
	
		$this->cache->set('foo', $values);
		$result = $this->cache->get('foo');

		$this->assertTrue(is_array($result));
		$this->assertCount(2, $result);
		$this->assertArrayHasKey('one', $result);
		$this->assertArrayHasKey('two', $result);
		$this->assertSame('bar', $result['one']);
		$this->assertSame('baz', $result['two']);
	}
	
	public function test_delete() {
		$this->cache->set('foo', 'bar');
		$this->cache->delete('foo');
		
		$result = $this->cache->get('foo');
		$this->assertNull($result);
	}

	public function test_delete_multiple_with_args() {
		$this->cache->set('foo', 'bar');
		$this->cache->set('baz', 'blah');

		$this->cache->deleteMultiple('foo', 'bar');

		$this->assertNull($this->cache->get('foo'));
		$this->assertNull($this->cache->get('bar'));
	}

	public function test_delete_multiple_with_array() {
		$this->cache->set('foo', 'bar');
		$this->cache->set('baz', 'blah');

		$this->cache->deleteMultiple(array( 'foo', 'bar' ));

		$this->assertNull($this->cache->get('foo'));
		$this->assertNull($this->cache->get('bar'));
	}

	public function test_delete_all() {
		$this->cache->set('foo', 'bar');
		$this->cache->set('baz', 'blah');
		$this->cache->deleteAll();

		$result = $this->cache->get('foo');
		$this->assertNull($result);

		$result = $this->cache->get('baz');
		$this->assertNull($result);
	}
	
	public function test_select_db() {
		$this->cache->set('foo', 'bar');
		$this->cache->set('baz', 'blah');
		
		$this->cache->selectDb(2);
		$result = $this->cache->get('foo');
		$this->assertNull($result);

		$this->cache->set('foo', 'bar');		
		$result = $this->cache->get('foo');
		$this->assertSame('bar', $result);
		
		$this->cache->deleteAll();
		$this->cache->selectDb(0);
		$result = $this->cache->get('foo');
		$this->assertSame('bar', $result);
	}
	
	public function test_increment() {
		$one_i = $this->cache->increment('foo');
		$one = $this->cache->get('foo');
		$two_i = $this->cache->increment('foo');
		$two = $this->cache->get('foo');
		
		$this->assertSame(1, $one);
		$this->assertSame(2, $two);
		$this->assertSame(1, $one_i);
		$this->assertSame(2, $two_i);
	}
	
	public function test_increment_by_2() {
		$two_i = $this->cache->increment('foo', 2);
		$two = $this->cache->get('foo');
		$four_i = $this->cache->increment('foo', 2);
		$four = $this->cache->get('foo');
	
		$this->assertSame(2, $two);
		$this->assertSame(4, $four);
		$this->assertSame(2, $two_i);
		$this->assertSame(4, $four_i);
	}
	
	public function test_decrement() {
		$neg_one_i = $this->cache->decrement('foo');
		$neg_one = $this->cache->get('foo');
		
		$this->cache->set('foo', 3);
		
		$two_i = $this->cache->decrement('foo');
		$two = $this->cache->get('foo');
		$one_i = $this->cache->decrement('foo');
		$one = $this->cache->get('foo');
		
		$this->assertSame(-1, $neg_one);
		$this->assertSame(2, $two);
		$this->assertSame(1, $one);
		$this->assertSame(-1, $neg_one_i);
		$this->assertSame(2, $two_i);
		$this->assertSame(1, $one_i);
	}
	
	public function test_decrement_by_2() {
		$neg_two_i = $this->cache->decrement('foo', 2);
		$neg_two = $this->cache->get('foo');
	
		$this->cache->set('foo', 6);
	
		$four_i = $this->cache->decrement('foo', 2);
		$four = $this->cache->get('foo');
		$two_i = $this->cache->decrement('foo', 2);
		$two = $this->cache->get('foo');
	
		$this->assertSame(-2, $neg_two);
		$this->assertSame(4, $four);
		$this->assertSame(2, $two);
		$this->assertSame(-2, $neg_two_i);
		$this->assertSame(4, $four_i);
		$this->assertSame(2, $two_i);
	}

	public function test_hash_get_and_set() {
		$this->cache->setHash('test', 'foo', 'bar');

		$result = $this->cache->getHash('test', 'foo');
		$this->assertSame('bar', $result);
	}

	public function test_hash_get_and_set_multiple() {
		$this->cache->setMultipleHash('test', array( 'foo' => 'bar', 'herp' => 'derp' ));

		$result = $this->cache->getAllHash('test');
		$this->assertSame('bar', $result['foo']);
		$this->assertSame('derp', $result['herp']);
	}

	public function test_hash_increment() {
		$result = $this->cache->incrementHash('test', 'foo');
		$this->assertSame(1, $result);
		$result = $this->cache->incrementHash('test', 'foo', 2);
		$this->assertSame(3, $result);
	}

	public function test_list_get_and_set() {
		$this->cache->setList('items', array( 'foo', 'bar' ));
		$result = $this->cache->getList('items');
		$this->assertSame(array( 'foo', 'bar' ), $result);

		$this->cache->setList('items', array( 'herp', 'derp' ));
		$result = $this->cache->getList('items');
		$this->assertSame(array( 'herp', 'derp' ), $result);

		$result = $this->cache->getList('items', 0, 0);
		$this->assertSame(array( 'herp' ), $result);

		$result = $this->cache->getList('items', 1, 1);
		$this->assertSame(array( 'derp' ), $result);
	}

	public function test_list_append() {
		$this->cache->appendList('items', 'foo');
		$this->cache->appendList('items', 'bar');
		$result = $this->cache->getList('items');
		$this->assertSame(array( 'foo', 'bar' ), $result);
	}

	public function test_list_prepend() {
		$this->cache->prependList('items', 'foo');
		$this->cache->prependList('items', 'bar');
		$result = $this->cache->getList('items');
		$this->assertSame(array( 'bar', 'foo' ), $result);
	}

	public function test_list_trim() {
		$this->cache->setList('items', array( 'foo', 'bar', 'herp', 'derp' ));
		$this->cache->trimList('items', 1, 2);
		$result = $this->cache->getList('items');
		$this->assertSame(array( 'bar', 'herp' ), $result);
	}

	public function test_publish_does_not_throw_error() {
		$this->cache->publish('foo', 'bar');
	}

	public function test_pipeline() {
		$replies = $this->cache->pipeline(function($pipe) {
			$pipe->set('foo', 'bar');
			$pipe->get('foo');
		});
		$this->assertTrue($replies[0]);
		$this->assertSame('bar', $replies[1]);
	}

	/**
	 * @var Redis
	 */
	private $cache;
}
