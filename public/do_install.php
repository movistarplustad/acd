<?php

namespace Acd;

require('../autoload.php');

use \Acd\Model\ContentLoader;
use \Acd\Model\UserLoader;
use \Acd\Controller\RolPermissionHttp;

ini_set('session.gc_maxlifetime', conf::$SESSION_GC_MAXLIFETIME);
session_start();

if (!RolPermissionHttp::checkUserEditor([\Acd\conf::$ROL_DEVELOPER])) die();

switch($_POST['a']) {
    case 'Create DB indexes':
        $total = 0;
        $contentLoader = new ContentLoader();
        $res = $contentLoader->createIndexes();
        $total += count($res);

        $userLoader = new UserLoader();
        $res = $userLoader->createIndexes();
        $total += count($res);

        $returnUrl = "install.php?r=ok&t=create&res=$total";
        break;
    case 'Drop DB indexes':
        $res = true;
        $contentLoader = new ContentLoader();
        $res = $res && $contentLoader->dropIndexes();

        $userLoader = new UserLoader();
        $res = $res && $userLoader->dropIndexes();

        $res = $res ? 'ok' : 'ko';
        $returnUrl = "install.php?r=$res&t=drop";
        break;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location:$returnUrl");
