<?php
// Compara dos resultados de compare.php y lista las consultas que difieren.
// Uso: php diff.php referencia.json nuevo.json
ini_set("memory_limit", "-1"); // Los resultados ocupan cientos de MB
[$self, $a, $b] = array_pad($argv, 3, null);
if (!$a || !$b) {
	fwrite(STDERR, "Uso: php diff.php referencia.json nuevo.json\n");
	exit(2);
}
$ref = json_decode(file_get_contents($a), true);
$new = json_decode(file_get_contents($b), true);
$diff = array_filter(array_keys($ref + $new), fn($k) => ($ref[$k] ?? null) !== ($new[$k] ?? null));
foreach ($diff as $k) {
	echo "$k\n  ref: " . substr(json_encode($ref[$k] ?? null, JSON_UNESCAPED_UNICODE), 0, 200)
		. "\n  new: " . substr(json_encode($new[$k] ?? null, JSON_UNESCAPED_UNICODE), 0, 200) . "\n";
}
echo count($ref), ' consultas, ', count($diff), " distintas\n";
exit($diff ? 1 : 0);
