#!/usr/bin/env php
<?php

use function \Differ\genDiff;

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

//print_r($argv);

try {
    if ($argv[1] == "--format") {
        $beforePath = $argv[3];
        $afterPath = $argv[4];
        $format = $argv[2];
    } else {
        $beforePath = $argv[1];
        $afterPath = $argv[2];
        $format = "pretty";
    }

    echo genDiff($beforePath, $afterPath, $format);
} catch (Exception $e) {
    echo 'Exception: ',  $e->getMessage(), "\n";
}
