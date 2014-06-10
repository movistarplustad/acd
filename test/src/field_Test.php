<?php
include_once (DIR_BASE.'/class/field_do.php');

class field extends PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		/* Type */
		// Arrange
		$a = new field_do();

		// Act
		$a->setId('foo');

		// Assert
		$this->assertEquals('foo', $a->getId());

		// Act
		$a->setType('text_simple');

		// Assert
		$this->assertEquals('text_simple', $a->getType());

		/* Name */
		// Arrange
		$a = new structure_do();

		// Act
		$a->setName('foo name');

		// Assert
		$this->assertEquals('foo name', $a->getName());

	}
}
