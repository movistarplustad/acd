<?php
namespace Acd\Model;

class Limits
{
	private $lower;
	private $upper;
	private $step;
	private $total; // Total elements previous to apply limits
	public function __construct($lower = 0, $upper = 100) {
		$this->setLower($lower);
		$this->setUpper($upper);
		$this->setStep($upper);
	}
	public function setLower($lower) {
		$this->lower = (int) $lower;
	}
	public function getLower() {
		return $this->lower;
	}
	public function setUpper($upper) {
		$this->upper = (int) $upper;
	}
	public function getUpper() {
		return $this->upper;
	}
	public function setTotal($total) {
		$this->total = (int) $total;
	}
	public function getTotal() {
		return $this->total;
	}
	public function setStep($step) {
		$this->step = (int) $step;
		$this->setUpper($this->getLower() + $this->step);
	}
	public function getStep() {
		return $this->step;
	}
	public function setPage($nPage) {
		$this->setLower($nPage * $this->getStep());
		$this->setUpper(($nPage + 1) * $this->getStep());
	}
}
