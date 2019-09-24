<?php

namespace Differ;

use function \Differ\LibDiffer\getDataArray;
use function \Differ\LibDiffer\findDiff;
use function \Differ\LibDiffer\toStr;
use function \Differ\LibDiffer\toPlain;
use function \Differ\LibDiffer\toSimpleArray;

function genDiff($beforeFile, $afterFile, $format)
{
    if (file_exists($beforeFile) && file_exists($afterFile)) {
        $diffOnArray = findDiff(getDataArray($beforeFile), getDataArray($afterFile));
    } else {
        throw new \Exception("one or both files do not exist\n");
        exit;
    }

    switch ($format) :
        case "pretty":
                $result = substr(toStr($diffOnArray), 0, -6) . "\n}" . "\n";
            break;

        case "plain":
                $result = toPlain($diffOnArray) . "\n";
            break;

        case "json":
                $result = json_encode(toSimpleArray($diffOnArray)) . "\n";
            break;

        default:
            throw new \Exception("format not correct\n");
            exit;
    endswitch;

    return $result;
}
