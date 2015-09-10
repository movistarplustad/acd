<?php
namespace Acd\Controller;

use \Acd\Model\EnumeratedLoader;
use \Acd\Model\Query;
use \Acd\Model\EnumeratedDo;
use \Acd\View\HeaderMenu;
// Output
class Enumerated {
	const VIEW_LIST = 'list'; // List of all enumerated collection
	const VIEW_DETAIL = 'edit'; // Detail (values) of a collection
	const VIEW_DETAIL_NEW = 'new'; // New collection
	private  $id;
	private $view;
	private $backButtom;
	private $contentFound;

	/* Setters and getters attributes */
	public function setId($id) {
		$this->id = (string)$id;
	}
	public function getId() {
		return $this->id;
	}
	public function setView($view) {
		$this->view = (string)$view;
	}
	public function getView() {
		return $this->view;
	}
	public function setBack($back) {
		$this->backButtom = (boolean)$back;
	}
	public function getBack() {
		return $this->backButtom;
	}
	public function setContent($contentFound) {
		$this->contentFound = $contentFound;
	}
	public function getContent() {
		return $this->contentFound;
	}
	public function getTitle() {
		return 'Collection of enumerated values';
	}

	public function getHeaderMenuOu() {
		$headerMenuOu = new HeaderMenu();
		$headerMenuOu->setBack($this->getBack());
		return $headerMenuOu;
	}

	public function load() {
		$enumeratedLoader = new EnumeratedLoader();
		$query = new Query();
		if ($this->getId()) {
			$query->setType('id');
			$query->setCondition(['id' => $this->getId()]);
		}
		else {
			if (!$this->getView()){
				$query->setType('all');
				$this->setView($this::VIEW_LIST);
			}
		}
		$this->setContent($enumeratedLoader->load($query));
	}
	public function render() {
		switch ($this->getView()) {
			case $this::VIEW_LIST:
				$ou = new \ACD\View\EnumeratedList();
				$ou->setEnumeratedList($this->getContent());
				break;
			case $this::VIEW_DETAIL:
				if ($this->getContent()->getId()) {
					$ou = new \ACD\View\EnumeratedDetail();
					$ou->setEnumeratedElement($this->getContent());
				}
				else {
					throw new \Exception('Enumerated collection not found', 404);
				}
				break;
			case $this::VIEW_DETAIL_NEW:
				$ou = new \ACD\View\EnumeratedDetail();
				$emptyCollection = new EnumeratedDo();
				$ou->setEnumeratedElement($emptyCollection);
				break;
			default:
				throw new \Exception("View (".$this->getView().") not defined", 1);
				break;
		}

		return $ou->render();
	}
}