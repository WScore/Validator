<?php
declare(strict_types=1);

namespace tests\Filters;

use WScore\Validation\Filters\Required;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\Result;

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
}
