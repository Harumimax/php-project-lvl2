<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function \Differ\genDiff;

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

}
