<?php

namespace Acd\Controller;

use \Acd\Model\EnumeratedLoader;
use \Acd\Model\Query;
use \Acd\Model\EnumeratedDo;
use \Acd\Model\SessionNavigation;
use \Acd\View\HeaderMenu;
// Output
class Enumerated
{
	const VIEW_LIST = 'list'; // List of all enumerated collection
	const VIEW_DETAIL = 'edit'; // Detail (values) of a collection
	const VIEW_DETAIL_NEW = 'new'; // New collection
	private  $id;
	private $view;
	private $sessionNavigation;
	private $requestUrl;
	private $contentFound;
	private $title;

	public function __construct()
	{
		$this->initializeSessionNavigation();
	}
	/* Setters and getters attributes */
	public function setId($id)
	{
		$this->id = (string)$id;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setView($view)
	{
		$this->view = (string)$view;
	}
	public function getView()
	{
		return $this->view;
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
	public function setContent($contentFound)
	{
		$this->contentFound = $contentFound;
	}
	public function getContent()
	{
		return $this->contentFound;
	}
	public function getTitle()
	{
		switch ($this->getView()) {
			case $this::VIEW_LIST:
				return 'Collections of enumerated values';
				break;
			case $this::VIEW_DETAIL:
				return 'Enumerated values of ' . $this->getContent()->getId();
				break;
			case $this::VIEW_DETAIL_NEW:
				return 'New collection of enumerated values';
				break;
		}
	}

	public function getHeaderMenuOu()
	{
		$headerMenuOu = new HeaderMenu();
		$headerMenuOu->setBack(!$this->sessionNavigation->isEmpty());
		return $headerMenuOu;
	}

	public function load()
	{
		$enumeratedLoader = new EnumeratedLoader();
		$query = new Query();
		if ($this->getId()) {
			$query->setType('id');
			$query->setCondition(['id' => $this->getId()]);
		} else {
			if (!$this->getView()) {
				$query->setType('all');
				$this->setView($this::VIEW_LIST);
			}
		}
		$this->setContent($enumeratedLoader->load($query));
	}
	public function render()
	{
		switch ($this->getView()) {
			case $this::VIEW_LIST:
				$ou = new \Acd\View\EnumeratedList();
				$ou->setEnumeratedList($this->getContent());
				break;
			case $this::VIEW_DETAIL:
				if ($this->getContent()->getId()) {
					$ou = new \Acd\View\EnumeratedDetail();
					$ou->setEnumeratedElement($this->getContent());
				} else {
					throw new \Exception('Enumerated collection not found', 404);
				}
				break;
			case $this::VIEW_DETAIL_NEW:
				$ou = new \Acd\View\EnumeratedDetail();
				$emptyCollection = new EnumeratedDo();
				$ou->setEnumeratedElement($emptyCollection);
				break;
			default:
				throw new \Exception("View (" . $this->getView() . ") not defined", 1);
				break;
		}

		$this->sessionNavigation->push([
			'hash' => 'enumerated - ' . $this->getView() . ' - ' . $this->getId(), // Page hash, consecutive same hash no add navigation
			'url' => $this->requestUrl,
			'title' => $this->getTitle()
		]);
		$this->sessionNavigation->save();

		return $ou->render();
	}
}
