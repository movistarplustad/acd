<?php
namespace Acd\Model;

class PersistentEnumeratedException extends \exception {}
interface iPersistentEnumeratedManager
{
	const NO_CONNECTION = 1;
	const UPDATE_FAILED = 2;
	const INSERT_FAILED = 4;
	const DELETE_FAILED = 8;
	const SAVE_FAILED = 16;

	public function initialize(); // Prepare storage to use
	public function isInitialized(); // Inform if the storage it's ready to use
	public function load($id);
	public function save($EnumeratedDo);
	// Install. Currently no indexes are required
	public function getIndexes();
	public function createIndexes();
	public function dropIndexes();
}