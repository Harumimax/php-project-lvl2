<?php

namespace Differ;

use function \Differ\LibDiffer\getDataArray;
use function \Differ\LibDiffer\findDiff;
use function \Differ\LibDiffer\toStr;

function genDiff($beforeFile, $afterFile)
{
    if (file_exists($beforeFile) && file_exists($afterFile)) {
        $diffOnArray = findDiff(getDataArray($beforeFile), getDataArray($afterFile));
        $result = toStr($diffOnArray);
    } else {
        throw new \Exception("one or both files do not exist\n");
        exit;
    }

    return $result;
}
