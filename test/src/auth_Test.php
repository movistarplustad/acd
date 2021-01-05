<?php
namespace Acd;

//include_once (DIR_BASE.'/app/model/Auth.php');
$DIR_TEST = dirname(__FILE__).'/..';

// Set  routes to data tests
\Acd\conf::$PATH_AUTH_CREDENTIALS_FILE = $DIR_TEST.'/data/auth.json';
\Acd\conf::$PATH_AUTH_PERMANENT_LOGIN_DIR = $DIR_TEST.'/data/auth_permanent_login';

class auth_Test extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		$aCredentials = Model\Auth::loginByCredentials('fer', 'fer', false);
		$this->assertTrue(Model\Auth::loginByCredentials('test_user', 'fer', false));
		//$this->assertTrue(Model\Auth::loginByCredentials('Fer', 'fer', true));
		$this->assertFalse(Model\Auth::loginByCredentials('bad_test_user', 'fer', false));

		$this->assertTrue(Model\Auth::loginByPersintence('test_user', '940e9aac0d740736b18be249a04e518343855a5a'));
		$this->assertFalse(Model\Auth::loginByPersintence('fer', 'fer'));

	}
}
