<?php

namespace Differ\engine;

use function \Differ\parser\parseData;
use function \Differ\makeTree\makeTree;

use function Differ\formatters\toStr\toStr;
use function Differ\formatters\toPlain\toPlain;
use function Differ\formatters\toJson\toJson;

function genDiff($beforeFilePath, $afterFilePath, $format)
{


    if (file_exists($beforeFilePath) && file_exists($afterFilePath)) {
        $dataFromBeforeFile = file_get_contents($beforeFilePath);
        $typeBeforeFile = pathinfo($beforeFilePath, PATHINFO_EXTENSION);
        $dataFromAfterFile = file_get_contents($afterFilePath);
        $typeAfterFile = pathinfo($afterFilePath, PATHINFO_EXTENSION);

        $treeOfDiff = makeTree(
            parseData($dataFromBeforeFile, $typeBeforeFile),
            parseData($dataFromAfterFile, $typeAfterFile)
        );
    } else {
        throw new \Exception("one or both files do not exist\n");
    }

    switch ($format) :
        case "pretty":
                $result = toStr($treeOfDiff) . "\n";
            break;

        case "plain":
                $result = toPlain($treeOfDiff) . "\n";
            break;

        case "json":
                $result = toJson($treeOfDiff) . "\n";
            break;

        default:
            throw new \Exception("Format {$format} not correct\n");
    endswitch;

    return $result;
}
