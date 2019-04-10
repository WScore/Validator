<?php
declare(strict_types=1);

namespace tests\Result;

use WScore\Validation\Validators\Result;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Validators\ResultList;

class ResultListTest extends TestCase
{
    private function buildResult($value = 'root', $name = 'root'): ResultList
    {
        return new ResultList($value, $name);
    }

    private function buildResultWithChildren(): ResultList
    {
        $list = $this->buildResult();
        $list->addResult(new Result('test', 'test'));
        $list->addResult(new Result('more', 'more'));
        return $list;
    }

    public function testGetIterator()
    {
        $result = $this->buildResultWithChildren();
        $index = 10;
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($result as $item) {
            $index++;
        }
        $this->assertEquals(12, $index);
    }

    public function testGetChild()
    {
        $result = $this->buildResultWithChildren();
        $this->assertEquals(Result::class, get_class($result->getChild('test')));
        $this->assertEquals('test', $result->getChild('test')->name());
    }

    public function testGetChildren()
    {
        $result = $this->buildResultWithChildren();
        $this->assertTrue(array_key_exists('test', $result->getChildren()));
        $this->assertTrue(array_key_exists('more', $result->getChildren()));
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
        $this->assertEquals('root', $result->name());
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
        $result = $this->buildResultWithChildren();
        $this->assertTrue($result->hasChild('test'));
    }

    public function testValue()
    {
        $result = $this->buildResult();
        $this->assertEquals('root', $result->value());
    }

    public function testSetValue()
    {
        $result = $this->buildResult();
        $result->setValue('test-me');
        $this->assertEquals('test-me', $result->value());
    }

    public function testHasChildren()
    {
        $result = $this->buildResultWithChildren();
        $this->assertTrue($result->hasChildren());
    }

    public function testGetErrorMessage()
    {
        $result = $this->buildResultWithChildren();
        $this->assertEquals([], $result->getErrorMessage());
        $result->failed('root1', [], 'failed-message1');
        $result->failed('root2', [], 'failed-message2');
        $result->finalize();
        $this->assertEquals(['failed-message1', 'failed-message2'], $result->getErrorMessage());

        $result->getChild('test')->failed('test', [], 'test-failed');
        $result->getChild('more')->failed('more', [], 'more-failed');
        $this->assertEquals(['failed-message1', 'failed-message2'], $result->getErrorMessage());

        $result->finalize();
        $this->assertEquals([
            'failed-message1',
            'failed-message2',
        ], $result->getErrorMessage());
        $this->assertEquals([
            'test' => ['test-failed'],
            'more' => ['more-failed'],
        ], $result->summarizeErrorMessages());
    }
}
