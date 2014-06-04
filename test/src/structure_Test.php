<?php
require_once (DIR_BASE.'/class/structure_do.php');


class structure extends PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		/* Id */
		// Arrange
		$a = new structure_do();

		// Act
		$a->setId('foo id');

		// Assert
		$this->assertEquals('foo id', $a->getId());

		/* Name */
		// Arrange
		$a = new structure_do();

		// Act
		$a->setName('foo name');

		// Assert
		$this->assertEquals('foo name', $a->getName());

		/* Storage */
		// Arrange
		$a = new structure_do();

		// Act
		$a->setStorage('mongodb');

		// Assert
		$this->assertEquals('mongodb', $a->getStorage());

	}

	public function testFields() {
		/* Add */
		$a = new structure_do();
		$idFiedld = $a->addField('foo');
		$field = $a->getFields()->get('foo');
		$this->assertEquals('foo', $field->getType());
	}
}
