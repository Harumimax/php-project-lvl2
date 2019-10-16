<?php

namespace Differ\makeTree;

use Funct\Collection;

function makeTree($beforeArr, $afterArr, $offset = 1): array
{
    $collectionFromBeforeArr = collect($beforeArr);
    $unionOfTwoArray = $collectionFromBeforeArr->union($afterArr)->all();
    // Вы правы, через union намного проще строить дерево

    $result = array_map(function ($key, $value) use ($beforeArr, $afterArr, $offset) {
        if (is_bool($value)) {
            $value = changeTypeBoolToStr($value);
        }

        if (array_key_exists($key, $afterArr) && array_key_exists($key, $beforeArr)) {
            if ($value == $afterArr[$key]) {
                return ['type' => 'same', 'key' => $key, 'value' => $value, 'offset' => $offset];
            } else {
                if (is_array($value)) {
                    $arrayValue = makeTree($value, $afterArr[$key], $offset + 1);
                    return ['type' => 'array', 'key' => $key, 'value' => $arrayValue, 'offset' => $offset];
                } else {
                    return ['type' => 'change', 'key' => $key, 'old-value' => $value,
                            'new-value' => $afterArr[$key], 'offset' => $offset];
                }
            }
        } else {
            if (is_array($value)) {
                $value['offset'] = $offset + 1;
            }

            if (array_key_exists($key, $beforeArr)) {
                return ['type' => 'delete', 'key' => $key, 'value' => $value, 'offset' => $offset];
            } else {
                return ['type' => 'new', 'key' => $key,'value' => $value, 'offset' => $offset];
            }
        }
    }, array_keys($unionOfTwoArray), $unionOfTwoArray);

    return $result;
}

function changeTypeBoolToStr(bool $value): string
{
    return ($value) ? "true" : "false";
}
