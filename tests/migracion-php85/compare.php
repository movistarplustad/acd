<?php
// Ejecuta las mismas consultas de solo lectura con una base de código ACD y
// escribe un JSON por consulta, para comparar esta rama (integ, montada en /integ)
// con otra versión (neo, por su autoload). Compatible con PHP 7.2.
// Uso: php compare.php integ|neo <rol> <fichero_salida> [mongodb://servidor] [autoload de neo]
// Variables: ACD_MONGODB_SERVER, ACD_TEST_DATE (timestamp de vigencia), ACD_TEST_ALL_ERRORS=1
error_reporting(getenv('ACD_TEST_ALL_ERRORS') ? E_ALL : (E_ALL & ~E_DEPRECATED));
list($self, $which, $rol, $outFile, $mongo, $neoPath) = array_pad($argv, 6, null);
$mongo = $mongo ?: 'mongodb://acd-e2e-mongo:27017';

if ($which === 'neo') {
	require $neoPath;
	\Acd\conf::$MONGODB_SERVER = $mongo;
	\Acd\conf::$MONGODB_DB = 'acd';
	\Acd\conf::$DEFAULT_STORAGE = 'mongodb';
	$ROL_EDITOR = \Acd\conf::$ROL_EDITOR;
	$ROL_DEVELOPER = \Acd\conf::$ROL_DEVELOPER;
} else {
	require '/integ/config/conf.php';
	$ROL_EDITOR = $_ENV['ACD_ROL_EDITOR'];
	$ROL_DEVELOPER = $_ENV['ACD_ROL_DEVELOPER'];
}
$_SESSION['rol'] = $rol === 'editor' ? $ROL_EDITOR : $ROL_DEVELOPER;

use Acd\Model\ContentLoader;
use Acd\Model\StructuresDo;

function ser($v, $depth = 0)
{
	if ($depth > 6) return '…';
	if ($v instanceof \Acd\Model\ContentDo) {
		$fields = [];
		foreach ($v->getFields() as $f) {
			$fields[$f->getId()] = ser($f->getValue(), $depth + 1);
		}
		ksort($fields);
		return ['id' => $v->getId(), 's' => $v->getIdStructure(), 't' => $v->getTitle(), 'a' => $v->getAliasId(),
			'tags' => $v->getTags(), 'pov' => $v->getPeriodOfValidity(), 'f' => $fields];
	}
	if ($v instanceof \Acd\Model\ContentsDo) {
		$o = [];
		foreach ($v as $c) $o[] = ser($c, $depth + 1);
		return ['contents' => $o];
	}
	if ($v instanceof \Traversable) $v = iterator_to_array($v);
	if (is_array($v)) { $o = []; foreach ($v as $k => $x) $o[$k] = ser($x, $depth + 1); return $o; }
	if (is_object($v)) return get_class($v) . ':' . json_encode($v);
	return $v;
}

$out = [];
function q($name, callable $fn)
{
	global $out;
	try {
		$r = $fn();
		$out[$name] = ser($r);
	} catch (\Throwable $e) {
		$out[$name] = 'EXC ' . get_class($e) . ': ' . $e->getMessage();
	}
}
function L($s) { $l = new ContentLoader(); $l->setId($s); return $l; }
function total($s, $method, $params) { $l = L($s); $r = $l->loadContents($method, $params); return ['n' => $r ? $r->length() : null, 'total' => $l->getLimits()->getTotal(), 'r' => ser($r)]; }

$db = (new \MongoDB\Client(getenv('ACD_MONGODB_SERVER') ?: $mongo))->selectDatabase('acd');
$date = getenv('ACD_TEST_DATE') ?: '2026-10-28 00:00:00';
if (ctype_digit($date)) $date = (int) $date;

// Estructuras
q('structures', function () {
	$s = new StructuresDo();
	$s->loadFromFile();
	$o = [];
	foreach ($s->getAllStructures() as $id) {
		$f = [];
		foreach ($s->get($id)->getFields() as $field) $f[] = $field->getId() . ':' . $field->getType();
		$o[$id] = [$s->get($id)->getName(), $s->get($id)->getStorage(), $f];
	}
	ksort($o);
	return $o;
});

// Muestra determinista: los 8 primeros contenidos (por _id) de cada estructura
$sample = [];
foreach ($db->content->distinct('id_structure') as $st) {
	foreach ($db->content->find(['id_structure' => $st], ['sort' => ['_id' => 1], 'limit' => 8, 'projection' => ['_id' => 1, 'alias_id' => 1]]) as $d) {
		$sample[] = [$st, (string) $d['_id'], $d['alias_id'] ?? ''];
	}
}
sort($sample);
foreach ($sample as [$st, $id, $alias]) {
	q("id|$st|$id", function () use ($id, $st) { return L($st)->loadContent('id', $id); });
	q("id-deep|$st|$id", function () use ($id, $st) { return L($st)->loadContent('id-deep', ['id' => $id, 'depth' => 3]); });
	q("id-deep-date|$st|$id", function () use ($id, $st) { return L($st)->loadContent('id-deep', ['id' => $id, 'depth' => 3, 'validity-date' => $GLOBALS['date']]); });
	q("countParents|$st|$id", function () use ($id, $st) { $c = L($st)->loadContent('id+countParents', $id); return $c ? $c->getCountParents() : null; });
	q("parents|$st|$id", function () use ($id, $st) { return L($st)->loadContents('parents', $id); });
	if ($alias !== '') {
		q("alias-id-deep|$st|$alias", function () use ($alias, $st) { return L($st)->loadContent('alias-id-deep', ['id' => $alias, 'depth' => 2, 'validity-date' => $GLOBALS['date']]); });
		q("difuse|$st|$alias", function () use ($alias, $st) { return L($st)->loadContent('difuse-alias-id', ['id' => $alias]); });
	}
}

// Etiquetas
foreach (['pagina', 'item_destacado', 'video', 'blog_post', 'noticia'] as $st) {
	foreach (['migracion', 'portadilla', 'blog', 'cine', 'pagina_portada'] as $tag) {
		q("tag-deep|$st|$tag", function () use ($st, $tag) { return total($st, 'tag-deep', ['tags' => [$tag], 'depth' => 1]); });
		q("tag-deep-date|$st|$tag", function () use ($st, $tag) { return total($st, 'tag-deep', ['tags' => [$tag], 'depth' => 1, 'validity-date' => $GLOBALS['date']]); });
		q("tag-deep-asc|$st|$tag", function () use ($st, $tag) { return total($st, 'tag-deep', ['tags' => [$tag], 'depth' => 1, 'order' => 'asc']); });
		q("tag-one-deep|$st|$tag", function () use ($st, $tag) { return L($st)->loadContent('tag-one-deep', ['tags' => [$tag], 'depth' => 1]); });
	}
}

// Listados y búsqueda del editor
foreach (['pagina', 'blog_post', 'redireccion', 'video', 'enlace_corto', 'menu'] as $st) {
	q("all|$st", function () use ($st) { return total($st, 'all', null); });
	foreach (['portada', 'a.b', '(', 'blog', 'ñ', 'home/', '*'] as $s) {
		q("editor-search|$st|$s", function () use ($s, $st) { return total($st, 'editor-search', ['all' => $s, 'idStructure' => $st]); });
	}
}

// Acortador de enlaces
foreach ([[], ['q' => 'movistar'], ['q' => 'promo'], ['validity-from' => '2025-01-01 00:00:00'], ['validity-to' => '2024-01-01 00:00:00'], ['validity-date' => $date]] as $i => $c) {
	q("shortlink|$i", function () use ($c) { return total('enlace_corto', 'shortlink-search', $c); });
}
foreach ($db->content->find(['id_structure' => 'enlace_corto'], ['limit' => 5, 'sort' => ['_id' => 1]]) as $d) {
	$a = $d['alias_id'] ?? '';
	q("shortlink-alias|$a", function () use ($a) { return L('enlace_corto')->loadContents('shortlink-search', ['alias_id' => $a]); });
}

file_put_contents($outFile, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
echo count($out), " consultas\n";
