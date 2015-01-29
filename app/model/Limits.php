<?php
namespace Acd\Model;

class Limits
{
	private $lower;
	private $upper;
	private $total; // Total elements previous to apply limits
	public function __construct($lower = 0, $upper = 50) {
		$this->setLower($lower);
		$this->setUpper($upper);
	}
	public function setLower($lower) {
		$this->lower = $lower;
	}
	public function getLower() {
		return $this->lower;
	}
	public function setUpper($upper) {
		$this->upper = $upper;
	}
	public function getUpper() {
		return $this->upper;
	}
	public function setTotal($total) {
		$this->total = $total;
	}
	public function getTotal() {
		return $this->total;
	}
}