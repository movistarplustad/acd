<?php
namespace Acd\Model;

class Query
{
	private $type;
	private $condition;
	private $limits;
	private $depth;
	
	public function __construct() {
		$this->setLimits (new Limits(0, 50));
		$this->setDepth (10);
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
	public function getCondition($condition = null) {
		if (is_null($condition)) {
			return $this->condition;
		}
		else {
			return @$this->condition[$condition] ?: null;
		}
	}
	public function setLimits($limits) {
		$this->limits = $limits;
	}
	public function getLimits() {
		return $this->limits;
	}
	public function setDepth($depth) {
		$this->depth = $depth;
	}
	public function getDepth() {
		return $this->depth;
	}
}