<?php
namespace Acd;

require_once (DIR_BASE.'/app/model/StructureDo.php');
require_once (DIR_BASE.'/app/model/FieldDo.php');


class structure extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		/* Id */
		// Arrange
		$a = new Model\StructureDo();

		// Act
		$a->setId('foo id');

		// Assert
		$this->assertEquals('foo id', $a->getId());

		/* Name */
		// Arrange
		$a = new Model\StructureDo();

		// Act
		$a->setName('foo name');

		// Assert
		$this->assertEquals('foo name', $a->getName());

		/* Storage */
		// Arrange
		$a = new Model\StructureDo();

		// Act
		$a->setStorage('mongodb');

		// Assert
		$this->assertEquals('mongodb', $a->getStorage());

	}

	public function testFields() {
		/* Add */
		$a = new Model\StructureDo();
		$f = new Model\FieldDo();
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

	public function testLoadFromFile() {
		$a = new Model\StructureDo();
		$a->setId('chat_tienda');
		// Formerly loadFromFile(DIR_BASE.'/test/data/structures_demo.json');
		// TODO: Change to inject dataManager
		$a->loadFromFile();
		$this->assertEquals('chat_tienda', $a->getId());
		$this->assertEquals('Chat de tienda online', $a->getName());
		$this->assertEquals('mongodb', $a->getStorage());

		$field = $a->getFields()->get('foo');
		$this->assertEquals('foo', $field->getId());
		$this->assertEquals('pretty', $field->getName());
		$this->assertEquals('text_simple', $field->getType());

		$field = $a->getFields()->get('abierta');
		$this->assertEquals('abierta', $field->getId());
		$this->assertEquals('Abierta', $field->getName());
		$this->assertEquals('boolean', $field->getType());

		$this->setExpectedException('\Acd\Model\KeyInvalidException');
		$fields = $a->getFields()->get('no_exists');
	}
}
