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

function findDiff($beforeArr, $afterArr, $offset = 1): array
{
    $result = [];
    foreach ($beforeArr as $key => $value) {
        if (is_bool($value)) {
            $value = changeType($value);
        }

        if (array_key_exists($key, $afterArr)) {
            if ($value == $afterArr[$key]) {
                    $result[$key] = ['type' => 'same', 'value' => $value, 'offset' => $offset];
            } else {
                if (is_array($value)) {
                    $arrayValue = findDiff($value, $afterArr[$key], $offset + 1);
                    $result[$key] = ['type' => 'array', 'value' => $arrayValue, 'offset' => $offset];
                } else {
                    $result[$key] =
                    ['type' => 'change', 'old-value' => $value, 'new-value' => $afterArr[$key], 'offset' => $offset];
                }
            }
                unset($afterArr[$key]); // удаляем значение из второго массива по ключу
        } else {
                $result[$key] = ['type' => 'delete', 'value' => $value, 'offset' => $offset];
            if (is_array($value)) {
                $result[$key]['value']['offset'] = $offset + 1;
            }
        }
    }

    foreach ($afterArr as $key => $value) {
        if (is_bool($value)) {
            $value = changeType($value);
        }

        $result[$key] = ['type' => 'new', 'value' => $value, 'offset' => $offset];
        if (is_array($value)) {
            $result[$key]['value']['offset'] = $offset + 1;
        }
    }
    //print_r($result);

    return $result;
}

function changeType(bool $value): string
{
    return ($value) ? "true" : "false";
}

function toStr($diffs): string
{

    $resultArr = [];
    foreach ($diffs as $key => $value) {
        $offsetInStr = str_repeat("  ", $value['offset']);

        if ($value['type'] == 'delete' || $value['type'] == 'new') {
            if (is_array($value['value'])) {
                    $offsetForFunction = str_repeat("   ", $value['value']['offset']);
                    $arrayValueInStr = array_map(function ($key, $value) use ($offsetForFunction, $offsetInStr) {
                        if ($key == 'offset') {
                        } else {
                            return "{$offsetForFunction} {$key}: {$value}";
                        }
                    }, array_keys($value['value']), $value['value']);

                    $strValue = "{\n" . implode("\n", array_diff($arrayValueInStr, array(''))) . "\n{$offsetInStr}  }";
            } else {
                $strValue = $value['value'];
            }
        }


        if ($value['type'] == 'same') {
            $resultArr[] = "{$offsetInStr}  {$key}: {$value['value']}\n";
        } elseif ($value['type'] == 'delete') {
            $resultArr[] = "{$offsetInStr}- {$key}: {$strValue}\n";
        } elseif ($value['type'] == 'new') {
            $resultArr[] = "{$offsetInStr}+ {$key}: {$strValue}\n";
        } elseif ($value['type'] == 'change') {
            $resultArr[] = "{$offsetInStr}+ {$key}: {$value['new-value']}\n";
            $resultArr[] = "{$offsetInStr}- {$key}: {$value['old-value']}\n";
        } elseif ($value['type'] == 'array') {
            $arrayValue = toStr($value['value']);
            $resultArr[] = "{$offsetInStr}  {$key}: {$arrayValue}\n";
        }
    }

    $resultStr = implode('', $resultArr);
    
    return "{\n{$resultStr}{$offsetInStr}}";
}

function toPlain($diffs, $rootKey = ""): string
{
    $resultArr = [];
    foreach ($diffs as $key => $value) {
        $rootKeys = (empty($rootKey)) ? $key : "{$rootKey}.{$key}";

        if ($value['type'] != 'change') {
            $newValue = (is_array($value['value'])) ? "complex value" : $value['value'];
        } else {
            $newValue = (is_array($value['new-value'])) ? "complex value" : $value['new-value'];
            $oldValue = (is_array($value['old-value'])) ? "complex value" : $value['old-value'];
        }
        
        if ($value['type'] == 'delete') {
            $resultArr[] = "Property '{$rootKeys}' was removed";
        } elseif ($value['type'] == 'new') {
            $resultArr[] = "Property '{$rootKeys}' was added with value: '{$newValue}'";
        } elseif ($value['type'] == 'change') {
            $resultArr[] = "Property '{$rootKeys}' was changed. From '{$oldValue}' to '{$newValue}'";
        } elseif ($value['type'] == 'array') {
            $resultArr[] = toPlain($value['value'], $rootKeys);
        }
    }

    return implode("\n", $resultArr);
}
