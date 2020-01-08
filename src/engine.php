<?php

namespace Differ\engine;

use function \Differ\parser\parseData;
use function \Differ\makeTree\makeTree;

use function Differ\formatters\toStr\toStr;
use function Differ\formatters\toPlain\toPlain;
use function Differ\formatters\toJson\toJson;

function genDiff($beforeFilePath, $afterFilePath, $format)
{

    if (!(file_exists($beforeFilePath) && file_exists($afterFilePath))) {
        throw new \Exception("one or both files do not exist\n");
        exit;
    }

    $dataFromBeforeFile = file_get_contents($beforeFilePath);
    $typeBeforeFile = pathinfo($beforeFilePath, PATHINFO_EXTENSION);
    $dataFromAfterFile = file_get_contents($afterFilePath);
    $typeAfterFile = pathinfo($afterFilePath, PATHINFO_EXTENSION);

    $tree = makeTree(
        parseData($dataFromBeforeFile, mb_strtolower($typeBeforeFile)),
        parseData($dataFromAfterFile, mb_strtolower($typeAfterFile))
    );

    switch ($format) {
        case "pretty":
                $result = toStr($tree);
            break;

        case "plain":
                $result = toPlain($tree);
            break;

        case "json":
                $result = toJson($tree);
            break;

        default:
            throw new \Exception("Format {$format} not correct\n");
    }

    return $result . "\n";
}
