<?php
namespace IntegrationTest;

use GMO\Cache\Memcache;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemcacheTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->cache = new Memcache();
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
		$one_i = $this->cache->increment('foo');
		$one = $this->cache->get('foo');
		$two_i = $this->cache->increment('foo');
		$two = $this->cache->get('foo');
		
		$this->assertSame(1, $one);
		$this->assertSame(2, $two);
		$this->assertSame(1, $one_i);
		$this->assertSame(2, $two_i);
		
		$this->cache->deleteAll();
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
		
		$this->cache->deleteAll();
	}
	
	public function test_decrement() {
		$zero_i = $this->cache->decrement('foo');
		$zero = $this->cache->get('foo');
		
		$this->cache->set('foo', 3);
		
		$two_i = $this->cache->decrement('foo');
		$two = $this->cache->get('foo');
		$one_i = $this->cache->decrement('foo');
		$one = $this->cache->get('foo');
		
		$this->assertSame(0, $zero);
		$this->assertSame('2', $two);
		$this->assertSame('1', $one);
		$this->assertSame(0, $zero_i);
		$this->assertSame(2, $two_i);
		$this->assertSame(1, $one_i);
		
		$this->cache->deleteAll();
	}
	
	public function test_decrement_by_2() {
		$zero_i = $this->cache->decrement('foo', 2);
		$zero = $this->cache->get('foo');
	
		$this->cache->set('foo', 6);
	
		$four_i = $this->cache->decrement('foo', 2);
		$four = $this->cache->get('foo');
		$two_i = $this->cache->decrement('foo', 2);
		$two = $this->cache->get('foo');
	
		$this->assertSame(0, $zero);
		$this->assertSame('4', $four);
		$this->assertSame('2', $two);
		$this->assertSame(0, $zero_i);
		$this->assertSame(4, $four_i);
		$this->assertSame(2, $two_i);
		
		$this->cache->deleteAll();
	}
		
	/**
	 * @var ICache
	 */
	private $cache;
}
