<?php

namespace Differ;

use function \Differ\LibDiffer\getDataArray;
use function \Differ\LibDiffer\findDiff;
use function \Differ\LibDiffer\toStr;
use function \Differ\LibDiffer\toPlain;

function genDiff($beforeFile, $afterFile, $format)
{
    switch ($format) {
        case "pretty":
            if (file_exists($beforeFile) && file_exists($afterFile)) {
                $diffOnArray = findDiff(getDataArray($beforeFile), getDataArray($afterFile));
                $result = toStr($diffOnArray) . "\n";
            } else {
                throw new \Exception("one or both files do not exist\n");
                exit;
            }
            break;
        case "plain":
            if (file_exists($beforeFile) && file_exists($afterFile)) {
                $diffOnArray = findDiff(getDataArray($beforeFile), getDataArray($afterFile));
                $result = toPlain($diffOnArray) . "\n";
            } else {
                throw new \Exception("one or both files do not exist\n");
                exit;
            }
            break;
    }

    return $result;
}
