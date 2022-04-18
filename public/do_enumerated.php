<?php
namespace Acd;
use \Acd\Controller\RolPermissionHttp;

require ('../autoload.php');

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

$action = strtolower($_POST['a']);
$id = $_POST['id'];
$elements = isset($_POST['element']) ? $_POST['element'] : array();

$enumeratedLoader = new Model\EnumeratedLoader();
$query = new Model\Query();
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
			$modifiedEnumerated = new Model\EnumeratedDo();
			$modifiedEnumerated->setId($id);
			$saveItems = array();
			foreach ($elements as $key => $value) {
				if (!isset($elements[$key]['delete'])) {
					// New element
					if ($key === $modifiedEnumerated::EMPTY_ID && $elements[$key]['id'] === '') {
						continue;
					}
					$saveItems[$elements[$key]['id']] = $elements[$key]['description'];
				}
			}
			$modifiedEnumerated->setItems($saveItems);
			$enumeratedLoader = new Model\EnumeratedLoader();
			$enumeratedLoader->save($modifiedEnumerated);
			$returnUrl = 'enumerated.php?a=edit&r=ok&id='.urlencode($id);
		}
		else {
			$returnUrl = 'enumerated.php?a=edit&r=ko&id='.urlencode($id);
		}
		break;
	case 'delete':
		$enumeratedLoader = new Model\EnumeratedLoader();
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
