<?php
namespace Acd;

class content extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		/* Type */
		// Arrange
		$c = new Model\ContentDo();

		// Act
		$c->setId('foo');

		// Assert
		$this->assertEquals('foo', $c->getId());

		// Act
		$c->setIdStructure('structure foo');

		// Assert
		$this->assertEquals('structure foo', $c->getIdStructure());
	}

// TODO revisar
	public function testLoadContentById() {
		$cl = new Model\ContentLoader();
		$cl->setId('estructura_texto');
		$colContent = $cl->loadContents('byId', '1');
		
		$this->assertEquals('structure foo', $colContent);
	}


	public function testSaveNewContent() {
		$cl = new Model\ContentLoader();
		$cl->setId('programa_tv');
		$content = new \ACD\Model\ContentDo();
		$content->setIdStructure($cl->getId());
		$content->setData('Título', 'el campo Título');
		$content->setData('Destacada', 'el campo Destacada');
		$content->setData('Descripción', 'el campo Descripción');

		$result = $cl->saveContent($content);

		$this->assertTrue($result->getId() !== null);
		$this->assertEquals($result->getData('Título'), 'el campo Título');
	}

	public function testUpdataContent() {
		// Save
		$cl = new \ACD\Model\ContentLoader();
		$cl->setId('programa_tv');
		$content = new \ACD\Model\ContentDo();
		$content->setIdStructure($cl->getId());
		$content->setId('id-fer');
		$content->setData('Título', 'el campo Título');
		$content->setData('Destacada', 'el campo Destacada');
		$content->setData('Descripción', 'el campo Descripción');

		$result = $cl->saveContent($content);

		$this->assertEquals($result->getId(), 'id-fer');
		$this->assertEquals($result->getData('Título'), 'el campo Título');
	}

	public function testDeleteContent() {
		// Delete
		$cl = new \ACD\Model\ContentLoader();
		$cl->setId('programa_tv');

		$content = new \ACD\Model\ContentDo();
		$content->setIdStructure($cl->getId());
		$content->setId('id-demo');
		$content->setData('Título', 'El campo Título');
		$cl->saveContent($content);
		$colContent = $cl->loadContents('id', 'id-demo');

		$cl->deleteContent('id-demo');
		$colContent = $cl->loadContents('id', 'id-demo');

		$this->assertNull($colContent);
	}
}
