<?php

namespace Differ\functions;

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
    }

    return $result;
}

function findDiff($beforeArr, $afterArr, $offset = 1): array
{
    $prepareTreeFromBeforeArr = array_map(function ($key, $value) use ($afterArr, $offset) {
        if (is_bool($value)) {
            $value = changeTypeBoolToStr($value);
        }

        if (array_key_exists($key, $afterArr)) {
            if ($value == $afterArr[$key]) {
                    return ['type' => 'same', 'key' => $key, 'value' => $value, 'offset' => $offset];
            } else {
                if (is_array($value)) {
                    $arrayValue = findDiff($value, $afterArr[$key], $offset + 1);
                    return ['type' => 'array', 'key' => $key, 'value' => $arrayValue, 'offset' => $offset];
                } else {
                    return
                    ['type' => 'change', 'key' => $key, 'old-value' => $value,
                    'new-value' => $afterArr[$key], 'offset' => $offset];
                }
            }
        } else {
            if (is_array($value)) {
                $value['offset'] = $offset + 1;
            }
            return ['type' => 'delete', 'key' => $key, 'value' => $value, 'offset' => $offset];
        }
    }, array_keys($beforeArr), $beforeArr);

    $diffBetweenTwoArray = array_diff_key($afterArr, $beforeArr);

    $newValuesFromAfterArr = array_map(function ($key, $value) use ($offset) {
        if (is_bool($value)) {
            $value = changeTypeBoolToStr($value);
        }

        if (is_array($value)) {
            $value['offset'] = $offset + 1;
        }

        return ['type' => 'new', 'key' => $key,'value' => $value, 'offset' => $offset];
    }, array_keys($diffBetweenTwoArray), $diffBetweenTwoArray);

    return array_merge($prepareTreeFromBeforeArr, $newValuesFromAfterArr);
}

function changeTypeBoolToStr(bool $value): string
{
    return ($value) ? "true" : "false";
}
