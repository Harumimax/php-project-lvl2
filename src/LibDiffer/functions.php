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
        if (array_key_exists($key, $afterArr)) { // есть ли ключ во втором массиве?
            if (!is_array($value)) { // значение не массив, а значение?
                if ($beforeArr[$key] == $afterArr[$key]) {  // значения двух массивов равны
                    $result[$key] = $value;
                } else {  // значения двух массиво не равны, оба заносим в дифф
                    $result["+" . "{$key}"] = $afterArr[$key];
                    $result["-" . "{$key}"] = $value;
                }
                //$afterArr = \Funct\Collection\without($afterArr, $afterArr[$key]);
            } else { // значение массив
                if ($value == $afterArr[$key]) {  // два массива одинаковые
                    $result[$key] = $value;
                } else {  // два массива не одинаковые
                    $result[$key] = findDiff($value, $afterArr[$key], $offset + 1);
                }
            }
            unset($afterArr[$key]); // удаляем значение из второго массива по ключу
        } else { //ключа нет во втором массиве, значит значение было удалено из второго массива, возврат с -
            if (is_array($value)) {
                $value['offset'] = $offset + 1;
            }
            $result["-" . "{$key}"] = $value;
        }
    }
        
    foreach ($afterArr as $key => $value) {
        if (is_array($value)) {
            $value['offset'] = $offset + 1;
        }
        $result["+" . "{$key}"] = $value;
    }
        
    $result['offset'] = $offset;

    return $result;
}

function toStr($diffs, $keyOfname = ""): string
{
    
    $resultArr = [];
    foreach ($diffs as $key => $value) {
        if ($key !== 'offset') {
            $offsetInStr = str_repeat("   ", $diffs['offset']);
            if (!is_array($value)) {
                if ($key[0] == "-" || $key[0] == "+") {
                    $resultArr[] = "{$offsetInStr}{$key}: {$value}\n";
                } else {
                    $resultArr[] = "{$offsetInStr} {$key}: {$value}\n";
                }
            } else {
                $str = toStr($value, $key);
                $resultArr[$key] = "{$offsetInStr}{$key}: {$str}";
            }
        }
    }

    $resultStr = implode('', $resultArr);
    $offsetForPair = str_repeat("   ", $diffs['offset'] - 1);
    
    if (!empty($keyOfname)) {
        if ($keyOfname[0] == "-" || $keyOfname[0] == "+") {
            $offsetForPair = " {$offsetForPair}";
        }
    }

    return "{\n{$resultStr}{$offsetForPair}}\n";
}
