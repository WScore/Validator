<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2019-03-26
 * Time: 17:01
 */

namespace tests\Filters;

use WScore\Validation\Filters\Required;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\Result;

class RequiredTest extends TestCase
{

    public function testRequiredFailed()
    {
        $result = new Result(null, '');
        $required = new Required();
        $required->__invoke($result);
        $this->assertFalse($result->isValid());
    }

    public function testRequiredPass()
    {
        $result = new Result(null, 'value');
        $required = new Required();
        $required->__invoke($result);
        $this->assertTrue($result->isValid());
        $this->assertEquals('value', $result->value());
    }

    public function testRequiredFailedMessage()
    {
        $result = new Result(Messages::create('en'), '');
        $required = new Required();
        $required($result);
        $this->assertEquals('required', $result->getErrorMessage()[0]);
    }
}
