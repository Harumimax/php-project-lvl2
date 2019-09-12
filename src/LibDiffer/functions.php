<?php

namespace Differ\LibDiffer;

use Funct;
use Symfony\Component\Yaml\Yaml;

function getDataArray($filePath): array
{
    $fileName = basename($filePath);
    $fileNameArr = explode(".", $fileName);
    $typeOfFile = end($fileNameArr);

    if (mb_strtolower($typeOfFile) == "json") {
        $result = json_decode(file_get_contents($filePath), true);
    } elseif (mb_strtolower($typeOfFile) == "yaml" || mb_strtolower($typeOfFile) == "yml") {
        $result = Yaml::parseFile($filePath);
    } else {
        throw new \Exception("the {$fileName} is {$typeOfFile} format. Its not correct format. Must be JSON or YAML\n");
        exit;
    }

    return $result;
}

function findDiff($beforeArr, $afterArr): array
{
    $result = [];
    foreach ($beforeArr as $key => $value) {
        if (array_key_exists($key, $afterArr)) {
            if ($beforeArr[$key] == $afterArr[$key]) {
                $result[$key] = $value;
            } else {
                $result["+" . "{$key}"] = $afterArr[$key];
                $result["-" . "{$key}"] = $value;
            }
            $afterArr = \Funct\Collection\without($afterArr, $afterArr[$key]);
        } else {
            $result["-" . "{$key}"] = $value;
        }
    }
    foreach ($afterArr as $key => $value) {
        $result["+" . "{$key}"] = $value;
    }
    //print_r($result);
    return $result;
}

function toStr($diffs): string
{
    $resultArr = [];
    foreach ($diffs as $key => $value) {
        if ($key[0] == "-" || $key[0] == "+") {
            $resultArr[] = "   {$key}: {$value}\n";
        } else {
            $resultArr[] = "    {$key}: {$value}\n";
        }
    }
    $resultStr = implode('', $resultArr);
    $str = "{\n{$resultStr}}\n";
    //print_r($str);
    return $str;
}
