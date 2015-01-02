<?php
namespace Acd;

include_once (DIR_BASE.'/tpl/Tools.php');
\Acd\conf::$PATH_AUTH_CREDENTIALS_FILE = DIR_TEST.'/data/auth.json';
\Acd\conf::$DIR_TEMPLATES = DIR_TEST.'/data/tools/';

class tools_ou_test extends \PHPUnit_Framework_TestCase
{
	// ...

	public function testSetData()
	{
		$tools = new Ou\Tools();
		$aCredentials = auth::getCredentials('test_user');
		//var_dump($aCredentials);
		$this->assertEquals($tools->render(), '<strong>Tools</strong>');

	}
}
