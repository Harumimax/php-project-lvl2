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
        switch ($value['type']) :
            case "same":
                $result[$value['key']] = $value['value'];
                break;

            case "delete":
                if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                    $value['value'] = deleteOffset($value['value']);
                }
                $result["- {$value['key']}"] = $value['value'];
                break;

            case "new":
                if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                    $value['value'] = deleteOffset($value['value']);
                }
                $result["+ {$value['key']}"] = $value['value'];
                break;

            case "change":
                $result["+ {$value['key']}"] = $value['new-value'];
                $result["- {$value['key']}"] = $value['old-value'];
                break;
            
            case "array":
                $arrayValue = toSimpleArray($value['value']);
                $result[$value['key']] = $arrayValue;
                break;
        endswitch;
    }

    /*
    foreach ($diff as $key => $value) {
        if ($value['type'] == 'same') {
            $result[$value['key']] = $value['value'];
        } elseif ($value['type'] == 'delete') {
            if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                $value['value'] = deleteOffset($value['value']);
            }
            $result["- {$value['key']}"] = $value['value'];
        } elseif ($value['type'] == 'new') {
            if (is_array($value['value']) && array_key_exists('offset', $value['value'])) {
                $value['value'] = deleteOffset($value['value']);
            }
            $result["+ {$value['key']}"] = $value['value'];
        } elseif ($value['type'] == 'change') {
            $result["+ {$value['key']}"] = $value['new-value'];
            $result["- {$value['key']}"] = $value['old-value'];
        } elseif ($value['type'] == 'array') {
            $arrayValue = toSimpleArray($value['value']);
            $result[$value['key']] = $arrayValue;
        }
    }
    */
    
    return $result;
}

function deleteOffset(array $array): array
{
    $valueWithoutOffset = array_filter($array, function ($value, $key) {
        return $key != 'offset';
    }, ARRAY_FILTER_USE_BOTH);

    return $valueWithoutOffset;
}
