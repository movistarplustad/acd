<?php

namespace Acd\Model\Loaders;

class Loader
{
  private $manager;

  public function __construct()
  {
    $this->manager = $this->getManager();
  }

  public function getManager()
  {
  }

  public function load($query)
  {
    // $dataManager = $this->getManager();
    return $this->getManager()->load($query);
  }

  public function save($enumeratedDo)
  {
    // $dataManager = $this->getManager();
    return $this->manager->save($enumeratedDo);
    // return $NewEnumeratedDo;
  }

  public function delete($manager, $id)
  {
    // $dataManager = $this->getManager();
    return $manager->delete($id);
  }

  // Install
  public function getIndexes($manager)
  {
    // $dataManager = $this->getManager();
    return $manager->getIndexes();
  }

  public function createIndexes($manager)
  {
    // $dataManager = $this->getManager();
    return $manager->createIndexes();
  }

  public function dropIndexes($manager)
  {
    // $dataManager = $this->getManager();
    return $manager->dropIndexes();
  }
}
