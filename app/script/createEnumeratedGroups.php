<?php
namespace Acd;
require (__DIR__.'/../../autoload.php');

use Acd\Model\EnumeratedDo;
use Acd\Model\EnumeratedLoader;


$enumeratedLoader = new EnumeratedLoader();

$enumeratedDo = new EnumeratedDo();
$enumeratedDo->setId('PERFIL');
$perfiles = array(
	'LOCALP' => 'Local público',
	'JAZZ' => 'OTT Jazztel',
	'VODAFO' => 'OTT Vodafone',
	'YVICAS' => 'Yomvi Casual',
	'YVISUS' => 'Yomvi Suscriptor',
	'DTHLIG' => 'DTH Residencial con C+ Liga',
	'DTH' => 'DTH Residencial sin C+ Liga'
);
$enumeratedDo->setItems($perfiles);
$enumeratedLoader->save($enumeratedDo);
unset($enumeratedDo);

$enumeratedDo = $enumeratedLoader->load('PERFIL');
s($enumeratedDo);