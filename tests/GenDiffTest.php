<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

Use function \Differ\LibDiffer\getDataArray;
Use function \Differ\LibDiffer\findDiff;
Use function \Differ\LibDiffer\toStr;
Use function \Differ\LibDiffer\toPlain;

class GenDiffTest extends TestCase
{
    private $path;

    private $before;
    private $after;

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

    protected function setUp(): void
    {
        $this->before = ['timeout' => 50, 'host' => 'hexlet.io'];
        $this->after = ['timeout' => 50, 'host' => 'new.host'];
    }


    public function testFindDiff()
    {

        $request = findDiff($this->before, $this->after);

        $this->assertIsArray($request);

        $this->assertEquals(2, sizeof($request));
        $this->assertTrue(array_key_exists('timeout', $request));
        $this->assertTrue(array_key_exists('host', $request));
    }


    public function testNotEmpty()
    {
        $request = findDiff($this->before, $this->after);
        
        $this->assertFalse(empty(toStr($request)));
    }

    public function testToStr()
    {
        $data = findDiff($this->before, $this->after);
        $request = toStr($data);
            
        $this->assertIsString($request);
        $this->assertStringContainsString("hexlet.io", $request);
        $this->assertStringContainsString("timeout", $request);
        $this->assertStringContainsString("host", $request);
    }

    public function testToPlain()
    {
        $data = findDiff($this->before, $this->after);
        $request = toPlain($data);
            
        $this->assertIsString($request);
        $this->assertStringContainsString("hexlet.io", $request);
        $this->assertStringContainsString("new.host", $request);
        $this->assertStringContainsString("host", $request);
        $this->assertFalse(strpos($request, "timeout"));
    }
}