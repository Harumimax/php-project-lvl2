<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function \Differ\engine\genDiff;

class GenDiffTest extends TestCase
{
    private $before;
    private $after;

    public function testGenDiffFormatPrettyFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'pretty';

        $expected = substr(file_get_contents('tests/files/expectedFormatPrettyFileJson'), 0, -1);

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }
  
    public function testGenDiffFormatPrettyYaml()
    {
        $beforeFilePath = 'tests/files/before.yaml';
        $afterFilePath = 'tests/files/after.yaml';
        $format = 'pretty';

        $expected = substr(file_get_contents('tests/files/expectedFormatPrettyFileYaml'), 0, -1);

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }

    public function testGenDiffFormatPlainFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'plain';

        $expected = substr(file_get_contents('tests/files/expectedFormatPlainFileJson'), 0, -1);

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }

    public function testGenDiffFormatJsonFileJson()
    {
        $beforeFilePath = 'tests/files/before.json';
        $afterFilePath = 'tests/files/after.json';
        $format = 'json';

        $expected = substr(file_get_contents('tests/files/expectedFormatJsonFileJson'), 0, -1);

        $this->assertEquals($expected, genDiff($beforeFilePath, $afterFilePath, $format));
    }
}
