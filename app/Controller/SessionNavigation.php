<?php

namespace Acd\Controller;

class SessionNavigationException extends \Exception
{
}
// Manage user navigation for back  link
//http://www.sitepoint.com/php-data-structures-1/
class SessionNavigation
{
	protected $stack;
	protected $limit;

	public function __construct($limit = 10)
	{
		// initialize the stack
		$this->stack = array();
		// stack can only contain this many items
		$this->limit = $limit;
	}
	public function getStack()
	{
		return $this->stack;
	} // Temporal for debug

	public function push($item)
	{
		// TODO: Comprobar que es array y tiene hash y url
		if (!$this->sameLastUrl($item)) {
			// trap for stack overflow
			if (count($this->stack) < $this->limit) {
				// prepend item to the start of the array
				array_unshift($this->stack, $item);
			} else {
				// delete first-old item and prepend item to the start of the array
				array_pop($this->stack);
				array_unshift($this->stack, $item);
			}
		}
	}

	public function pop()
	{
		if ($this->isEmpty()) {
			// trap for stack underflow
			throw new SessionNavigationException('Session stack is empty!');
		} else {
			// pop item from the start of the array
			return array_shift($this->stack);
		}
	}

	public function top()
	{
		return current($this->stack);
	}

	public function isEmpty()
	{
		return empty($this->stack);
	}

	private function sameLastUrl($item)
	{
		if ($this->isEmpty()) {
			return false;
		} else {
			$lastItem = $this->top();
			return $lastItem['hash'] === $item['hash'];
		}
	}

	public function load()
	{
		if (isset($_SESSION['navigation_history']) && is_array($_SESSION['navigation_history'])) {
			$this->stack = $_SESSION['navigation_history'];
		}
	}
	public function save()
	{
		$_SESSION['navigation_history'] = $this->stack;
	}
}
