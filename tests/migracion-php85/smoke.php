<?php
// Prueba de escritura del ACD sobre una base desechable (ACD_MONGODB_DB): estructuras,
// contenidos con relación y colección, cargas, búsquedas, padres y borrado protegido.
error_reporting(E_ALL);
require '/integ/config/conf.php';

use Acd\Model\StructureDo;
use Acd\Model\StructuresDo;
use Acd\Model\ContentDo;
use Acd\Model\ContentLoader;

if (strpos($_ENV['ACD_MONGODB_DB'], 'smoke') === false) {
	die("Solo se ejecuta contra una base *smoke*\n");
}

function step($name, callable $fn)
{
	try {
		$r = $fn();
		echo "[OK]   $name" . ($r !== null ? ' => ' . json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '') . "\n";
		return $r;
	} catch (\Throwable $e) {
		echo "[FAIL] $name => " . get_class($e) . ': ' . $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine() . "\n";
		return null;
	}
}
function L($s) { $l = new ContentLoader(); $l->setId($s); return $l; }
function titles($contents) {
	if (is_null($contents)) return null;
	$t = [];
	foreach ($contents as $c) { $t[] = $c ? $c->getTitle() : null; }
	return $t;
}
function newContent($idStructure, $title, $alias, $tags, $validity = ['start' => '', 'end' => ''], $profile = [])
{
	$structure = new StructureDo();
	$structure->setId($idStructure);
	$structure->loadFromFile();
	$c = new ContentDo();
	$c->buildSkeleton($structure);
	$c->setTitle($title);
	$c->setPeriodOfValidity($validity);
	$c->setAliasId($alias);
	$c->setTags($tags);
	$c->setProfileValues($profile);
	return $c;
}

(new \MongoDB\Client($_ENV['ACD_MONGODB_SERVER']))->dropDatabase($_ENV['ACD_MONGODB_DB']);
$now = time();

step('guardar estructuras', function () {
	$structures = new StructuresDo();
	foreach ([
		'hijo' => ['name' => 'Hijo', 'fields' => [['texto' => ['type' => 'text_simple', 'name' => 'Texto']]]],
		'padre' => ['name' => 'Padre', 'fields' => [
			['texto' => ['type' => 'text_simple', 'name' => 'Texto']],
			['rel' => ['type' => 'content', 'name' => 'Relacionado']],
			['col' => ['type' => 'collection', 'name' => 'Coleccion']],
		]],
	] as $id => $def) {
		$s = new StructureDo();
		$s->setId($id);
		$s->load(['name' => $def['name'], 'storage' => 'mongodb', 'fields' => $def['fields']]);
		$structures->add($s, $id);
	}
	$structures->save();
	return null;
});
step('cargar estructuras', function () {
	$s = new StructuresDo();
	$s->loadFromFile();
	$out = [];
	foreach ($s->getAllStructures() as $id) {
		$f = [];
		foreach ($s->get($id)->getFields() as $field) $f[] = $field->getId() . ':' . $field->getType();
		$out[$id] = $f;
	}
	ksort($out);
	return $out;
});

$ids = [];
$ids['h1'] = step('crear hijo vigente', function () {
	$c = newContent('hijo', 'Hijo uno', 'hijo-uno', ['portada']);
	$c->setFieldValue('texto', 'texto hijo 1');
	return L('hijo')->saveContent($c)->getId() !== null;
});
$ids['h2'] = step('crear hijo caducado (borrador, end=3600)', function () {
	return L('hijo')->saveContent(newContent('hijo', 'Hijo borrador', 'hijo-borrador', ['portada'], ['start' => '', 'end' => 3600]))->getId() !== null;
});
step('crear hijo con profile LITE', function () {
	return L('hijo')->saveContent(newContent('hijo', 'Hijo lite', 'seccion/hijo-lite', ['otra'], ['start' => '', 'end' => ''], ['LITE']))->getId() !== null;
});
$db = (new \MongoDB\Client($_ENV['ACD_MONGODB_SERVER']))->selectDatabase($_ENV['ACD_MONGODB_DB']);
$h1 = (string) $db->content->findOne(['alias_id' => 'hijo-uno'])['_id'];
$h2 = (string) $db->content->findOne(['alias_id' => 'hijo-borrador'])['_id'];
step('crear padre con relación y colección', function () use ($h1, $h2) {
	$c = newContent('padre', 'Padre Ñandú (test)', 'seccion', ['portada', 'home']);
	$c->setFieldValue('texto', 'texto padre');
	$c->setFieldValue('rel', ['ref' => $h2, 'id_structure' => 'hijo']);
	$c->setFieldValue('col', [['ref' => $h1, 'id_structure' => 'hijo'], ['ref' => $h2, 'id_structure' => 'hijo']]);
	return L('padre')->saveContent($c)->getId() !== null;
});
$pid = (string) $db->content->findOne(['alias_id' => 'seccion'])['_id'];

step('editar padre (título y tags)', function () use ($pid) {
	$c = L('padre')->loadContent('id', $pid);
	$c->setTitle('Padre editado');
	$c->setTags(['portada', 'editado']);
	L('padre')->saveContent($c);
	$r = L('padre')->loadContent('id', $pid);
	return [$r->getTitle(), $r->getTags()];
});
step('id-deep sin fecha', function () use ($pid) {
	$c = L('padre')->loadContent('id-deep', ['id' => $pid, 'depth' => 3]);
	return ['rel' => $c->getFieldValue('rel') ? $c->getFieldValue('rel')->getTitle() : null, 'col' => titles($c->getFieldValue('col'))];
});
step('alias-id-deep con timestamp (filtra borrador)', function () use ($now) {
	$c = L('padre')->loadContent('alias-id-deep', ['id' => 'seccion', 'depth' => 3, 'validity-date' => $now]);
	return ['rel' => $c->getFieldValue('rel') ? $c->getFieldValue('rel')->getTitle() : null, 'col' => titles($c->getFieldValue('col'))];
});
step('alias-id-deep profile LITE', function () { $c = L('hijo')->loadContent('alias-id-deep', ['id' => 'seccion/hijo-lite', 'depth' => 1, 'profile' => 'LITE']); return $c ? $c->getTitle() : null; });
step('alias-id-deep profile OTRO', function () { $c = L('hijo')->loadContent('alias-id-deep', ['id' => 'seccion/hijo-lite', 'depth' => 1, 'profile' => 'OTRO']); return $c ? $c->getTitle() : null; });
step('tag-deep portada con timestamp', function () use ($now) { return titles(L('hijo')->loadContents('tag-deep', ['tags' => ['portada'], 'depth' => 1, 'validity-date' => $now])); });
step('all hijo + total', function () { $l = L('hijo'); $r = $l->loadContents('all'); return ['titulos' => titles($r), 'total' => $l->getLimits()->getTotal()]; });
step('editor-search "(test)"', function () { return titles(L('padre')->loadContents('editor-search', ['all' => '(test)', 'idStructure' => 'padre'])); });
step('countParents hijo uno', function () use ($h1) { return L('hijo')->loadContent('id+countParents', $h1)->getCountParents(); });
step('parents hijo uno', function () use ($h1) { return titles(L('hijo')->loadContents('parents', $h1)); });
step('difuse-alias-id seccion/hijo-lite', function () { return count(L('hijo')->loadContent('difuse-alias-id', ['id' => 'seccion/hijo-lite'])); });
step('borrar hijo con padre (debe rechazarse)', function () use ($h1) { L('hijo')->deleteContent($h1); return 'NO SE RECHAZÓ'; });
step('quitar relaciones del padre y borrar hijo', function () use ($pid, $h1) {
	$c = L('padre')->loadContent('id', $pid);
	$c->setFieldValue('col', []);
	$c->setFieldValue('rel', ['ref' => '', 'id_structure' => '']);
	L('padre')->saveContent($c);
	L('hijo')->deleteContent($h1);
	return L('hijo')->loadContent('id', $h1);
});
step('borrar padre', function () use ($pid) { L('padre')->deleteContent($pid); return L('padre')->loadContent('id', $pid); });
