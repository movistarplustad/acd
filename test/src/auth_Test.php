<?php
include_once (DIR_BASE.'/class/auth.php');
// Set  routes to data tests
conf::$PATH_AUTH_CREDENTIALS_FILE = DIR_TEST.'/data/auth.json';
conf::$PATH_AUTH_PREMANENT_LOGIN_DIR = DIR_TEST.'/data/auth_permanent_login';

class auth_test extends PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		$aCredentials = auth::loginByCredentials('fer', 'fer', false);
		$this->assertTrue(auth::loginByCredentials('test_user', 'fer', false));
		//$this->assertTrue(auth::loginByCredentials('Fer', 'fer', true));
		$this->assertFalse(auth::loginByCredentials('bad_test_user', 'fer', false));

		$this->assertTrue(auth::loginByPersintence('test_user', '940e9aac0d740736b18be249a04e518343855a5a'));
		$this->assertFalse(auth::loginByPersintence('fer', 'fer'));

	}
}
