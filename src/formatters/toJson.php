<?php

namespace Differ\formatters\toJson;

function toJson($diff)
{
    return json_encode(toSimpleArray($diff));
}

function toSimpleArray(array $diff): array
{
    $result = [];
    foreach ($diff as $key => $value) {
        if ($value['type'] == 'same') {
            $result[$value['key']] = $value['value'];
        } elseif ($value['type'] == 'delete' || $value['type'] == 'new') {
            if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                $value['value'] = array_filter($value['value'], function ($value, $key) {
                    return $key != 'offset';
                }, ARRAY_FILTER_USE_BOTH);
            }
            if ($value['type'] == 'delete') {
                $result["- {$value['key']}"] = $value['value'];
            } else {
                $result["+ {$value['key']}"] = $value['value'];
            }
        } elseif ($value['type'] == 'change') {
            $result["+ {$value['key']}"] = $value['new-value'];
            $result["- {$value['key']}"] = $value['old-value'];
        } elseif ($value['type'] == 'array') {
            $arrayValue = toSimpleArray($value['value']);
            $result[$value['key']] = $arrayValue;
        }
    }
    
    return $result;
}
