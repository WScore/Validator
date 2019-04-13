<?php

namespace tests\Filters;

use PHPUnit\Framework\TestCase;
use WScore\Validation\Filters\StringLength;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\Result;

class StringLengthTest extends TestCase
{
    private function buildResult($value='tests'): Result
    {
        return new Result($value);
    }

    private function buildStringLength(): StringLength
    {
        return new StringLength(['min' => 3, 'max' => 6]);
    }

    public function test()
    {
        $msg = Messages::create();
        $this->assertEquals(
            'The input is more than 11 characters.',
            $msg->getMessage(StringLength::MAX, ['name' => 'test-me', 'max' => 11]));
    }

    public function testLengthFails()
    {
        $result = $this->buildResult();
        $length = new StringLength(['length' => 8]);
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertFalse($result->isValid());
        $this->assertEquals('The input must be 8 characters.', $result->getErrorMessage()[0]);
    }

    public function testLengthSucceeds()
    {
        $result = $this->buildResult();
        $length = new StringLength(['length' => 5]);
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getErrorMessage());
    }

    public function testMinAndMaxSucceeds()
    {
        $result = $this->buildResult();
        $length = $this->buildStringLength();
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getErrorMessage());
    }

    public function testMaxFails()
    {
        $result = $this->buildResult('1234567');
        $length = $this->buildStringLength();
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertFalse($result->isValid());
        $this->assertEquals('The input is more than 6 characters.', $result->getErrorMessage()[0]);
    }

    public function testMaxSucceeds()
    {
        $result = $this->buildResult('123456');
        $length = $this->buildStringLength();
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertTrue($result->isValid());
    }

    public function testMinFails()
    {
        $result = $this->buildResult('12');
        $length = $this->buildStringLength();
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertFalse($result->isValid());
        $this->assertEquals('The input is less than 3 characters.', $result->getErrorMessage()[0]);
    }

    public function testMinSucceeds()
    {
        $result = $this->buildResult('123');
        $length = $this->buildStringLength();
        $length->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertTrue($result->isValid());
    }
}
