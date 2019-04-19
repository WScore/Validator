<?php
declare(strict_types=1);

namespace tests\Validation;

use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\ValidatorBuilder;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Validators\ValidationRepeat;

class ValidationMultipleTest extends TestCase
{
    /**
     * @param array $filters
     * @return ValidationInterface|ValidationRepeat
     */
    public function buildMultiple($filters = [])
    {
        $filters += [
            'multiple' => true,
        ];
        $vb = new ValidatorBuilder();
        return $vb->text($filters);
    }

    public function testVerify()
    {
        $list = $this->buildMultiple([
            new AddPostfix('-multi')
        ]);
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->verify($input);

        $this->assertEquals($input, $result->getOriginalValue());
        $this->assertTrue($result->hasChildren());
        $this->assertEquals('test1-multi', $result->getChild('test')->value());
        $this->assertEquals('test2-multi', $result->getChild('more')->value());
    }

    public function testEmptyInput()
    {
        $chain = $this->buildMultiple();
        $result = $chain->verify(['test', null, false]);
        $this->assertTrue($result->isValid());
        $this->assertEquals(['test', '', ''], $result->value());
    }

    public function testRequired()
    {
        $chain = $this->buildMultiple();
        $chain->addFilters([
            new Required(),
        ]);
        $result = $chain->verify(['test', null, false]);
        $this->assertFalse($result->isValid());
        $this->assertEquals(['test', '', ''], $result->value());
    }

    public function testFailedCase()
    {
        $chain = $this->buildMultiple([
            new ValidateInteger(),
        ]);
        $result = $chain->verify([1, 'xxx', 2.0, '1a']);
        $this->assertFalse($result->isValid());
        $this->assertEquals([1, null, 2, null], $result->value());
        $this->assertEquals(['validation failed'], $result->getErrorMessage());
        $this->assertEquals([
            [],
            ['The input is not a valid integer. '],
            [],
            ['The input is not a valid integer. '],
        ], $result->summarizeErrorMessages());

    }
}
