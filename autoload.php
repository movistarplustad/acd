<?php
require ('conf.php');
//http://stackoverflow.com/questions/5280347/autoload-classes-from-different-folders
// autoload classes based on a 1:1 mapping from namespace to directory structure.
spl_autoload_register(function ($className)
{
	// replace to obtain __DIR__/app/namespace/classname.php path
	$aPath = explode('\\', $className);
	$aPath[0] = 'app';
	if (isset($aPath[1] )){
		$aPath[1] = strtolower($aPath[1]);
	}

	// get full name of file containing the required class
	$file = __DIR__.'/'.implode('/', $aPath).'.php';

	// get file if it is readable
	if (is_readable($file)) require_once $file;
});