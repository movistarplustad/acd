<?php
namespace Acd\Controller;

use \Acd\View\HeaderMenu;
use \Acd\Model\ContentLoader;
// Output
class Relation {
	private  $idContent;
	private $idStructure;
	private $view;
	private $content;
	private $backButtom;
	const VIEW_LIST = 'list'; // List of all parents of a contents

	/* Setters and getters attributes */
	public function setIdContent($id) {
		$this->id = (string)$id;
	}
	public function getIdContent() {
		return $this->id;
	}
	public function setIdStructure($idStructure) {
		$this->idStructure = (string)$idStructure;
	}
	public function getIdStructure() {
		return $this->idStructure;
	}
	public function setView($view) {
		$this->view = (string)$view;
	}
	public function getView() {
		return $this->view;
	}
	private function setContent($content) {
		$this->content = $content;
	}
	private function getContent() {
		return $this->content;
	}
	private function setParents($parents) {
		$this->parents = $parents;
	}
	private function getParents() {
		return $this->parents;
	}
	public function load() {
		$contentLoader = new ContentLoader();
		$contentLoader->setId($this->getIdStructure());
		$this->setContent($contentLoader->loadContent('id', $this->getIdContent()));

		// Parents
		$this->setParents($contentLoader->loadContents('parents', $this->getIdContent()));
	}
	public function getTitle() {
		return 'Relations of ';
	}
	public function setBack($back) {
		$this->backButtom = (boolean)$back;
	}
	public function getBack() {
		return $this->backButtom;
	}
	public function getHeaderMenuOu() {
		$headerMenuOu = new HeaderMenu();
		$headerMenuOu->setBack($this->getBack());
		return $headerMenuOu;
	}
	public function render() {
		$ou = new \Acd\View\Relation();
		$ou->setContentTitle($this->getContent()->getTitle());
		$ou->setParentList($this->getParents());
		return $ou->render();
	}
}