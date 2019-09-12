#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require('../src/docopt.php');

$params = array(
    'argv' => array_slice($_SERVER['argv'], 1),
    'help' => true,
    'version' => 'GenDiff by harumimax 1.0.1',
    'optionsFirst' => false,
);

$handler = new \Docopt\Handler($params);
$handler->handle($doc, $argv);
print_r($argv);

try {
    echo \Differ\genDiff($argv[1], $argv[2]);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
