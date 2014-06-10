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
		$f = new field_do();
		$f->setId('foo');
		$f->setName('var');
		$f->setType('text_simple');
		$this->assertEquals('foo', $f->getId());
		$this->assertEquals('var', $f->getName());
		$this->assertEquals('text_simple', $f->getType());

		$idFiedld = $a->addField($f);
		$field = $a->getFields()->get('foo');
		$this->assertEquals('foo', $field->getId());
		$this->assertEquals('var', $field->getName());
		$this->assertEquals('text_simple', $field->getType());
	}
}
