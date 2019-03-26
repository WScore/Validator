<?php
/**
 * Created by PhpStorm.
 * User: wsjp
 * Date: 2019/03/25
 * Time: 21:00
 */

use PHPUnit\Framework\TestCase;
use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Validators\Result;
use WScore\Validation\Validators\ValidationChain;

class ValidationChainTest extends TestCase
{
    /**
     * @param string $locale
     * @return ValidationChain
     */
    public function buildValidationChain($locale = 'en')
    {
        $messages = \WScore\Validation\Locale\Messages::create($locale);
        $chain = new ValidationChain($messages);

        return $chain;
    }

    public function testConstruction()
    {
        $chain = $this->buildValidationChain();
        $this->assertEquals(ValidationChain::class,get_class($chain));
    }

    public function testInitialize()
    {
        $chain = $this->buildValidationChain();
        //$chain->addFilters(new AddPostfix());
        $result = $chain->initialize('test-me');
        $this->assertEquals(Result::class, get_class($result));
        $this->assertEquals('test-me', $result->value());
    }

    public function testValidate()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters(new AddPostfix('-testInitialize'));
        $result = $chain->initialize('test-me');
        $this->assertEquals('test-me', $result->value());

        $result = $chain->validate($result);
        $this->assertEquals(Result::class, get_class($result));
        $this->assertEquals('test-me-testInitialize', $result->value());
    }

    public function testVerify()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters(new AddPostfix('-verified'));
        $result = $chain->verify('test-verify');

        $this->assertEquals(Result::class, get_class($result));
        $this->assertEquals('test-verify-verified', $result->value());
    }
}
