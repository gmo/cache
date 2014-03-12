<?php
namespace IntegrationTest;

use Psr\Log\NullLogger;
use GMO\Cache\Redis;

require_once __DIR__ . "/../../vendor/autoload.php";

class RedisTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->cache = new Redis();
		$this->cache->deleteAll();
	}
	
	public function test_set_and_get() {
		$this->cache->set('foo', 'bar');
		$result = $this->cache->get('foo');
		
		$this->assertSame('bar', $result);
		$this->cache->deleteAll();
	}
	
	public function test_set_expiration() {
		$expires = 1;
		$this->cache->set('foo', 'bar', $expires);
		sleep($expires + 1);
		$result = $this->cache->get('foo');
		
		$this->assertSame(NULL, $result);
	}
	
	public function test_delete() {
		$this->cache->set('foo', 'bar');
		$this->cache->delete('foo');
		
		$result = $this->cache->get('foo');
		$this->assertSame(NULL, $result);
		$this->cache->deleteAll();
	}
	
	public function test_delete_all() {
		$this->cache->set('foo', 'bar');
		$this->cache->set('baz', 'blah');
		$this->cache->deleteAll();
		
		$result = $this->cache->get('foo');
		$this->assertSame(NULL, $result);
		
		$result = $this->cache->get('baz');
		$this->assertSame(NULL, $result);
	}
	
	public function test_increment() {
		$this->cache->increment('foo');
		$one = $this->cache->get('foo');
		$this->cache->increment('foo');
		$two = $this->cache->get('foo');
		
		$this->assertSame('1', $one);
		$this->assertSame('2', $two);

		$this->cache->deleteAll();
	}
	
	public function test_increment_by_2() {
		$this->cache->increment('foo', 2);
		$two = $this->cache->get('foo');
		$this->cache->increment('foo', 2);
		$four = $this->cache->get('foo');
	
		$this->assertSame('2', $two);
		$this->assertSame('4', $four);
		
		$this->cache->deleteAll();
	}
	
	public function test_decrement() {
		$this->cache->decrement('foo');
		$neg_one = $this->cache->get('foo');
		
		$this->cache->set('foo', 3);
		
		$this->cache->decrement('foo');
		$two = $this->cache->get('foo');
		$this->cache->decrement('foo');
		$one = $this->cache->get('foo');
		
		$this->assertSame('-1', $neg_one);
		$this->assertSame('2', $two);
		$this->assertSame('1', $one);
	
		$this->cache->deleteAll();
	}
	
	public function test_decrement_by_2() {
		$this->cache->decrement('foo', 2);
		$neg_two = $this->cache->get('foo');
	
		$this->cache->set('foo', 6);
	
		$this->cache->decrement('foo', 2);
		$four = $this->cache->get('foo');
		$this->cache->decrement('foo', 2);
		$two = $this->cache->get('foo');
	
		$this->assertSame('-2', $neg_two);
		$this->assertSame('4', $four);
		$this->assertSame('2', $two);
	
		$this->cache->deleteAll();
	}
		
	/**
	 * @var ICache
	 */
	private $cache;
}
