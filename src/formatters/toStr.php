<?php

namespace Differ\formatters\toStr;

use function Differ\formatters\changeBoolToString\changeBoolToString;

function toStr($diff, $level = 0)
{
    $offset = str_repeat('    ', $level);
    $resultArray = array_reduce($diff, function ($acc, $branch) use ($offset, $level) {

        switch ($branch['type']) {
            case 'same':
                $acc[] = $offset . "    {$branch['key']}: " . prepareString($branch['old-value'], $level + 1);
                break;
            case 'changed':
                $oldValue = $offset . "  - {$branch['key']}: " . prepareString($branch['old-value'], $level + 1);
                $newValue = $offset . "  + {$branch['key']}: " . prepareString($branch['new-value'], $level + 1);
                $acc[] = "{$oldValue}" . "\n" . "{$newValue}";
                break;
            case 'nested':
                $acc[] = $offset . "    {$branch['key']}: " . toStr($branch['children'], $level + 1);
                break;
            case 'deleted':
                $acc[] = $offset . "  - {$branch['key']}: " . prepareString($branch['old-value'], $level + 1);
                break;
            case 'added':
                $acc[] = $offset . "  + {$branch['key']}: " . prepareString($branch['new-value'], $level + 1);
                break;
        }
        return $acc;
    }, []);

    $result = implode("\n", $resultArray);
    return "{" . "\n" . $result . "\n" . $offset .  "}";
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
