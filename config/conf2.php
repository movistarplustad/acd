<?php
// var_dump(__DIR__ . '/..');die;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();