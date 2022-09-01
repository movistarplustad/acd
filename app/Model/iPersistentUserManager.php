<?php
namespace Acd\Model;

interface iPersistentUserManager
{
    const NO_CONNECTION = 1;
    const UPDATE_FAILED = 2;
    const INSERT_FAILED = 4;
    const DELETE_FAILED = 8;
    const SAVE_FAILED = 16;
	const GET_INDEXES_FAILED = 32;
	const CREATE_INDEXES_FAILED = 64;
	const DROP_INDEXES_FAILED = 128;

    public function initialize(); // Prepare storage to use
    public function isInitialized(); // Inform if the storage it's ready to use
    public function load($query);
    public function save($userDo);
    public function delete($id);
    public function persistSession($userDo); // Return token
    public function loadPersistSession($token);
    public function deletePersistSession($token);
    public function loadUserPersistSessions($id);
	// Install
	public function getIndexes();
    public function createIndexes();
    public function dropIndexes();
}
