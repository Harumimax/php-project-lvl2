<?php

namespace Differ\formatters\toPretty;

use function Differ\formatters\changeBoolToString\changeBoolToString;

function toPretty($diff)
{
    $iter = function ($diff, $level) use (&$iter) {
        $offset = str_repeat('    ', $level);
        $resultArray = array_reduce($diff, function ($acc, $branch) use ($offset, $level, $iter) {

            switch ($branch['type']) {
                case 'same':
                    $acc[] = $offset . "    {$branch['key']}: " . prepareString($branch['oldValue'], $level + 1);
                    break;
                case 'changed':
                    $oldValue = $offset . "  - {$branch['key']}: " . prepareString($branch['oldValue'], $level + 1);
                    $newValue = $offset . "  + {$branch['key']}: " . prepareString($branch['newValue'], $level + 1);
                    $acc[] = "{$oldValue}" . "\n" . "{$newValue}";
                    break;
                case 'nested':
                    $acc[] = $offset . "    {$branch['key']}: " . $iter($branch['children'], $level + 1);
                    break;
                case 'deleted':
                    $acc[] = $offset . "  - {$branch['key']}: " . prepareString($branch['oldValue'], $level + 1);
                    break;
                case 'added':
                    $acc[] = $offset . "  + {$branch['key']}: " . prepareString($branch['newValue'], $level + 1);
                    break;
            }
            return $acc;
        }, []);
        $result = implode("\n", $resultArray);

        return "{" . "\n" . $result . "\n" . $offset .  "}";
    };

    return $iter($diff, 0);
}

function prepareString($value, $level)
{
    if (!is_array($value)) {
        return changeBoolToString($value);
    }

    $offsetForMap = str_repeat('    ', $level + 1);
    $arrayValueForStr = array_map(function ($key) use ($value, $offsetForMap) {
        $value = changeBoolToString($value);
        return "{$offsetForMap}{$key}: {$value[$key]}";
    }, array_keys($value));

    $offset = str_repeat('    ', $level);
    return "{" . "\n" . implode("\n", array_diff($arrayValueForStr, array(''))) . "\n" . "{$offset}" . "}";
}
