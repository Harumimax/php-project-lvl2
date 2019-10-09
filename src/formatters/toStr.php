<?php

namespace Differ\formatters\toStr;

use Funct\Collection;

function toStr($diff): string
{
    return substr(prepareString($diff), 0, -6) . "\n}";
}

function prepareString($diff): string
{
    $resultArray = array_map(function ($value) {
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
            return "{$offset}  {$value['key']}: {$value['value']}\n";
        } elseif ($value['type'] == 'delete') {
            return "{$offset}- {$value['key']}: {$valueToString}\n";
        } elseif ($value['type'] == 'new') {
            return "{$offset}+ {$value['key']}: {$valueToString}\n";
        } elseif ($value['type'] == 'change') {
            return ["{$offset}+ {$value['key']}: {$value['new-value']}\n",
                    "{$offset}- {$value['key']}: {$value['old-value']}\n"];
        } elseif ($value['type'] == 'array') {
            $arrayValue = toStr($value['value']);
            return "{$offset}  {$value['key']}: {$arrayValue}\n";
        }
    }, $diff);

    $flattenedResultArray = Collection\flattenAll($resultArray);
    $resultToString = implode('', $flattenedResultArray);

    return "{\n{$resultToString}    }";
}
