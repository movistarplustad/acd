<?php

namespace Acd\Controller;

use \Acd\Model\SessionNavigation;
use \Acd\Model\ContentLoader;
use \Acd\Model\UserLoader;
use \Acd\View\HeaderMenu;

// Output
class Install
{
    const VIEW_INFO = 'info'; // Show collection index
    protected $view;
    protected $sessionNavigation;
    protected $requestUrl;

    public function __construct()
    {
        $this->initializeSessionNavigation();
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
    public function load()
    {
        // extra index in collections: content, authPermanent & relation
        $contentLoader = new ContentLoader();
        $userLoader = new UserLoader();
    }
    public function getTitle()
    {
        return 'DB indexes of the collections';
    }
    public function getHeaderMenuOu()
    {
        $headerMenuOu = new HeaderMenu();
        $headerMenuOu->setBack(!$this->sessionNavigation->isEmpty());
        return $headerMenuOu;
    }
    public function render()
    {
        switch ($this->getView()) {
            case $this::VIEW_INFO:
                $ou = new \ACD\View\InstallInfo();
                $contentLoader = new ContentLoader();
                $ou->addCollectionIndexes($contentLoader->getIndexes());
                $userLoader = new UserLoader();
                $ou->addCollectionIndexes($userLoader->getIndexes());
                break;
        }

        $this->sessionNavigation->push([
            'hash' => 'install - ' . $this->getView(), // Page hash, consecutive same hash no add navigation
            'url' => $this->requestUrl,
            'title' => $this->getTitle()
        ]);
        $this->sessionNavigation->save();
        return $ou->render();
    }
}
