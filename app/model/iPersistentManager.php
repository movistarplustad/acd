<?php
namespace Acd\Model;

interface iPersistentManager
{
	const NO_CONNECTION = 1;
	const UPDATE_FAILED = 2;
	const INSERT_FAILED = 4;
	const DELETE_FAILED = 8;
	const SAVE_FAILED = 16;
	const GET_INDEXES_FAILED = 32;
	const CREATE_INDEXES_FAILED = 64;
	const DROP_INDEXES_FAILED = 128;

	public function initialize($structureDo); // Prepare storage to use
	public function isInitialized($structureDo); // Inform if the storage it's ready to use
	public function load($structureDo, $query);
	public function save($structureDo, $contentDo);
	public function delete($structureDo, $idContent);
	// Install
	public function getIndexes();
	public function createIndexes();
	public function dropIndexes();
}