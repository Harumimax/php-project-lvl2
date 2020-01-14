<?php

namespace Differ\makeTree;

use Funct\Collection;

function makeTree($dataBefore, $dataAfter): array
{

    $collectionFromBeforeArr = collect($dataBefore);
    $result = $collectionFromBeforeArr->union($dataAfter)->map(function ($item, $key) use ($dataBefore, $dataAfter) {
        if (array_key_exists($key, $dataAfter) && array_key_exists($key, $dataBefore)) {
            if ($item == $dataAfter[$key]) {
                return ['type' => 'same', 'key' => $key, 'oldValue' => $dataBefore[$key],
                'newValue' => $dataAfter[$key], 'children' => null];
            } else {
                if (is_array($item)) {
                    return ['type' => 'nested', 'key' => $key, 'oldValue' => null,
                    'newValue' => null, 'children' => makeTree($dataBefore[$key], $dataAfter[$key])];
                } else {
                    return ['type' => 'changed', 'key' => $key, 'oldValue' => $dataBefore[$key],
                    'newValue' => $dataAfter[$key], 'children' => null];
                }
            }
        } else {
            if (array_key_exists($key, $dataBefore)) {
                return ['type' => 'deleted', 'key' => $key, 'oldValue' => $dataBefore[$key],
                'newValue' => null, 'children' => null];
            } else {
                return ['type' => 'added', 'key' => $key, 'oldValue' => null,
                'newValue' => $dataAfter[$key], 'children' => null];
            }
        }
    })->all();

    return $result;
}
