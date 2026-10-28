<?php
// Acceso por posición que hace redirecciones (DownloadHandler.php:15, SearchHandler.php:98):
// ContentDo convertido a array, índice 8 = campos; FieldDo convertido a array, índice 3 = valor.
require '/integ/config/conf.php';
use Acd\Model\ContentLoader;
$db = (new \MongoDB\Client($_ENV['ACD_MONGODB_SERVER']))->selectDatabase('acd');
$n = 0; $bad = 0;
foreach ($db->content->find(['data' => ['$ne' => []]], ['limit' => 300, 'projection' => ['id_structure' => 1]]) as $d) {
	$l = new ContentLoader(); $l->setId($d['id_structure']);
	foreach ($l->loadContents('all') as $c) {
		$a = array_values((array) $c); $n++;
		$ok = is_object($a[8]) && method_exists($a[8], 'getIterator') && $a[4] === $c->getAliasId();
		$f = array_values((array) $a[8]->getIterator())[0] ?? null;
		if ($f) { $ok = $ok && array_values((array) $f)[3] === $f->getValue(); }
		if (!$ok) $bad++;
		break;
	}
}
echo "contenidos $n, fallos $bad\n";
echo "ContentDo: " . implode(',', array_map(fn($k) => trim(substr($k, strrpos($k, "\0"))), array_keys((array) $c))) . "\n";
exit($bad || !$n ? 1 : 0);
