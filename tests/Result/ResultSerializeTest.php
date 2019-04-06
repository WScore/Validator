<?php
namespace tests\Result;

use PHPUnit\Framework\TestCase;
use WScore\Validation\Validators\Result;
use WScore\Validation\Validators\ResultList;

class ResultSerializeTest extends TestCase
{
    private function buildResult($value = 'root', $name = 'root'): ResultList
    {
        return new ResultList($value, $name);
    }

    /**
     * @return ResultList
     */
    private function buildResultWithChildren(): ResultList
    {
        $list = $this->buildResult();
        $list->addResult(new Result('test', 'test'));
        $list->addResult(new Result('more', 'more'));
        return $list;
    }

    public function testSerializeResults()
    {
        $oldRoot = $this->buildResultWithChildren();
        $serialized = serialize($oldRoot);
        /** @var ResultList $newRoot */
        $newRoot = unserialize($serialized);

        $this->assertEquals(ResultList::class, get_class($newRoot));
        $this->assertTrue($newRoot->hasChildren());

        $test = $newRoot->getChild('test');
        $this->assertEquals('test', $test->value());

        $parent = $test->getParent();
        $this->assertEquals(ResultList::class, get_class($parent));
        $this->assertTrue($newRoot === $parent);
    }
}