<?php
namespace Acd;

use Acd\Model\EnumeratedDo;

class enumerated_test extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		$enum = new EnumeratedDo();
		$id = 'foo';
		$enum->setId($id);
		$this->assertEquals($enum->getId(), $id);

		$data = [
			'foo1' => 'var1',
			'foo2' => 'var2',
		];
		$enum->setItems($data);
		$this->assertEquals($enum->getItems(), $data);
	}

	public function testModifyData()
	{
		$data = ['foo1' => 'var1'];
		$enum = new EnumeratedDo();
		$enum->setItems($data);
		$data = $enum->getItems();
		$this->assertEquals($enum->getItems(), $data);
	}
}
