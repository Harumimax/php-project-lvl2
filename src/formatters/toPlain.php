<?php

namespace Differ\formatters\toPlain;

function toPlain($diff, $header = ""): string
{

    $result = array_map(function ($value) use ($header) {

        $newHeader = (empty($header)) ? $value['key'] : "{$header}.{$value['key']}";
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
    }, $diff);

    return implode("\n", array_diff($result, array('')));
}
