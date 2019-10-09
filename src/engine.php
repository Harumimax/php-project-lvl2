<?php

namespace Differ\engine;

use function \Differ\functions\getDataArray;
use function \Differ\functions\findDiff;

use function Differ\formatters\toStr\toStr;
use function Differ\formatters\toPlain\toPlain;
use function Differ\formatters\toJson\toJson;

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
                $result = toStr($diffOnArray) . "\n";
            break;

        case "plain":
                $result = toPlain($diffOnArray) . "\n";
            break;

        case "json":
                $result = toJson($diffOnArray) . "\n";
            break;

        default:
            throw new \Exception("format not correct\n");
            exit;
    endswitch;

    return $result;
}
