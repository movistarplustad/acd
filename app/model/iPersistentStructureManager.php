<?php
namespace Acd\Model;

class PersistentStorageQueryTypeNotImplemented extends \exception {}
interface iPersistentStructureManager
{
	public function loadAll();
	public function save($structuresDo);
}