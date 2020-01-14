<?php

namespace Differ\formatters\toPlain;

use function Differ\formatters\changeBoolToString\changeBoolToString;

function toPlain($diff, $header = ""): string
{
    $result = array_reduce($diff, function ($acc, $branch) use ($header) {

        $newHeader = (empty($header)) ? $branch['key'] : "{$header}.{$branch['key']}";

        $newValue = chooseAnArrayOrComplexValue($branch['newValue']);
        $oldValue = chooseAnArrayOrComplexValue($branch['oldValue']);

        switch ($branch['type']) {
            case 'deleted':
                $acc[] = "Property '{$newHeader}' was removed";
                break;
            case 'added':
                $acc[] = "Property '{$newHeader}' was added with value: '{$newValue}'";
                break;
            case 'changed':
                $acc[] = "Property '{$newHeader}' was changed. From '{$oldValue}' to '{$newValue}'";
                break;
            case 'nested':
                $acc[] = toPlain($branch['children'], $newHeader);
                break;
        }
        return $acc;
    }, []);

    return implode("\n", $result);
}

function chooseAnArrayOrComplexValue($value)
{
    return is_array($value) ? "complex value" : changeBoolToString($value);
}
