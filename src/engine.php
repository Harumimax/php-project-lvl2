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


    
    /* findDiff
    $result = [];
    foreach($beforeArr as $key => $value) {
        if (array_key_exists($key, $afterArr)) {

            if ($beforeArr[$key] == $afterArr[$key]) {
                $result[$key] = $value;
            } else {
                $result["+"."{$key}"] = $afterArr[$key];
                $result["-"."{$key}"] = $value;
            }
            $afterArr = \Funct\Collection\without($afterArr, $afterArr[$key]);
        } else {
            $result["-"."{$key}"] = $value;
        }
    }
    foreach($afterArr as $key => $value){
        $result["+"."{$key}"] = $value;
    }
    print_r($result);
    */
    
    /* toStr
    foreach($result as $key => $value) {
        if ($key[0] == "-" || $key[0] == "+") {
            $resultArr[] = "   {$key}: {$value}\n";
        } else {
            $resultArr[] = "    {$key}: {$value}\n";
        }
    }

    $resultStr = implode('', $resultArr);
    $str = "{\n{$resultStr}}\n";
    print_r($str);
    */
    
    //print_r(json_encode($result));
    
    //print_r("\n{\n   text: text\n   text: text\n   text: text\n}");
    
    //print_r(genDiff($argv));
