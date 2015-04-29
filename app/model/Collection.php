<?php
namespace Acd\Model;

class KeyInvalidException extends \exception {}
class KeyhasUseException extends \exception {}
class Collection implements \IteratorAggregate
{
	protected $elements;
	private $limits;

	public function __construct() {
		$this->elements = array(); /* Create empty structure */
	}

	// return iterator
	public function getIterator() {
		return new \ArrayIterator( $this->elements );
	}

	public function hasKey($key) {
		return isset($this->elements[$key]);
	}

	public function add($element, $key = null) {
		if ($key == null) {
			$this->elements[] = $element;
		}
		else {
			if ($this->hasKey($key)) {
				throw new KeyHasUseException("Key '$key' already in use.");
			}
			else {
				$this->elements[$key] = $element;
			}
		}
	}

	public function set($element, $key) {
		$this->elements[$key] = $element;
	}

	public function remove($key) {
		if (isset($this->elements[$key])) {
			unset($this->elements[$key]);
		}
		else {
			throw new KeyInvalidException("Invalid key '$key'.");
		}
	}

	public function get($key) {
		if (isset($this->elements[$key])) {
			return $this->elements[$key];
		}
		else {
			throw new KeyInvalidException("Invalid key '$key'.");
		}
	}

	public function one() {
		reset($this->elements);
		return current($this->elements);
	}

	public function keys() {
		return array_keys($this->elements);
	}
	public function length() {
		return count($this->elements);
	}

	public function setLimits($limits) {
		$this->limits = $limits;
	}
	public function getLimits() {
		return $this->limits;
	}

}