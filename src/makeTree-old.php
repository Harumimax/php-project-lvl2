<?php

namespace Differ\makeTree;

use Funct\Collection;

function makeTree($dataBefore, $dataAfter, $level = 1): array
{
    $collectionFromBeforeArr = collect($dataBefore);
    $unionOfTwoArray = $collectionFromBeforeArr->union($dataAfter)->all();
    // Вы правы, через union намного проще строить дерево

    $result = array_map(function ($key, $value) use ($dataBefore, $dataAfter, $level) {
        if (is_bool($value)) {
            $value = changeTypeBoolToStr($value);
        }

        if (array_key_exists($key, $dataAfter) && array_key_exists($key, $dataBefore)) {
            if ($value == $dataAfter[$key]) {
                return ['type' => 'same', 'key' => $key, 'value' => $value, 'level' => $level];
            } else {
                if (is_array($value)) {
                    $arrayValue = makeTree($value, $dataAfter[$key], $level + 1);
                    return ['type' => 'array', 'key' => $key, 'value' => $arrayValue, 'level' => $level];
                } else {
                    return ['type' => 'change', 'key' => $key, 'old-value' => $value,
                            'new-value' => $dataAfter[$key], 'level' => $level];
                }
            }
        } else {
            if (is_array($value)) {
                $value['level'] = $level + 1;
            }

            if (array_key_exists($key, $dataBefore)) {
                return ['type' => 'delete', 'key' => $key, 'value' => $value, 'level' => $level];
            } else {
                return ['type' => 'new', 'key' => $key,'value' => $value, 'level' => $level];
            }
        }
    }, array_keys($unionOfTwoArray), $unionOfTwoArray);

    return $result;
}

function changeTypeBoolToStr(bool $value): string
{
    return ($value) ? "true" : "false";
}
