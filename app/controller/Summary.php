<?php

namespace Acd\Controller;
// Output
class Summary
{
	private $idContent;
	private $idStructure;
	private $contentDo;

	/* Setters and getters attributes */
	public function setIdContent($id)
	{
		$this->id = (string)$id;
	}
	public function getIdContent()
	{
		return $this->id;
	}
	public function setIdStructure($idStructure)
	{
		$this->idStructure = (string)$idStructure;
	}
	public function getIdStructure()
	{
		return $this->idStructure;
	}
	public function load()
	{
		$contentLoader = new \Acd\Model\ContentLoader();
		$contentLoader->setId($this->getIdStructure());
		$this->contentDo = $contentLoader->loadContent('id-deep', ['id' => $this->getIdContent(), 'depth' => 20]);
		return $this->contentDo;
	}
	public function render()
	{
		// INF, -INF and NaN conversion to json problems
		//$json_string = json_encode($this->contentDo->tokenizeData(), JSON_PRETTY_PRINT);
		//return $json_string;
		$data_string = print_r($this->contentDo->tokenizeData(), true);
		return $data_string;
	}
}
