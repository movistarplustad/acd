<?php
namespace Acd;
use \Acd\Model\SessionNavigation;

class session_navigation_test extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testIsEmpty()
	{
		$navigation = new SessionNavigation();
		$this->assertTrue($navigation->isEmpty());

	}
	public function testPush()
	{
		$navigation = new SessionNavigation();
		$item = [
			'hash' => 'foo',
			'url' => '/foo.html'
		];
		$navigation->push($item);
		$this->assertFalse($navigation->isEmpty());
		$this->assertEquals($navigation->top(), $item);
	}
	public function testPop()
	{
		$navigation = new SessionNavigation();
		$item = [
			'hash' => 'foo',
			'url' => '/foo.html'
		];
		$navigation->push($item);
		$this->assertFalse($navigation->isEmpty());
		$this->assertEquals($navigation->pop(), $item);
		$this->assertTrue($navigation->isEmpty());
	}
	public function testClearOut() {
		/**
		* @expectedException Model\SessionNavigation\SessionNavigationException
		*/
		$navigation = new SessionNavigation();
		$item = [
			'hash' => 'foo',
			'url' => '/foo.html'
		];
		$navigation->push($item);
		$this->assertEquals($navigation->pop(), $item);

		$this->setExpectedException('Exception');
		$this->assertEquals($navigation->pop(), $item);
	}
	public function testoveflowStack() {
		$navigation = new SessionNavigation(5);
		for($n=0; $n < 15; $n++) {
			$item = [
				'hash' => "foo $n",
				'url' => "/foo_$n.html"
			];
			$navigation->push($item);
		}
		$this->assertEquals($navigation->pop(), $item); // Item is last item
	}
	public function testPush2ItemsSameHistory()
	{
		$navigation = new SessionNavigation();
		$item0 = [
			'hash' => '2ItemsSameHistory',
			'url' => '/foo0.html'
		];
		$item1 = [
			'hash' => '2ItemsSameHistory',
			'url' => '/foo1html'
		];

		$navigation->push($item0);
		$navigation->push($item1);
		$this->assertEquals($navigation->top(), $item0);
	}
	public function testPush2Items2History()
	{
		$navigation = new SessionNavigation();
		$item0 = [
			'hash' => '2Items2History 0',
			'url' => '/foo0.html'
		];
		$item1 = [
			'hash' => '2Items2History 1',
			'url' => '/foo1html'
		];
		$item2 = [
			'hash' => '2Items2History 2',
			'url' => '/foo2html'
		];

		$navigation->push($item0);
		$navigation->push($item1);

		$this->assertEquals($navigation->top(), $item1);
	}
}
