<?php

namespace Differ;

use Funct;

class GenDiff
{
    private $beforeArr;
    private $afterArr;
    private $diff;

    public function __construct($beforeFile, $afterFile)
    {
            $this->beforeArr = json_decode($beforeFile, true);
            $this->afterArr = json_decode($afterFile, true);
    }

    public function makeDiff()
    {
        $this->diff = [];
        $afterArrWithoutBefore = $this->afterArr;
        foreach($this->beforeArr as $key => $value) {
            if (array_key_exists($key, $this->afterArr)) {

                if ($this->beforeArr[$key] == $this->afterArr[$key]) {
                    $this->diff[$key] = $value;
                } else {
                    $this->diff["+"."{$key}"] = $this->afterArr[$key];
                    $this->diff["-"."{$key}"] = $value;
                }
                $afterArrWithoutBefore = \Funct\Collection\without($afterArrWithoutBefore, $this->afterArr[$key]);
            } else {
                $this->diff["-"."{$key}"] = $value;
            }
        }

        foreach($afterArrWithoutBefore as $key => $value){
            $this->diff["+"."{$key}"] = $value;
        }
    }

    public function toStr()
    {
        $resultArr = [];
        foreach($this->diff as $key => $value) {
            if ($key[0] == "-" || $key[0] == "+") {
                $resultArr[] = "   {$key}: {$value}\n";
            } else {
                $resultArr[] = "    {$key}: {$value}\n";
            }
        }
        $resultStr = implode('', $resultArr);

        return "{\n{$resultStr}}\n";
    }

}
