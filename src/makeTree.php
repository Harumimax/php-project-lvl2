<?php

namespace Differ\makeTree;

use Funct\Collection;

function makeTree($dataBefore, $dataAfter): array
{

    $collectionFromBeforeArr = collect($dataBefore);
    $unionOfTwoArray = $collectionFromBeforeArr->union($dataAfter)->all();
    // Вы правы, через union намного проще строить дерево

    $result = array_map(function ($key, $value) use ($dataBefore, $dataAfter) {
        
        if (array_key_exists($key, $dataAfter) && array_key_exists($key, $dataBefore)) {
            if ($value == $dataAfter[$key]) {
                return ['type' => 'same', 'key' => $key, 'old-value' => $dataBefore[$key],
                'new-value' => $dataAfter[$key], 'children' => null];
            } else {
                if (is_array($value)) {
                    return ['type' => 'nested', 'key' => $key, 'old-value' => null,
                    'new-value' => null, 'children' => makeTree($dataBefore[$key], $dataAfter[$key])];
                } else {
                    return ['type' => 'changed', 'key' => $key, 'old-value' => $dataBefore[$key],
                    'new-value' => $dataAfter[$key], 'children' => null];
                }
            }
        } else {
            if (array_key_exists($key, $dataBefore)) {
                return ['type' => 'deleted', 'key' => $key, 'old-value' => $dataBefore[$key],
                'new-value' => null, 'children' => null];
            } else {
                return ['type' => 'added', 'key' => $key, 'old-value' => null,
                'new-value' => $dataAfter[$key], 'children' => null];
            }
        }
    }, array_keys($unionOfTwoArray), $unionOfTwoArray);

    return $result;
}
