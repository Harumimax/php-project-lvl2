<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

Use function \Differ\LibDiffer\getDataArray;
Use function \Differ\LibDiffer\findDiff;
Use function \Differ\LibDiffer\toStr;

class GenDiffTest extends TestCase
{
    //private $diff;
    private $path;
    //private $path2;

/*
    protected function setUp(): void
    {
        if(file_exists($this->path)){
            rmdir($this->path);
        }

        $temp = sys_get_temp_dir();
        $this->path1 = $temp . DIRECTORY_SEPARATOR . "file1";
        $this->path2 = $temp . DIRECTORY_SEPARATOR . "file2";

        $dataBefore = '{
            "host": "hexlet.io",
            "timeout": 50,
            "proxy": "123.234.53.22"
          }';

        if (is_writable($this->path1)) {
            $handle = fopen($this->path1, "ab");
            if ($handle) {
                try {
                    fwrite($handle, $dataBefore);
                } finally {
                    fclose($handle);
                }
            }
        }
        
        $dataAfter = '{
            "timeout": 20,
            "verbose": true,
            "host": "hexlet.io"
          }';

        if (is_writable($this->path2)) {
            $handle = fopen($this->path2, "ab");
            if ($handle) {
                try {
                    fwrite($handle, $dataAfter);
                } finally {
                    fclose($handle);
                }
            }
        }

    }
*/

    public function testGetDataArray()
    {
        if(file_exists($this->path)){
            rmdir($this->path);
        }
        $temp = sys_get_temp_dir();
        $this->path = $temp . DIRECTORY_SEPARATOR . "file1.json";
        $dataBefore = '{
            "host": "hexlet.io",
            "timeout": 50,
            "proxy": "123.234.53.22"
          }';
        
        file_put_contents($this->path, $dataBefore);

        $request = getDataArray($this->path);
        $this->assertIsArray($request);
        $this->assertFalse(empty($request));

    }

    protected function tearDown(): void 
    {
        if(file_exists($this->path)){
            unlink($this->path);
        }
    }

    public function testFindDiff()
    {
        $dataBefore = ['timeout' => 50, 'host' => 'hexlet.io'];
        $dataAfter = ['timeout' => 50, 'host' => 'new.host'];
        $request = findDiff($dataBefore, $dataAfter);

        $this->assertIsArray($request);

        $this->assertEquals(3, sizeof($request));
        $this->assertTrue(array_key_exists('timeout', $request));
        $this->assertTrue(array_key_exists('-host', $request));
        $this->assertTrue(array_key_exists('+host', $request));
        $this->assertEquals('timeout', array_search('50', $request));
    }

    public function testNotEmpty()
    {
        $data = [
            'host' => 'hexlet.io',
            '+timeout' => 20,
            '-timeout' => 50,
            '-proxy' => '123.234.53.22',
            '+verbose' => 1];
        
        $this->assertFalse(empty(toStr($data)));
    }

    public function testToStr()
    {
        $data = [
        'host' => 'hexlet.io',
        '+timeout' => 20,
        '-timeout' => 50,
        '-proxy' => '123.234.53.22',
        '+verbose' => 1];
        
        $request = toStr($data);
            
        $this->assertIsString($request);
        
        $this->assertStringContainsString("hexlet", $request);
        $this->assertStringContainsString("timeout", $request);
        $this->assertStringContainsString("verbose", $request);

    }
}