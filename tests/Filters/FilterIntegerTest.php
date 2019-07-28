<?php

namespace tests\Filters;

use PHPUnit\Framework\TestCase;
use WScore\Validator\Filters\ValidateInteger;
use WScore\Validator\Locale\Messages;
use WScore\Validator\Validators\Result;

class FilterIntegerTest extends TestCase
{
    /**
     * @dataProvider successCaseProvider
     * @param string $value
     * @param int $integer
     */
    public function testValidFloatCases($value, $integer)
    {
        $input = new Result($value);
        $filter = new ValidateInteger();
        $filter->apply($input);
        $this->assertTrue($input->isValid());
        $this->assertEquals($integer, $input->value());
        $this->assertTrue(is_int($input->value()));
    }

    public function successCaseProvider()
    {
        return [
            ['123.0', 123],
            ['5e-3', 0],
            ['-1.23', -1],
            ['0.0', 0],
            ['-0.0', 0],
        ];
    }

    public function testIntegerAsInput()
    {
        $input = new Result('123');
        $filter = new ValidateInteger();
        $filter->apply($input);
        $this->assertTrue($input->isValid());
        $this->assertEquals(123, $input->value());
        $this->assertTrue(is_integer($input->value()));
    }

    public function testInvalidString()
    {
        $input = new Result('a12b3c');
        $filter = new ValidateInteger();
        $filter->apply($input);
        $this->assertFalse($input->isValid());
        $this->assertEquals(null, $input->value());

        $input->finalize(Messages::create());
        $this->assertEquals(['The input is not a valid integer. '], $input->getErrorMessage());
    }

    public function testFloatIsConvertedToInteger()
    {
        $input = new Result('1.2');
        $filter = new ValidateInteger();
        $filter->apply($input);
        $this->assertTrue($input->isValid());
        $this->assertEquals(1, $input->value());
        $this->assertTrue(is_integer($input->value()));
    }

    public function testInvalidUtf8()
    {
        $input = new Result(mb_convert_encoding('1２．３', 'SJIS', 'UTF-8'));
        $filter = new ValidateInteger();
        $filter->apply($input);
        $this->assertFalse($input->isValid());

    }
}
