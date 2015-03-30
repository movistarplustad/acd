<?php
namespace Acd\Model;

interface iPersistentStructureManager
{
	public function loadAll();
	public function save($structuresDo);
}