<?php

namespace tests\Filters;

use PHPUnit\Framework\TestCase;
use WScore\Validation\Filters\ValidateFloat;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\Result;

class FilterFloatTest extends TestCase
{
    /**
     * @dataProvider successCaseProvider
     * @param string $value
     * @param float $float
     */
    public function testValidFloatCases($value, $float)
    {
        $input = new Result($value);
        $filter = new ValidateFloat();
        $filter->apply($input);
        $this->assertTrue($input->isValid());
        $this->assertEquals($float, $input->value());
        $this->assertTrue(is_float($input->value()));
    }

    public function successCaseProvider()
    {
        return [
            ['123.0', 123.0],
            ['5e-3', 0.005],
            ['-1.23', -1.23],
            ['0.0', 0.0],
            ['-0.0', 0.0],
        ];
    }

    public function testInvalidFloatString()
    {
        $input = new Result('a12.b3c');
        $filter = new ValidateFloat();
        $filter->apply($input);
        $this->assertFalse($input->isValid());
        $this->assertEquals(null, $input->value());

        $input->finalize(Messages::create());
        $this->assertEquals(['The input is not a valid float. '], $input->getErrorMessage());    }
}
