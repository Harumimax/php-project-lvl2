<?php

namespace Differ\formatters\toPlain;

use function Differ\formatters\changeBoolToString\changeBoolToString;

function toPlain($diff, $header = ""): string
{
    $result = array_map(function ($value) use ($header) {
        $newHeader = (empty($header)) ? $value['key'] : "{$header}.{$value['key']}";

        $newValue = (is_array($value['new-value'])) ? "complex value" : changeBoolToString($value['new-value']);
        $oldValue = (is_array($value['old-value'])) ? "complex value" : changeBoolToString($value['old-value']);

        if ($value['type'] == 'deleted') {
            return "Property '{$newHeader}' was removed";
        } elseif ($value['type'] == 'added') {
            return "Property '{$newHeader}' was added with value: '{$newValue}'";
        } elseif ($value['type'] == 'changed') {
            return "Property '{$newHeader}' was changed. From '{$oldValue}' to '{$newValue}'";
        } elseif ($value['type'] == 'nested') {
            return toPlain($value['children'], $newHeader);
        }
    }, $diff);

    return implode("\n", array_diff($result, array('')));
}
