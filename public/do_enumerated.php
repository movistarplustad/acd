<?php

use Acd\Controller\RolPermissionHttp;
use Acd\Model\EnumeratedLoader;
use Acd\Model\Query;
use Acd\Model\EnumeratedDo;
use Acd\Lib\Request;


require '../config/conf.php';

ini_set('session.gc_maxlifetime', $_ENV[ 'ACD_SESSION_GC_MAXLIFETIME']);
session_start();

if(!RolPermissionHttp::checkUserEditor([$_ENV['ACD_ROL_DEVELOPER']])) die();

$action = strtolower(Request::string($_POST, 'a') ?? '');
$id = Request::string($_POST, 'id') ?? '';
$elements = Request::array($_POST, 'element');

$enumeratedLoader = new EnumeratedLoader();
$query = new Query();
$query->setType('id');
$query->setCondition(['id' => $id]);
$enumerated = $enumeratedLoader->load($query);

$objectFound = $enumerated->getId() != '';

switch ($action) {
	case 'edit':
		$returnUrl = $objectFound ? 'enumerated.php?a=edit&id='.urlencode($id) : '404.html';
		break;
	case 'save':
	case 'new':
		$bIdValid = false;
		if ($action === 'new') {
			$bIdValid = ($objectFound === null && $id !== '');
		}
		else {
			$bIdValid = ($objectFound !== null);
		}
		if($bIdValid) {
			$modifiedEnumerated = new EnumeratedDo();
			$modifiedEnumerated->setId($id);
			$saveItems = array();
			foreach ($elements as $key => $value) {
				if (!isset($elements[$key]['delete'])) {
					// New element
					$elementId = Request::string($value, 'id');
					if ($elementId === null || ($key === $modifiedEnumerated::EMPTY_ID && $elementId === '')) {
						continue;
					}
					$saveItems[$elementId] = Request::string($value, 'description') ?? '';
				}
			}
			$modifiedEnumerated->setItems($saveItems);
			$enumeratedLoader = new EnumeratedLoader();
			$enumeratedLoader->save($modifiedEnumerated);
			$returnUrl = 'enumerated.php?a=edit&r=ok&id='.urlencode($id);
		}
		else {
			$returnUrl = 'enumerated.php?a=edit&r=ko&id='.urlencode($id);
		}
		break;
	case 'delete':
		$enumeratedLoader = new EnumeratedLoader();
		$result = $enumeratedLoader->delete($id) ? 'ok' : 'ko';
		//d("borrar");
		$returnUrl = 'enumerated.php?a=delete&r='.$result;
		break;
	default:
		$returnUrl = '404.html';
		break;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");
