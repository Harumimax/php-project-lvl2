<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function \Differ\genDiff;
use function \Differ\LibDiffer\getDataArray;
use function \Differ\LibDiffer\findDiff;
use function \Differ\LibDiffer\toStr;
use function \Differ\LibDiffer\toPlain;
use function \Differ\LibDiffer\toSimpleArray;

class GenDiffTest extends TestCase
{
    private $before;
    private $after;

    public function testGenDiffFormatPrettyFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'pretty';
        $request = genDiff($beforeFilePath, $afterFilePath, $format);

        $this->assertIsString($request);
        $this->assertFalse(empty($request));

        $this->assertStringContainsString("common", $request);
        $this->assertStringContainsString("setting4", $request);
        $this->assertStringContainsString("100500", $request);
    }

    
    public function testGenDiffFormatPrettyYaml()
    {
        $beforeFilePath = 'tests/files/before.yaml';
        $afterFilePath = 'tests/files/after.yaml';
        $format = 'pretty';
        $request = genDiff($beforeFilePath, $afterFilePath, $format);

        $this->assertIsString($request);
        $this->assertFalse(empty($request));

        $this->assertStringContainsString("timeout", $request);
        $this->assertStringContainsString("host", $request);
        $this->assertStringContainsString("hexlet.io", $request);
        $this->assertStringContainsString("proxy", $request);
    }

    
    public function testGenDiffFormatPlainFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'plain';
        $request = genDiff($beforeFilePath, $afterFilePath, $format);

        $this->assertIsString($request);
        $this->assertFalse(empty($request));

        $this->assertStringContainsString("Property 'common.setting6' was removed", $request);
        $this->assertStringContainsString("Property 'common.setting5' was added with value: 'complex value'", $request);
        $this->assertStringContainsString("Property 'group3' was added with value: 'complex value'", $request);
        $this->assertStringNotContainsString("setting1", $request);
    }

    public function testGenDiffFormatJsonFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'json';
        $request = genDiff($beforeFilePath, $afterFilePath, $format);

        $this->assertIsString($request);
        $this->assertFalse(empty($request));

        $this->assertStringContainsString('{"key":"value"}', $request);
        $this->assertStringContainsString('"group1":{"+ baz":"bars","- baz":"bas","foo":"bar"}', $request);
        $this->assertStringContainsString('"+ group3":{"foo":"bar","fee":"100500"}', $request);
    }


    // --------------------------------- old tests ---------------------------

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
        $this->assertFalse(array_key_exists('timeout', $request));
        $this->assertFalse(in_array('host', $request));
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

    public function testToSimpleArray()
    {
        $data = findDiff($this->before, $this->after);
        $request = toSimpleArray($data);
            
        $this->assertIsArray($request);
        $this->assertEquals(3, sizeof($request));
        $this->assertTrue(array_key_exists('timeout', $request));

        $result = json_encode($request);
        $this->assertStringContainsString("hexlet.io", $result);
        $this->assertStringContainsString("new.host", $result);
        $this->assertStringContainsString("host", $result);
    }
}
