<?php
ini_set('session.gc_maxlifetime', $_ENV['ACD_SESSION_GC_MAXLIFETIME']);
session_start();
require '../config/conf.php';

function dump($name, $variable) {
    echo "\n<br/>$name: $variable";
}
function test($name, $result) {
    echo "\n<br/>$name: ".
    resultToString($result);
}

function resultToString($result) {
    return $result ? "🟢 ok"
    : "🔴 fail";
}
function expectEqual($value, $expectedValue) {
    return $value === $expectedValue;
}
function expectNull($value) {
    return $value === null;
}
function expectString($value) {
    return gettype($value) === "string";
}

echo "<h1>Session</h1>";
dump("session_save_path()", session_save_path());
echo "<h1>Environment variables</h1>";
test (
    "Environment variable ACD_DIR_TEMPLATES is setted",
    expectString($_ENV["ACD_DIR_TEMPLATES"])
);
test (
    "Environment variable ACD_not_setted is not setted",
    expectNull($_ENV["ACD_not_seted"])
);

echo "<h1>Values</h1>";
foreach (get_defined_vars()["_ENV"] as $key => $value) {
    dump($key, $value);
}
