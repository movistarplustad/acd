<?php
namespace Acd\Controller;

use \Acd\View\HeaderMenu;
use \Acd\Model\ContentLoader;
use \Acd\Model\SessionNavigation;
// Output
class AliasId {
	private $aliasId;
	private $aliasIdMatches;
	private $view;
	private $content;
	private $resultDesc;
	private $sessionNavigation;
	private $requestUrl;
	const VIEW_LIST = 'list'; // List of all alias-id matches

	public function __construct() {
		$this->initializeSessionNavigation();
		$this->setAliasIdMatches([]);
	}

	/* Setters and getters attributes */
	public function setAliasId($aliasId) {
		$this->aliasId = (string)$aliasId;
	}
	public function getAliasId() {
		return $this->aliasId;
	}
	public function setView($view) {
		$this->view = (string)$view;
	}
	public function getView() {
		return $this->view;
	}
	public function setResultDesc($resultDesc) {
		$this->resultDesc = $resultDesc;
	}
	public function getResultDesc() {
		return $this->resultDesc;
	}
	private function setContent($content) {
		$this->content = $content;
	}
	private function getContent() {
		return $this->content;
	}
	private function setAliasIdMatches($aliasIdMatches) {
		$this->aliasIdMatches = $aliasIdMatches;
	}
	private function getAliasIdMatches() {
		return $this->aliasIdMatches;
	}
	public function load() {
		$contentLoader = new ContentLoader();
		$matchContentsIds = $contentLoader->loadContent('difuse-alias-id', ['id' => $this->getAliasId()]);

		// Load contents using ids
		$matchContents = [];
		foreach ($matchContentsIds as $dataIds) {
			$contentLoader->setId($dataIds['id_structure']);
			$matchContents[] = $contentLoader->loadContent('id', $dataIds['id']);
		}
		$this->setAliasIdMatches($matchContents);
	}
	public function getTitle() {
		return 'Relations of ';
	}
	// back button
	private function initializeSessionNavigation() {
		$this->sessionNavigation = new SessionNavigation();
		$this->sessionNavigation->load();
	}
	public function setRequestUrl($url) {
		$this->requestUrl = $url;
	}
	public function getHeaderMenuOu() {
		$headerMenuOu = new HeaderMenu();
		$headerMenuOu->setBack(!$this->sessionNavigation->isEmpty());
		return $headerMenuOu;
	}
	public function render() {
		$ou = new \Acd\View\AliasId();
		$ou->setContentTitle($this->getAliasId());
		$ou->setAliasId($this->getAliasId());
		$ou->setMatchList($this->getAliasIdMatches());
		$ou->setResultDesc($this->getResultDesc());

		$this->sessionNavigation->push([
			'hash' => 'aliasId - '.$this->getAliasId(), // Page hash, consecutive same hash no add navigation
			'url' => $this->requestUrl,
			'title' => $this->getTitle().$this->getAliasId()
		]);
		$this->sessionNavigation->save();

		return $ou->render();
	}
}
