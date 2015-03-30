<?php
namespace Acd\Model;

interface iPersistentManager
{
	const NO_CONNECTION = 1;
	const UPDATE_FAILED = 2;
	const INSERT_FAILED = 4;
	const DELETE_FAILED = 8;

	public function initialize($structureDo); // Prepare storage to use
	public function isInitialized($structureDo); // Inform if the storage it's ready to use
	public function load($structureDo, $query);
	public function save($structureDo, $contentDo);
	public function delete($structureDo, $idContent);
}