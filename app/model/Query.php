<?php
namespace Acd\Model;

class Query
{
	private $type;
	private $condition;
	private $limits;
	
	public function __construct() {
		$this->limits = new Limits(0, 50);
	}
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	public function setCondition($condition) {
		$this->condition = $condition;
	}
	public function getCondition() {
		return $this->condition;
	}
	public function setLimits($limits) {
		$this->limits = $limits;
	}
	public function getLimits() {
		return $this->limits;
	}
}