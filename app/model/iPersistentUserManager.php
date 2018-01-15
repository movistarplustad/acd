<?php
namespace Acd\Model;

interface iPersistentUserManager
{
    const NO_CONNECTION = 1;
    const UPDATE_FAILED = 2;
    const INSERT_FAILED = 4;
    const DELETE_FAILED = 8;
    const SAVE_FAILED = 16;

    public function initialize(); // Prepare storage to use
    public function isInitialized(); // Inform if the storage it's ready to use
    public function load($query);
    public function save($userDo);
    public function delete($id);
    public function persistSession($userDo); // Return token
    public function loadPersistSession($token);
    public function deletePersistSession($token);
    public function loadUserPersistSessions($id);
}
