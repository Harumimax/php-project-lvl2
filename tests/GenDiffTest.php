<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

Use function \Differ\LibDiffer\getDataArray;
Use function \Differ\LibDiffer\findDiff;
Use function \Differ\LibDiffer\toStr;

class GenDiffTest extends TestCase
{
    private $diff;

    protected function setUp(): void
    {

        $before = '{
            "host": "hexlet.io",
            "timeout": 50,
            "proxy": "123.234.53.22"
          }';

        $after = '{
            "timeout": 20,
            "verbose": true,
            "host": "hexlet.io"
          }';

          $this->diff = new \Differ\GenDiff($before, $after);

    }

    public function testNotEmpty()
    {
        $this->assertFalse(empty($this->diff));
    }

    public function testToStr()
    {

        $this->diff->makeDiff();
        $request = $this->diff->toStr();
        $this->assertStringContainsString("hexlet", $request);
        $this->assertStringContainsString("timeout", $request);
        $this->assertStringContainsString("verbose", $request);
    }
}