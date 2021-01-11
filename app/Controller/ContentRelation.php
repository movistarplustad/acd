<?php

namespace Acd\Controller;

use \Acd\View\HeaderMenu;
use \Acd\Model\SessionNavigation;
// Output
class ContentRelation
{
	private $id;
	private $idStructureTypeSearch;
	private $titleSearch;
	private $idParent;
	private $idStructureTypeParent;
	private $idField;
	private $positionInField;
	private $numPage;
	private $action;
	private $structures;
	private $contentParent;

	public function __construct()
	{
		$this->initializeSessionNavigation();
	}
	// back button
	private function initializeSessionNavigation()
	{
		$this->sessionNavigation = new SessionNavigation();
		$this->sessionNavigation->load();
	}
	public function setRequestUrl($url)
	{
		$this->requestUrl = $url;
	}
	public function setIdContent($id)
	{
		$this->id = $id;
	}
	public function getIdContent()
	{
		return $this->id;
	}
	public function getIdStructureTypeSearch()
	{
		return $this->idStructureTypeSearch;
	}
	public function setIdStructureTypeSearch($idStructureTypeSearch)
	{
		$this->idStructureTypeSearch = $idStructureTypeSearch;
	}
	public function getTitleSearch()
	{
		return $this->titleSearch;
	}
	public function setTitleSearch($titleSearch)
	{
		$this->titleSearch = $titleSearch;
	}
	public function getIdParent()
	{
		return $this->idParent;
	}
	public function setIdParent($idParent)
	{
		$this->idParent = $idParent;
	}
	public function getIdStructureTypeParent()
	{
		return $this->idStructureTypeParent;
	}
	public function setIdStructureTypeParent($idStructureTypeParent)
	{
		$this->idStructureTypeParent = $idStructureTypeParent;
	}
	public function getIdField()
	{
		return $this->idField;
	}
	public function setIdField($idField)
	{
		$this->idField = $idField;
	}
	public function getPositionInField()
	{
		return $this->positionInField;
	}
	public function setPositionInField($positionInField)
	{
		$this->positionInField = $positionInField;
	}
	public function getNumPage()
	{
		return $this->numPage;
	}
	public function setNumPage($numPage)
	{
		$this->numPage = $numPage;
	}
	public function setAction($action)
	{
		$this->action = $action;
	}
	public function getAction()
	{
		return $this->action;
	}
	public function getTitle()
	{
		return 'Add content to ' . $this->contentParent->getTitle() . ' (' . $this->getIdStructureTypeParent() . ' / ' . $this->getIdField() . ')';
	}
	public function getHeaderMenuOu()
	{
		$headerMenuOu = new HeaderMenu();
		$headerMenuOu->setBack(!$this->sessionNavigation->isEmpty());
		return $headerMenuOu;
	}
	public function load()
	{
		$contentLoader = new \Acd\Model\ContentLoader();
		$contentLoader->setId($this->getIdStructureTypeParent());
		$this->contentParent = $contentLoader->loadContent('id', $this->getIdParent());
		$this->structures = $this->contentParent->getFields()->get($this->getIdField())->getRestrictedStructures();
		// Check if the field has restricted structures, otherwise put all available structures
		if ($this->structures->length() === 0) {
			$this->structures->loadFromFile(); // Load all structures
		} else {
			$this->structures->hydratate(); // Load only restricted structures
		}
	}
	public function render()
	{
		$contentOu = new \Acd\View\ContentEditSearch();
		$contentOu->setId($this->getIdParent());
		$contentOu->setType($this->getIdStructureTypeParent());
		$contentOu->setIdField($this->getIdField());
		$contentOu->setPositionInField($this->getPositionInField());
		$contentOu->setStructures($this->structures);
		$contentOu->setTitleSeach($this->getTitleSearch());
		$contentOu->setStructureTypeSeach($this->getIdStructureTypeSearch());

		if ($this->getAction() === 'search') {
			$contentLoader = new \Acd\Model\ContentLoader();
			$contentLoader->setId($this->getIdStructureTypeSearch());
			$whereCondition = [];
			if ($this->getTitleSearch()) {
				$whereCondition['title'] = $this->getTitleSearch();
			}
			if ($this->getIdStructureTypeSearch()) {
				$whereCondition['idStructure'] = $this->getIdStructureTypeSearch();
			}
			$limits = $contentLoader->getLimits();
			$limits->setPage($this->getNumPage());
			$matchContents = $contentLoader->loadContents('editor-search', $whereCondition);
			//d($matchContents);
			$contentOu->setResultSearch($matchContents);
		}
		$this->sessionNavigation->push([
			'hash' => 'content-relation - ' . $this->getIdParent() . ' - ' . $this->getIdStructureTypeParent() . ' - ' . $this->getIdField(), // Page hash, consecutive same hash no add navigation
			'url' => $this->requestUrl,
			'title' => $this->getTitle()
		]);
		$this->sessionNavigation->save();

		return $contentOu->render();
	}
}
