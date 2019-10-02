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

        $expected = file_get_contents('tests/files/expected1');

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }

    
    public function testGenDiffFormatPrettyYaml()
    {
        $beforeFilePath = 'tests/files/before.yaml';
        $afterFilePath = 'tests/files/after.yaml';
        $format = 'pretty';

        $expected = file_get_contents('tests/files/expected2');

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }

    
    public function testGenDiffFormatPlainFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'plain';

        $expected = file_get_contents('tests/files/expected3');

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }

    public function testGenDiffFormatJsonFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'json';

        $expected = file_get_contents('tests/files/expected4');

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }
}
