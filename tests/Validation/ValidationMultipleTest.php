<?php
declare(strict_types=1);

namespace tests\Validation;

use tests\Validation\Filters\AddPostfix;
use WScore\Validator\Filters\Required;
use WScore\Validator\Filters\ValidateInteger;
use WScore\Validator\Interfaces\ValidationInterface;
use WScore\Validator\ValidatorBuilder;
use PHPUnit\Framework\TestCase;
use WScore\Validator\Validators\ValidationMultiple;
use WScore\Validator\Validators\ValidationRepeat;

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
        $result = $chain->verify([]);
        $this->assertFalse($result->isValid());
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

    public function testMultipleFilter()
    {
       $vb = new ValidatorBuilder();
       /** @var ValidationMultiple $chain */
       $chain = $vb->chain([
            'multiple' => [
                Required::class,
            ],
        ]);
        $result = $chain->verify([]);
        $this->assertFalse($result->isValid());

        $this->assertFalse($chain->getFilters()->has(Required::class));
        $this->assertTrue($chain->getPostFilters()->has(Required::class));
    }
}
