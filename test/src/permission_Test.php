<?php
namespace Acd;

include_once (DIR_BASE.'/class/permission.php');

class permission_Test extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		/* Type */
		// Arrange
		$a = new permission();
		$a->load();

		// Act
		$this->assertTrue($a->hasAccess('developer', 'login'));
		$this->assertTrue($a->hasAccess('developer', 'edit-structures'));
		$this->assertTrue($a->hasAccess('developer', 'manage-content'));

		// Act
		$this->assertTrue($a->hasAccess('editor', 'login'));
		$this->assertFalse($a->hasAccess('editor', 'edit-structures'));
		$this->assertTrue($a->hasAccess('editor', 'manage-content'));
	}
}
