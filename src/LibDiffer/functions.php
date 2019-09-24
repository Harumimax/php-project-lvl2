<?php

namespace Differ\LibDiffer;

use Funct\Collection;
use Symfony\Component\Yaml\Yaml;

function getDataArray($filePath): array
{
    $fileName = basename($filePath);
    $fileNameArray = explode(".", $fileName);
    $typeOfFile = end($fileNameArray);

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
            $value = changeTypeBoolToStr($value);
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
            unset($afterArr[$key]);
        } else {
                $result[$key] = ['type' => 'delete', 'value' => $value, 'offset' => $offset];
            if (is_array($value)) {
                $result[$key]['value']['offset'] = $offset + 1;
            }
        }
    }

    foreach ($afterArr as $key => $value) {
        if (is_bool($value)) {
            $value = changeTypeBoolToStr($value);
        }

        $result[$key] = ['type' => 'new', 'value' => $value, 'offset' => $offset];
        if (is_array($value)) {
            $result[$key]['value']['offset'] = $offset + 1;
        }
    }
    //print_r($result);

    return $result;
}

function changeTypeBoolToStr(bool $value): string
{
    return ($value) ? "true" : "false";
}

function toStr($diff): string
{
    $resultArray = array_map(function ($key, $value) {
        $offset = str_repeat("  ", $value['offset']);

        if ($value['type'] == 'delete' || $value['type'] == 'new') {
            if (is_array($value['value'])) {
                    $offsetForMap = str_repeat("   ", $value['value']['offset']);
                    $arrayValueForStr = array_map(function ($key, $value) use ($offsetForMap, $offset) {
                        if ($key == 'offset') {
                        } else {
                            return "{$offsetForMap} {$key}: {$value}";
                        }
                    }, array_keys($value['value']), $value['value']);

                    $valueToString = "{\n" . implode("\n", array_diff($arrayValueForStr, array(''))) . "\n{$offset}  }";
            } else {
                $valueToString = $value['value'];
            }
        }

        if ($value['type'] == 'same') {
            return "{$offset}  {$key}: {$value['value']}\n";
        } elseif ($value['type'] == 'delete') {
            return "{$offset}- {$key}: {$valueToString}\n";
        } elseif ($value['type'] == 'new') {
            return "{$offset}+ {$key}: {$valueToString}\n";
        } elseif ($value['type'] == 'change') {
            return ["{$offset}+ {$key}: {$value['new-value']}\n",
                    "{$offset}- {$key}: {$value['old-value']}\n"];
        } elseif ($value['type'] == 'array') {
            $arrayValue = toStr($value['value']);
            return "{$offset}  {$key}: {$arrayValue}\n";
        }
    }, array_keys($diff), $diff);

    $flattenedResultArray = Collection\flattenAll($resultArray);
    $resultToString = implode('', $flattenedResultArray);

    return "{\n{$resultToString}    }";
}

function toPlain($diff, $header = ""): string
{

    $result = array_map(function ($key, $value) use ($header) {

        $newHeader = (empty($header)) ? $key : "{$header}.{$key}";
        if ($value['type'] != 'change') {
            $newValue = (is_array($value['value'])) ? "complex value" : $value['value'];
        } else {
            $newValue = (is_array($value['new-value'])) ? "complex value" : $value['new-value'];
            $oldValue = (is_array($value['old-value'])) ? "complex value" : $value['old-value'];
        }

        if ($value['type'] == 'delete') {
            return "Property '{$newHeader}' was removed";
        } elseif ($value['type'] == 'new') {
            return "Property '{$newHeader}' was added with value: '{$newValue}'";
        } elseif ($value['type'] == 'change') {
            return "Property '{$newHeader}' was changed. From '{$oldValue}' to '{$newValue}'";
        } elseif ($value['type'] == 'array') {
            return toPlain($value['value'], $newHeader);
        }
    }, array_keys($diff), $diff);

    return implode("\n", array_diff($result, array('')));
}

function toSimpleArray(array $diff): array
{
    $result = [];
    foreach ($diff as $key => $value) {
        if ($value['type'] == 'same') {
                $result[$key] = $value['value'];
        } elseif ($value['type'] == 'delete') {
            if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                unset($value['value']['offset']);
            }
              $result["- {$key}"] = $value['value'];
        } elseif ($value['type'] == 'new') {
            if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                unset($value['value']['offset']);
            }
             $result["+ {$key}"] = $value['value'];
        } elseif ($value['type'] == 'change') {
            $result["+ {$key}"] = $value['new-value'];
            $result["- {$key}"] = $value['old-value'];
        } elseif ($value['type'] == 'array') {
            $arrayValue = toSimpleArray($value['value']);
            $result[$key] = $arrayValue;
        }
    }

    return $result;
}
