<?php

namespace tests\Locale;

use PHPUnit\Framework\TestCase;
use WScore\Validation\Filters\Required;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\ValidatorBuilder;

class Test extends TestCase
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
}
