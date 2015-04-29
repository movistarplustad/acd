<?php
namespace Acd\Controller;
// Output
class Summary {
	private  $idContent;
	private $idStructure;

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
	public function load() {
		$contentLoader = new \ACD\Model\ContentLoader();
		$contentLoader->setId($this->getIdStructure());
		$this->contentDo = $contentLoader->loadContents('id-deep', ['id' => $this->getIdContent(), 'depth' => 20]);
		$this->contentDo = $this->contentDo->one();
		return $this->contentDo;
	}
	public function render() {
		$json_string = json_encode($this->contentDo->tokenizeData(), JSON_PRETTY_PRINT);
		return $json_string;
	}
}