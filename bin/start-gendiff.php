#!/usr/bin/env php
<?php

use \Differ\GenDiff;

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



if (file_exists($argv[1]) && file_exists($argv[2])) {
    $differ = new GenDiff(file_get_contents($argv[1]), file_get_contents($argv[2]));
} else {
    echo "one or both files do not exist\n";
    exit;
}

$differ->makeDiff();
$result = $differ->toStr();
print_r($result);

/* makeDiff
$result = [];
foreach($beforeArr as $key => $value) {
    if (array_key_exists($key, $afterArr)) {

        if ($beforeArr[$key] == $afterArr[$key]) {
            $result[$key] = $value;
        } else {
            $result["+"."{$key}"] = $afterArr[$key];
            $result["-"."{$key}"] = $value;
        }
        $afterArr = \Funct\Collection\without($afterArr, $afterArr[$key]);
    } else {
        $result["-"."{$key}"] = $value;
    }
}
foreach($afterArr as $key => $value){
    $result["+"."{$key}"] = $value;
}
print_r($result);
*/

/* toStr
foreach($result as $key => $value) {
    if ($key[0] == "-" || $key[0] == "+") {
        $resultArr[] = "   {$key}: {$value}\n";
    } else {
        $resultArr[] = "    {$key}: {$value}\n";
    }
}

$resultStr = implode('', $resultArr);
$str = "{\n{$resultStr}}\n";
print_r($str);
*/

//print_r(json_encode($result));

//print_r("\n{\n   text: text\n   text: text\n   text: text\n}");

//print_r(genDiff($argv));
