<?php

namespace tests\Locale;

use PHPUnit\Framework\TestCase;
use WScore\Validator\Filters\Required;
use WScore\Validator\Interfaces\ValidationInterface;
use WScore\Validator\ValidatorBuilder;

class EmailTypeTest extends TestCase
{
    private function buildEmail($locale = 'en'): ValidationInterface
    {
        $b = new ValidatorBuilder($locale);
        return $b->email();
    }

    public function testNullEmail()
    {
        $v = $this->buildEmail();
        $result = $v->verify('');
        $this->assertTrue($result->isValid());
    }

    public function testRequiredEmail()
    {
        $v = $this->buildEmail();
        $v->addFilters([new Required()]);
        $result = $v->verify('');
        $this->assertFalse($result->isValid());
    }

    public function testNullEmailJa()
    {
        $v = $this->buildEmail('ja');
        $result = $v->verify('');
        $this->assertTrue($result->isValid());
    }

    public function testRequiredEmailJa()
    {
        $v = $this->buildEmail('ja');
        $v->addFilters([new Required()]);
        $result = $v->verify('');
        $this->assertFalse($result->isValid());
    }
}
