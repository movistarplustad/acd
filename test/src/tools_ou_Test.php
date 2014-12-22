<?php
include_once (DIR_BASE.'/tpl/tools.php');
conf::$PATH_AUTH_CREDENTIALS_FILE = DIR_TEST.'/data/auth.json';

class tools_ou_test extends PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		$tools = new acd\ou\tools();
		$aCredentials = auth::getCredentials('test_user');
		//var_dump($aCredentials);
		$this->assertEquals($tools->getOutput(), '<strong>Tools</strong>');

	}
}
