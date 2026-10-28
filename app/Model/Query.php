<?php
namespace Acd\Model;

class Query
{
	private $type;
	private $condition;
	private $limits;
	private $depth;
	private $order;

	public function __construct() {
		$this->setLimits (new Limits(0, 50));
		$this->setDepth (10);
		$this->setOrder ('');
	}
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	public function setCondition($condition) {
		if(isset($condition['depth'])) {
			$this->setDepth($condition['depth']);
			unset($condition['depth']);
		}
		if(isset($condition['order'])) {
			$this->setOrder($condition['order']);
			unset($condition['order']);
		}
		$this->condition = $condition;
	}
	/* If no conditions are requested an array is returned with all of them */
	public function getCondition($condition = null) {
		if (is_null($condition)) {
			return $this->condition;
		}
		else {
			return isset($this->condition[$condition]) ? $this->condition[$condition] : null;
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
	public function setOrder($order) {
		$this->order = $order;
	}
	public function getOrder() {
		return $this->order;
	}
}
