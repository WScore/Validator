<?php
declare(strict_types=1);

namespace tests\Result;

use WScore\Validation\Validators\Result;
use WScore\Validation\Validators\ResultList;
use PHPUnit\Framework\TestCase;

class ResultGetRootTest extends TestCase
{
    public function testGetRoot()
    {
        $root = new ResultList([], 'root');
        $list1 = new ResultList([], 'list1');
        $list2 = new ResultList([], 'list2');
        $form1 = new Result('', 'form1');
        $form2 = new Result('', 'form2');
        $root->addResult($list1, 'list1');
        $root->addResult($list2, 'list2');
        $list1->addResult($form1, 'form1');
        $list1->addResult($form2, 'form2');

        $this->assertEquals('list1', $form1->getParent()->name());
        $this->assertEquals('root', $form1->getRoot()->name());

        $this->assertEquals('root', $list1->getParent()->name());
        $this->assertEquals('root', $list1->getRoot()->name());

        $this->assertEquals(null, $root->getParent());
        $this->assertEquals(null, $root->getRoot());
    }
}
