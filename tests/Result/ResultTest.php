<?php
declare(strict_types=1);

namespace tests\Result;

use WScore\Validation\Validators\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    private function buildResult($value = 'test', $name = 'name')
    {
        return new Result(null, $value, $name);
    }

    public function testGetIterator()
    {
        $result = $this->buildResult();
        $index = 10;
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($result as $item) {
            $index++;
        }
        $this->assertEquals(10, $index);
    }

    public function testGetChild()
    {
        $result = $this->buildResult();
        $this->assertEquals(null, $result->getChild('none'));
    }

    public function testGetChildren()
    {
        $result = $this->buildResult();
        $this->assertEquals([], $result->getChildren());
    }

    public function testGetOriginalValue()
    {
        $result = $this->buildResult('test-me', 'test');
        $this->assertEquals('test-me', $result->value());
        $result->setValue('test-more');
        $this->assertEquals('test-more', $result->value());
        $this->assertEquals('test-me', $result->getOriginalValue());
    }

    public function testName()
    {
        $result = $this->buildResult();
        $this->assertEquals('name', $result->name());
    }

    public function testIsValid()
    {
        $result = $this->buildResult();
        $this->assertTrue($result->isValid());
        $result->failed('test');
        $this->assertFalse($result->isValid());
    }

    public function testHasChild()
    {
        $result = $this->buildResult();
        $this->assertFalse($result->hasChild('no'));
    }

    public function testValue()
    {
        $result = $this->buildResult();
        $this->assertEquals('test', $result->value());
    }

    public function testSetValue()
    {
        $result = $this->buildResult();
        $result->setValue('test-me');
        $this->assertEquals('test-me', $result->value());
    }

    public function testHasChildren()
    {
        $result = $this->buildResult();
        $this->assertFalse($result->hasChildren());
    }

    public function testGetErrorMessage()
    {
        $result = $this->buildResult();
        $this->assertEquals([], $result->getErrorMessage());
        $result->failed('test1', [], 'failed-message1');
        $result->failed('test2', [], 'failed-message2');
        $this->assertEquals(['failed-message1', 'failed-message2'], $result->getErrorMessage());
    }
}
