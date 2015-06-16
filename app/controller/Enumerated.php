<?php
namespace Acd\Controller;

use \Acd\Model\EnumeratedLoader;
use \Acd\Model\Query;
use \Acd\View\HeaderMenu;
// Output
class Enumerated {
	const VIEW_LIST = 'list'; // List of all enumerated collection
	const VIEW_DETAIL = 'detail'; // Detail (values) of a collection
	private  $id;
	private $view;
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
		$headerMenuOu->setUrl('enumerated.php');
		switch ($this->getView()) {
			case $this::VIEW_LIST:
				$headerMenuOu->setType('menu');
				break;
			case $this::VIEW_DETAIL:
				$headerMenuOu->setType('menuBackUrl');
				break;
		}
		return $headerMenuOu;
	}

	public function load() {
		$enumeratedLoader = new EnumeratedLoader();
		$query = new Query();
		if ($this->getId()) {
			$query->setType('id');
			$query->setCondition(['id' => $this->getId()]);
			$this->setView($this::VIEW_DETAIL);
		}
		else {
			$query->setType('all');
			$this->setView($this::VIEW_LIST);
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
				$ou = new \ACD\View\EnumeratedDetail();
				$ou->setEnumeratedElement($this->getContent());
				break;
			
			default:
				throw new Exception("View (".$this->getView().") not defined", 1);
				break;
		}

		return $ou->render();
	}
}