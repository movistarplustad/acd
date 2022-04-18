<?php
namespace Acd;
use \Acd\Controller\RolPermissionHttp;

require ('../autoload.php');
ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();
if(!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

$action = strtolower($_POST['a']);
$id = $_POST['id'];
$password = isset($_POST['password']) && $_POST['password'] !== '' ? $_POST['password'] : null; // If password is not setted store a null value
$rol = isset($_POST['rol']) ? $_POST['rol'] : null;

$userLoader = new Model\UserLoader();
$query = new Model\Query();
$query->setType('id');
$query->setCondition(['id' => $id]);
$user = $userLoader->load($query);

$objectFound = ($user !== null);

switch ($action) {
	case 'edit':
		$returnUrl = $objectFound ? 'user.php?a=edit&id='.urlencode($id) : '404.html';
		break;
	case 'delete':
		$userLoader = new Model\UserLoader();
		foreach($userLoader->loadUserPersistSessions($id) as $persistentSession) {
			$userLoader->deletePersistSession($persistentSession->getId());
		}
		$result = $userLoader->delete($id) ? 'ok' : 'ko';
		$returnUrl = 'user.php?a=delete&r='.$result;
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
			$modifiedUser = new Model\UserDo();
			$modifiedUser->setId($id);
			// If the user have not filled keep the old password
			if($password !== null) {
				$modifiedUser->setPassword($password);
			}
			else {
				$modifiedUser->putPassword($user->getPassword());
			}
			$modifiedUser->setRol($rol);
			$userLoader = new Model\UserLoader();
			$userLoader->save($modifiedUser);
			$returnUrl = 'user.php?a=edit&r=ok&id='.urlencode($id);
		}
		else {
			$returnUrl = 'user.php?a=edit&r=ko&id='.urlencode($id);
		}
		break;
	default:
		header("HTTP/1.0 404 Not Found");
		die("Error 404");
		break;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");
