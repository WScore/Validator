<?php
declare(strict_types=1);

namespace tests\Filters;

use WScore\Validator\Filters\RegEx;
use WScore\Validator\Filters\Required;
use PHPUnit\Framework\TestCase;
use WScore\Validator\Locale\Messages;
use WScore\Validator\ValidatorBuilder;
use WScore\Validator\Validators\Result;

class RequiredTest extends TestCase
{
    private function buildResult($value=null)
    {
        return new Result($value);
    }
    public function testRequiredFailed()
    {
        $result = $this->buildResult('');
        $required = new Required();
        $required->apply($result);
        $this->assertFalse($result->isValid());
    }

    public function testRequiredPass()
    {
        $result = $this->buildResult('value');
        $required = new Required();
        $required->apply($result);
        $this->assertTrue($result->isValid());
        $this->assertEquals('value', $result->value());
    }

    public function testRequiredFailedMessage()
    {
        $result = new Result('');
        $required = new Required();
        $required->apply($result);
        $result->finalize(Messages::create('en'));
        $this->assertEquals('The input field is required.', $result->getErrorMessage()[0]);
    }

    public function testWithOutNullable()
    {
        $v = (new ValidatorBuilder())
            ->text([
                RegEx::class => [RegEx::PATTERN => '[0-3]{2}'],
            ]);
        $result = $v->verify('');
        $this->assertFalse($result->isValid());
    }

    public function testNullable()
    {
        $v = (new ValidatorBuilder())
            ->text([
                Required::class => [Required::NULLABLE => true],
                RegEx::class => [RegEx::PATTERN => '[0-3]{2}'],
            ]);
        $result = $v->verify('');
        $this->assertTrue($result->isValid());
    }
}
