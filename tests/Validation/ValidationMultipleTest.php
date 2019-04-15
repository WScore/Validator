<?php
declare(strict_types=1);

namespace tests\Validation;

use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\ValidateInteger;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\ValidationChain;
use PHPUnit\Framework\TestCase;

class ValidationMultipleTest extends TestCase
{
    /**
     * @param string $locale
     * @return ValidationChain
     */
    public function buildValidationMultiple($locale = 'en')
    {
        $messages = Messages::create($locale);
        $chain = new ValidationChain($messages);
        $chain->setMultiple();

        return $chain;
    }

    public function testVerify()
    {
        $list = $this->buildValidationMultiple();
        $list->addFilters([new AddPostfix('-multi')]);
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->verify($input);

        $this->assertEquals($input, $result->getOriginalValue());
        $this->assertTrue($result->hasChildren());
        $this->assertEquals('test1-multi', $result->getChild('test')->value());
        $this->assertEquals('test2-multi', $result->getChild('more')->value());
    }

    public function testEmptyInput()
    {
        $chain = $this->buildValidationMultiple();
        $result = $chain->verify(['', null, false]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->value());
    }

    public function testRequired()
    {
        $chain = $this->buildValidationMultiple();
        $chain->addFilters([
            new Required(),
        ]);
        $result = $chain->verify(['', null, false]);
        $this->assertFalse($result->isValid());
        $this->assertEquals([], $result->value());
    }

    public function testFailedCase()
    {
        $chain = $this->buildValidationMultiple();
        $chain->addFilters([
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
